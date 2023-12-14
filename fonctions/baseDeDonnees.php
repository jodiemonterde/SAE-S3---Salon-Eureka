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

function getEntreprisesPerStudent($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT Company.company_id,name,logo,address,sector
                           FROM Company
                           JOIN WishList ON Company.company_id = WishList.company_id
                           WHERE user_id = $user_id");
    $stmt->execute();
    return $stmt;
}

function removeWishStudent($pdo, $user_id, $company_id) {
    $stmt = $pdo->prepare("DELETE FROM WishList
                           WHERE user_id = $user_id AND company_id = $company_id");
    return $stmt->execute();
}

?>