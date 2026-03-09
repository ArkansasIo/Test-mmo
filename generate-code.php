<?php
/**
 * Code Generator - Auto-creates missing game logic classes
 * Run: php generate-code.php
 */

$basePath = __DIR__;
$classesDir = "$basePath/Index/classes";
$indexDir = "$basePath/Index";

$generated = 0;

echo "\n=== Code Generator ===\n\n";

// Classes to generate
$classes = [
    'Defense.php' => 'Defense',
    'Building.php' => 'Building',
    'Resource.php' => 'Resource',
    'Market.php' => 'Market',
    'Validator.php' => 'Validator',
    'Logger.php' => 'Logger',
];

foreach ($classes as $filename => $className) {
    $filePath = "$classesDir/$filename";
    
    if (file_exists($filePath)) {
        echo "⊘ $filename (exists)\n";
        continue;
    }
    
    $code = generateClass($className);
    file_put_contents($filePath, $code);
    echo "✓ $filename\n";
    $generated++;
}

// Generate helpers.php
$helpersPath = "$indexDir/helpers.php";
if (!file_exists($helpersPath)) {
    $code = generateHelpers();
    file_put_contents($helpersPath, $code);
    echo "✓ helpers.php\n";
    $generated++;
} else {
    echo "⊘ helpers.php (exists)\n";
}

echo "\nGenerated: $generated files\n\n";

function generateClass($name) {
    $classes = [
        'Defense' => '<?php
class Defense {
    private $db;
    private $playerId;
    private $planetId;
    
    public function __construct($playerId, $planetId = null) {
        $this->db = Database::getInstance();
        $this->playerId = $playerId;
        $this->planetId = $planetId;
    }
    
    public function getDefenses() {
        return $this->db->fetch("SELECT * FROM defenses WHERE player_id = ? AND planet_id = ?", [$this->playerId, $this->planetId]);
    }
    
    public function build($type, $qty) {
        try {
            $this->db->insert("defenses", ["player_id" => $this->playerId, "planet_id" => $this->planetId, "type" => $type, "quantity" => $qty, "created_at" => time()]);
            return ["success" => true];
        } catch (Exception $e) {
            return ["success" => false];
        }
    }
}',
        
        'Building' => '<?php
class Building {
    private $db;
    private $playerId;
    private $planetId;
    
    public function __construct($playerId, $planetId = null) {
        $this->db = Database::getInstance();
        $this->playerId = $playerId;
        $this->planetId = $planetId;
    }
    
    public function getBuildings() {
        return $this->db->fetch("SELECT * FROM buildings WHERE planet_id = ?", [$this->planetId]);
    }
    
    public function buildStructure($type, $level = 1) {
        try {
            $this->db->insert("buildings", ["planet_id" => $this->planetId, "type" => $type, "level" => $level, "completed_at" => time() + (3600 * $level)]);
            return ["success" => true];
        } catch (Exception $e) {
            return ["success" => false];
        }
    }
}',
        
        'Resource' => '<?php
class Resource {
    private $db;
    private $playerId;
    
    public function __construct($playerId) {
        $this->db = Database::getInstance();
        $this->playerId = $playerId;
    }
    
    public function getResources() {
        return $this->db->fetchOne("SELECT metal, crystal, deuterium FROM players WHERE id = ?", [$this->playerId]);
    }
    
    public function add($metal = 0, $crystal = 0, $deuterium = 0) {
        $current = $this->getResources();
        $this->db->update("players", ["metal" => $current["metal"] + $metal, "crystal" => $current["crystal"] + $crystal, "deuterium" => $current["deuterium"] + $deuterium], "id = ?", [$this->playerId]);
    }
}',
        
        'Market' => '<?php
class Market {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getListings() {
        return $this->db->fetch("SELECT * FROM market_listings WHERE active = 1 LIMIT 100");
    }
    
    public function createListing($playerId, $resource, $qty, $price) {
        try {
            $this->db->insert("market_listings", ["player_id" => $playerId, "resource_type" => $resource, "quantity" => $qty, "price_per_unit" => $price, "active" => 1, "created_at" => time()]);
            return ["success" => true];
        } catch (Exception $e) {
            return ["success" => false];
        }
    }
}',
        
        'Validator' => '<?php
class Validator {
    public static function sanitizeUsername($u) {
        return preg_replace("/[^a-zA-Z0-9_]/", "", $u);
    }
    
    public static function isValidEmail($e) {
        return filter_var($e, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function isStrongPassword($p) {
        return strlen($p) >= 8 && preg_match("/[A-Z]/", $p) && preg_match("/[a-z]/", $p) && preg_match("/[0-9]/", $p);
    }
    
    public static function validateCoordinates($x, $y, $z) {
        return is_numeric($x) && is_numeric($y) && is_numeric($z) && $x > 0 && $y > 0 && $z > 0;
    }
}',
        
        'Logger' => '<?php
class Logger {
    private static $file = null;
    
    public static function init($f) {
        self::$file = $f;
    }
    
    public static function log($level, $msg, $ctx = []) {
        if (!self::$file) return;
        $ts = date("Y-m-d H:i:s");
        $c = !empty($ctx) ? " | " . json_encode($ctx) : "";
        $e = "[$ts] [$level] $msg$c" . PHP_EOL;
        file_put_contents(self::$file, $e, FILE_APPEND);
    }
}',
    ];
    
    return $classes[$name] ?? '';
}

function generateHelpers() {
    return '<?php
function calculateDistance($from, $to) {
    $dx = $from["x"] - $to["x"];
    $dy = $from["y"] - $to["y"];
    $dz = $from["z"] - $to["z"];
    return sqrt($dx*$dx + $dy*$dy + $dz*$dz);
}

function calculateTravelTime($dist, $speed, $bonus = 1.0) {
    return (int)(($dist / $speed) * 3600 / $bonus);
}

function formatGameTime($ts) {
    $r = $ts - time();
    if ($r <= 0) return "Done";
    return sprintf("%02d:%02d:%02d", (int)($r / 3600), (int)(($r % 3600) / 60), $r % 60);
}

function validateCoordinates($x, $y, $z) {
    return is_numeric($x) && is_numeric($y) && is_numeric($z) && $x > 0 && $y > 0 && $z > 0;
}
';
}
