<?php
/**
 * Database initialization script.
 * Usage (from project root):
 * php Index/database/init.php
 */

require_once __DIR__ . '/../config.php';

function loadSqlStatements($filePath) {
    if (!file_exists($filePath)) {
        throw new RuntimeException("Schema file not found: {$filePath}");
    }

    $sql = file_get_contents($filePath);
    if ($sql === false) {
        throw new RuntimeException("Failed to read schema file: {$filePath}");
    }

    $lines = preg_split('/\R/', $sql);
    $cleaned = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || strpos($trimmed, '--') === 0) {
            continue;
        }
        $cleaned[] = $line;
    }

    $joined = implode("\n", $cleaned);
    $statements = [];
    $buffer = '';

    $inSingle = false;
    $inDouble = false;
    $length = strlen($joined);

    for ($i = 0; $i < $length; $i++) {
        $ch = $joined[$i];
        $prev = $i > 0 ? $joined[$i - 1] : '';

        if ($ch === "'" && !$inDouble && $prev !== '\\\\') {
            $inSingle = !$inSingle;
        } elseif ($ch === '"' && !$inSingle && $prev !== '\\\\') {
            $inDouble = !$inDouble;
        }

        if ($ch === ';' && !$inSingle && !$inDouble) {
            $stmt = trim($buffer);
            if ($stmt !== '') {
                $statements[] = $stmt;
            }
            $buffer = '';
            continue;
        }

        $buffer .= $ch;
    }

    $tail = trim($buffer);
    if ($tail !== '') {
        $statements[] = $tail;
    }

    return $statements;
}

$schemaPath = __DIR__ . '/schema.sql';
$tasksSchemaPath = __DIR__ . '/tasks_schema.sql';

try {
    $statements = loadSqlStatements($schemaPath);
    $tasksStatements = [];
    
    if (file_exists($tasksSchemaPath)) {
        $tasksStatements = loadSqlStatements($tasksSchemaPath);
    }
    
    $allStatements = array_merge($statements, $tasksStatements);

    if (extension_loaded('pdo_mysql')) {
        $rootDsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
        $dbDsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

        $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $rootPdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET ' . DB_CHARSET . ' COLLATE ' . DB_CHARSET . '_unicode_ci');
        echo "Database ensured: " . DB_NAME . PHP_EOL;

        $pdo = new PDO($dbDsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Disable foreign key checks before executing schema
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        $executed = 0;
        foreach ($allStatements as $statement) {
            $pdo->exec($statement);
            $executed++;
        }

        // Re-enable foreign key checks after schema execution
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    } elseif (extension_loaded('mysqli')) {
        $rootMysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
        if ($rootMysqli->connect_error) {
            throw new RuntimeException('MySQL connection failed: ' . $rootMysqli->connect_error);
        }

        $charsetOk = $rootMysqli->set_charset(DB_CHARSET);
        if (!$charsetOk) {
            throw new RuntimeException('Failed to set charset: ' . $rootMysqli->error);
        }

        $createDbSql = 'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET ' . DB_CHARSET . ' COLLATE ' . DB_CHARSET . '_unicode_ci';
        if (!$rootMysqli->query($createDbSql)) {
            throw new RuntimeException('Failed to create database: ' . $rootMysqli->error);
        }
        $rootMysqli->close();

        echo "Database ensured: " . DB_NAME . PHP_EOL;

        $dbMysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($dbMysqli->connect_error) {
            throw new RuntimeException('Database connection failed: ' . $dbMysqli->connect_error);
        }

        if (!$dbMysqli->set_charset(DB_CHARSET)) {
            throw new RuntimeException('Failed to set DB charset: ' . $dbMysqli->error);
        }

        // Disable foreign key checks before executing schema
        if (!$dbMysqli->query('SET FOREIGN_KEY_CHECKS = 0')) {
            throw new RuntimeException('Failed to disable foreign key checks: ' . $dbMysqli->error);
        }

        $executed = 0;
        foreach ($allStatements as $statement) {
            if (!$dbMysqli->query($statement)) {
                throw new RuntimeException('SQL error: ' . $dbMysqli->error . ' | Statement: ' . $statement);
            }
            $executed++;
        }

        // Re-enable foreign key checks after schema execution
        if (!$dbMysqli->query('SET FOREIGN_KEY_CHECKS = 1')) {
            throw new RuntimeException('Failed to enable foreign key checks: ' . $dbMysqli->error);
        }

        $dbMysqli->close();
    } else {
        throw new RuntimeException('No MySQL driver available. Enable pdo_mysql or mysqli in PHP CLI.');
    }

    echo "Schema applied successfully. Statements executed: {$executed}" . PHP_EOL;
    echo "Done." . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, "Database initialization failed: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
