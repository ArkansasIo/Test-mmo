<?php
/**
 * Database Class
 * Handles all database connections and queries
 */

class Database {
    private static $instance = null;
    private $connection;
    private $driver;
    
    private function __construct() {
        try {
            if (extension_loaded('pdo_mysql')) {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $this->connection = new PDO($dsn, DB_USER, DB_PASS);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->driver = 'pdo';
                return;
            }

            if (extension_loaded('mysqli')) {
                $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if ($mysqli->connect_error) {
                    throw new RuntimeException($mysqli->connect_error);
                }

                if (!$mysqli->set_charset(DB_CHARSET)) {
                    throw new RuntimeException($mysqli->error);
                }

                $this->connection = $mysqli;
                $this->driver = 'mysqli';
                return;
            }

            throw new RuntimeException('No supported MySQL driver found (pdo_mysql or mysqli).');
        } catch (Throwable $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            if ($this->driver === 'pdo') {
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($params);
                return $stmt;
            }

            $normalized = $this->normalizeSqlAndParams($sql, $params);
            $sql = $normalized['sql'];
            $bindParams = $normalized['params'];

            $stmt = $this->connection->prepare($sql);
            if (!$stmt) {
                throw new RuntimeException($this->connection->error);
            }

            if (!empty($bindParams)) {
                $types = '';
                $values = [];
                foreach ($bindParams as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                    $values[] = $param;
                }

                $bindArgs = array_merge([$types], $values);
                $refs = [];
                foreach ($bindArgs as $k => $v) {
                    $refs[$k] = &$bindArgs[$k];
                }
                call_user_func_array([$stmt, 'bind_param'], $refs);
            }

            if (!$stmt->execute()) {
                throw new RuntimeException($stmt->error);
            }

            return $stmt;
        } catch (Throwable $e) {
            error_log("Database query error: " . $e->getMessage());
            return false;
        }
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if (!$stmt) {
            return [];
        }

        if ($this->driver === 'pdo') {
            return $stmt->fetchAll();
        }

        $result = $stmt->get_result();
        if ($result === false) {
            return [];
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if (!$stmt) {
            return null;
        }

        if ($this->driver === 'pdo') {
            return $stmt->fetch();
        }

        $result = $stmt->get_result();
        if ($result === false) {
            return null;
        }

        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }
    
    public function insert($table, $data) {
        $keys = array_keys($data);
        $fields = implode(', ', array_map(fn($k) => "`$k`", $keys));
        $placeholders = ':' . implode(', :', $keys);
        
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
        $stmt = $this->query($sql, $data);

        if (!$stmt) {
            return false;
        }

        if ($this->driver === 'pdo') {
            return $this->connection->lastInsertId();
        }

        $id = $this->connection->insert_id;
        $stmt->close();
        return $id;
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $sets = [];
        foreach (array_keys($data) as $key) {
            $sets[] = "`$key` = :$key";
        }
        $setString = implode(', ', $sets);
        
        $sql = "UPDATE $table SET $setString WHERE $where";
        $params = array_merge($data, $whereParams);
        
        return $this->query($sql, $params);
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params);
    }
    
    public function beginTransaction() {
        if ($this->driver === 'pdo') {
            return $this->connection->beginTransaction();
        }

        return $this->connection->begin_transaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        if ($this->driver === 'pdo') {
            return $this->connection->rollBack();
        }

        return $this->connection->rollback();
    }
    
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->query($sql, $params);
            if (!$stmt) {
                return false;
            }
            
            if ($this->driver === 'pdo') {
                return $stmt->rowCount();
            }
            
            $rowCount = $stmt->affected_rows;
            $stmt->close();
            return $rowCount;
        } catch (Throwable $e) {
            error_log("Database execute error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getLastInsertId() {
        if ($this->driver === 'pdo') {
            return $this->connection->lastInsertId();
        }
        return $this->connection->insert_id;
    }

    private function normalizeSqlAndParams($sql, $params) {
        if (empty($params)) {
            return ['sql' => $sql, 'params' => []];
        }

        $isAssoc = array_keys($params) !== range(0, count($params) - 1);
        if (!$isAssoc) {
            return ['sql' => $sql, 'params' => array_values($params)];
        }

        $ordered = [];
        $normalizedSql = preg_replace_callback('/:([a-zA-Z_][a-zA-Z0-9_]*)/', function ($matches) use ($params, &$ordered) {
            $key = $matches[1];
            $ordered[] = array_key_exists($key, $params) ? $params[$key] : null;
            return '?';
        }, $sql);

        return ['sql' => $normalizedSql, 'params' => $ordered];
    }
}
