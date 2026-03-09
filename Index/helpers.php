<?php
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
