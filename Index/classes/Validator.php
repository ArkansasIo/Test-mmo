<?php
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
}