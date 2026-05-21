<?php
// SAVE USER MATCH HISTORY
$matched_person = $bestMatchName ?? 'Unknown';
$match_distance = $bestDistance ?? 0;
$match_status = ($bestMatchName !== 'Unknown') ? 'Matched' : 'Not Matched';

$stmt = $conn->prepare("
    INSERT INTO match_history 
    (username, matched_person, match_distance, match_status) 
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("ssds", $_SESSION['username'], $matched_person, $match_distance, $match_status);
$stmt->execute();
?>
