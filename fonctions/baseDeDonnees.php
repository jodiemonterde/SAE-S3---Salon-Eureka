<?php
function connecteBD() {
    // Database configuration
    $host = 'mysql-sae-nmms.alwaysdata.net';
    $dbName = 'sae-nmms_eureka';
    $username = 'sae-nmms';
    $password = 'NicolMonterdeMiquelSchardt';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    return $pdo;
}

function planningPerUser($pdo, $user_id) {
    $requete = $pdo-> prepare("SELECT ap.start, ap.duration, c.name
                               FROM Appointment ap
                               JOIN User u on ap.user_id = u.user_id
                               JOIN Speaker s on ap.speaker_id = s.speaker_id
                               JOIN Company c on s.company_id = c.company_id
                               WHERE u.user_id = ?");
    $requete->execute([$user_id]);
    $planning = [];
    $i = 0;
    while ($ligne = $requete->fetch()) {
        $planning[$i]['start'] = substr($ligne['start'], 0, 5);
        $planning[$i]['company_name'] = $ligne['name'];
        list($hours, $minutes, $seconds) = sscanf($ligne['duration'], '%d:%d:%d');
        $interval = new DateInterval(sprintf('PT%dH%dM%dS', $hours, $minutes, $seconds));
        $planning[$i]['end'] = (date_add(new DateTime($ligne['start']), $interval))->format('H:i');
        $i++;
    }
    return $planning;
}

function unlistedCompanyPerUser($pdo, $user_id) {
    $requete = $pdo-> prepare("SELECT c.name 
                               FROM Company c
                               JOIN WishList w on c.company_id = w.company_id
                               WHERE c.excluded = 1
                               AND w.user_id = ?");
    $requete->execute([$user_id]);
    return $requete;
}

?>