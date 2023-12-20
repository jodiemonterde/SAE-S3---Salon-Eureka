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

function getEntreprisesPerField($pdo, $field_ids) {
    // Utilisez la fonction implode pour convertir le tableau en une chaîne séparée par des virgules
    $field_ids_str = implode(', ', $field_ids);
    
    $stmt = $pdo->prepare("SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector
                           FROM Company
                           JOIN Speaker
                           ON Company.company_id = Speaker.company_id
                           JOIN AssignmentSpeaker
                           ON AssignmentSpeaker.speaker_id = Speaker.speaker_id
                           WHERE field_id IN ($field_ids_str)");
    $stmt->execute();
    return $stmt;
}

function getStudentsPerCompany($pdo, $company_id) {
    $stmt = $pdo->prepare("SELECT Field.name, User.username
                           FROM Company
                           JOIN Speaker
                           ON Company.company_id = Speaker.company_id
                           JOIN Appointment
                           ON Speaker.speaker_id = Appointment.speaker_id
                           JOIN User
                           ON Appointment.user_id = User.user_id
                           JOIN AssignmentUser
                           ON User.user_id = AssignmentUser.user_id
                           JOIN Field
                           ON AssignmentUser.field_id = Field.field_id
                           WHERE Company.company_id = $company_id;");
    $stmt->execute();
    return $stmt;
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
                                    date = DATE(:dateForum),
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

function getIntervenant($pdo, $company_id){
    $requetes=$pdo->prepare("SELECT Speaker.name, Field.name 
                             FROM `Speaker` 
                             JOIN AssignmentSpeaker 
                             ON Speaker.speaker_id = AssignmentSpeaker.speaker_id 
                             JOIN Field 
                             ON AssignmentSpeaker.field_id = Field.field_id 
                             WHERE company_id = :company_id");
    $requetes->bindParam(':company_id', $company_id);
    $requetes->execute();
    return $requetes;
}

function getFields($pdo) {
    $sql = "SELECT * FROM `Field`";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt;
}

?>