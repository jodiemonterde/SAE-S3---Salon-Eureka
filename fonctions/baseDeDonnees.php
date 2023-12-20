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

function getEntreprisesForStudent($pdo, $user_id, $recherche) {
    $sql = ("SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector, WishList.company_id as wish
            FROM Company
            JOIN Speaker
            ON Company.company_id = Speaker.company_id
            JOIN AssignmentSpeaker
            ON AssignmentSpeaker.speaker_id = Speaker.speaker_id
            JOIN AssignmentUser
            ON AssignmentUser.field_id = AssignmentSpeaker.field_id
            LEFT JOIN WishList
            ON Company.company_id = WishList.company_id
            WHERE AssignmentUser.user_id = :user_id");
    if ($recherche != null) {
        $sql.= " AND Company.name LIKE :recherche";
    }
    $stmt = $pdo->prepare($sql);
    
    if ($recherche != null) {
        $stmt->bindValue(':recherche', '%' . $recherche . '%');
    }
    $stmt->bindParam(':user_id', $user_id);
    
    $stmt->execute();
    return $stmt;
}

function deleteWishStudent($pdo, $user_id, $company_id) {
    $stmt = $pdo->prepare("DELETE IGNORE FROM WishList
                           WHERE user_id = :user_id 
                           AND company_id = :company_id");
    $stmt->bindParam(':user_id', $user_id);                  
    $stmt->bindParam(':company_id', $company_id);
    return $stmt->execute();
}

function addWishStudent($pdo, $user_id, $company_id) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO WishList (user_id, company_id)
                          VALUES (:user_id, :company_id)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':company_id', $company_id);
    return $stmt->execute();
}

?>