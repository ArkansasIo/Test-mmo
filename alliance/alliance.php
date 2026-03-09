<?php
// alliance.php

// Include necessary files and initialize database connection
include_once '../includes/db.php'; // Adjust the path as needed
include_once '../includes/functions.php'; // Adjust the path as needed

// Function to create a new alliance
function createAlliance($name, $leaderId) {
    global $db;
    $stmt = $db->prepare("INSERT INTO alliances (name, leader_id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $leaderId);
    if ($stmt->execute()) {
        return $db->insert_id;
    } else {
        return false;
    }
}

// Function to get alliance details
function getAlliance($allianceId) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM alliances WHERE id = ?");
    $stmt->bind_param("i", $allianceId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to manage alliance members
function manageAllianceMembers($allianceId, $action, $memberId) {
    global $db;
    if ($action == 'add') {
        $stmt = $db->prepare("INSERT INTO alliance_members (alliance_id, member_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $allianceId, $memberId);
    } elseif ($action == 'remove') {
        $stmt = $db->prepare("DELETE FROM alliance_members WHERE alliance_id = ? AND member_id = ?");
        $stmt->bind_param("ii", $allianceId, $memberId);
    }
    return $stmt->execute();
}

// Function to disband an alliance
function disbandAlliance($allianceId) {
    global $db;
    $stmt = $db->prepare("DELETE FROM alliances WHERE id = ?");
    $stmt->bind_param("i", $allianceId);
    return $stmt->execute();
}

// Example usage
// $newAllianceId = createAlliance("Galactic Empire", 1);
// $allianceDetails = getAlliance($newAllianceId);
// manageAllianceMembers($newAllianceId, 'add', 2);
// disbandAlliance($newAllianceId);

?>
