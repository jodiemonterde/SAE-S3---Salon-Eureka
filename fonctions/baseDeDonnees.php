<?php

function connecteBD() {
    // Database configuration
    $host = 'mysql-sae-nmms.alwaysdata.net';
    $dbName = 'sae-nmms_eureka';
    $username = 'sae-nmms';
    $password = 'NicolMonterdeMiquelSchardt';

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        
        // Set PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // You can now use the $pdo object to perform database operations
        
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    return $pdo;
}

function getEntreprisesForStudent($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT DISTINCT Company.name, Company.description, Company.address, Company.sector
                           FROM Company
                           JOIN Speaker
                           ON Company.company_id = Speaker.company_id
                           JOIN AssignmentSpeaker
                           ON AssignmentSpeaker.speaker_id = Speaker.speaker_id
                           JOIN AssignmentUser
                           ON AssignmentUser.field_id = AssignmentSpeaker.field_id
                           WHERE user_id = $user_id;");
    $stmt->execute();
    return $stmt;
}

?>