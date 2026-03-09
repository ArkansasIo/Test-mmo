<?php
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
}