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

function infoForum($pdo){
    $maRequete=$pdo->prepare('SELECT date,start,end, primary_appointment_duration, secondary_appointment_duration, wish_period_end FROM Meeting');
    $maRequete->execute();
    return $maRequete;
}

function updateForum($pdo,$dateForum,$debut,$fin,$dureePrincipal,$dureeSecondaire,$jourFin){
    try{
        $pdo->beginTransaction();
        $maRequete=$pdo->prepare("UPDATE Meeting 
                                  SET 
                                    date = :dateForum,
                                    start = :debut,
                                    end = :fin,
                                    primary_appointment_duration = :dureePrincipal, 
                                    secondary_appointment_duration = :dureeSecondaire,
                                    wish_period_end = :jourFin
                                  WHERE meeting_id = 1");
        $maRequete->bindParam(':dateForum', $dateForum);
        $maRequete->bindParam(':debut', $debut);
        $maRequete->bindParam(':fin', $fin);
        $maRequete->bindParam(':dureePrincipal', $dureePrincipal);
        $maRequete->bindParam(':dureeSecondaire', $dureeSecondaire);
        $maRequete->bindParam(':jourFin', $jourFin);
        $maRequete->execute();
        $pdo->commit();
    }catch(Exception $e){
        $pdo->rollBack();
    }

}

?>