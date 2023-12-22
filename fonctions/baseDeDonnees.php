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
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        return $pdo;
    }
    function verifUtilisateur($pdo, $motDepasse, $identifiant){
        try{ 
			$connecte=false;
			$maRequete = $pdo->prepare("SELECT user_id, username, password from User where email = :leLogin and password = :lePWD");
			$maRequete->bindParam(':leLogin', $identifiant);
			$maRequete->bindParam(':lePWD', $motDepasse);
			if ($maRequete->execute()) {
				$maRequete->setFetchMode(PDO::FETCH_OBJ);
				while ($ligne=$maRequete->fetch()) {				
					$connecte=true;
				}
			}
			return $connecte;
		} catch ( Exception $e ) {
			echo "Connection failed: " . $e->getMessage();
			return false;
		} 
    }

    function infoUtilisateur($pdo, $motDepasse, $identifiant){
        try{ 
			$maRequete = $pdo->prepare("SELECT user_id, responsibility, username from User where email = :leLogin and password = :lePWD");
			$maRequete->bindParam(':leLogin', $identifiant);
			$maRequete->bindParam(':lePWD', $motDepasse);
			$maRequete->execute();
			return $maRequete;
		}
		catch ( Exception $e ) {
			echo "Connection failed: " . $e->getMessage();
			return false;
		} 
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

    function getPhase($pdo){
        try{ 
            $maRequete = $pdo->prepare("SELECT phase, wish_period_end from Meeting");
            $maRequete->execute();
            $ligne = $maRequete->fetch();
            $phase = $ligne['phase'];
            $phase = $phase == 1 && $ligne['wish_period_end'] < date("Y-m-d") ? 1.5 : $phase;
            return $phase;
		} catch ( Exception $e ) {
			echo "Connection failed: " . $e->getMessage();
			return false;
		} 
    }

	function getEntreprisesForStudent($pdo, $user_id, $recherche) {
        $sql = ("SELECT DISTINCT Company.company_id, Company.name, Company.logo_file_name, Company.description, Company.address, Company.sector, WishList.company_id as wish
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

    function getEntreprisesPerStudent($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT Company.company_id,name,logo_file_name,address,sector
                            FROM Company
                            JOIN WishList ON Company.company_id = WishList.company_id
                            WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    function getFields($pdo) {
        $sql = "SELECT * FROM `Field`";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    function getInfoStudents($pdo, $recherche, $field_ids) {
        $field_ids_str = implode(', ', $field_ids);
    
        if ($field_ids_str == null) {
            return null;
        }
    
        $sql = "SELECT u.username, f.name AS filiere, COUNT(w.company_id) AS nbSouhait, u.user_id
                FROM User u
                JOIN AssignmentUser au
                ON u.user_id = au.user_id
                JOIN Field f
                ON au.field_id = f.field_id
                LEFT JOIN WishList w
                ON u.user_id = w.user_id
                WHERE u.responsibility = 'E'
                AND f.field_id IN ($field_ids_str)
                GROUP BY u.username, filiere";
    
        if ($recherche != null) {
            $sql .= " HAVING u.username LIKE :recherche";
        }
    
        $sql .= " ORDER BY u.username";
    
        $stmt = $pdo->prepare($sql);
    
        if ($recherche != null) {
            $stmt->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
        }
    
        $stmt->execute();
        return $stmt;
    }
    
    function removeWishStudent($pdo, $user_id, $company_id) {
        $stmt = $pdo->prepare("DELETE FROM WishList
                            WHERE user_id = :user_id AND company_id = :company_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':company_id', $company_id);
        return $stmt->execute();
    }

    function planningPerUser($pdo, $user_id) {
        $requete = $pdo-> prepare("SELECT ap.start, ap.duration, c.name
                                FROM Appointment ap
                                JOIN User u on ap.user_id = u.user_id
                                JOIN Speaker s on ap.speaker_id = s.speaker_id
                                JOIN Company c on s.company_id = c.company_id
                                WHERE u.user_id = :user_id");
        $requete->bindParam(':user_id', $user_id);
        $requete->execute();
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
                                AND w.user_id = :user_id");
        $requete->bindParam(':user_id', $user_id);
        $requete->execute();
        return $requete;
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

    function getEntreprisesPhase2($pdo, $field_ids, $recherche) {
        // Utilisez la fonction implode pour convertir le tableau en une chaîne séparée par des virgules
        $field_ids_str = implode(', ', $field_ids);

        if ($field_ids_str == null) {
            return null;
        }

        // Requête SQL de base
        $sql = "SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector, Company.excluded
                FROM Company
                JOIN Speaker ON Company.company_id = Speaker.company_id
                JOIN AssignmentSpeaker ON AssignmentSpeaker.speaker_id = Speaker.speaker_id
                WHERE field_id IN ($field_ids_str)";

        // Ajoutez la condition de recherche à la requête si elle est fournie
        if ($recherche != null) {
            $sql .= " AND Company.name LIKE :recherche";
        }

        // Ajout de l'ordre de tri à la requête
        $sql .= " ORDER BY Company.name";

        // Préparation de la requête
        $stmt = $pdo->prepare($sql);

        // Si une recherche est fournie, liez le paramètre
        if ($recherche != null) {
            $stmt->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
        }

        // Exécution de la requête
        $stmt->execute();

        // Retourne le résultat de la requête
        return $stmt;
    }

    function getStudentsPerCompanyWishList($pdo, $company_id) {
        $stmt = $pdo->prepare("SELECT Field.name, User.username
                            FROM Company
                            JOIN WishList
                            ON Company.company_id = WishList.company_id
                            JOIN User
                            ON WishList.user_id = User.user_id
                            JOIN AssignmentUser
                            ON User.user_id = AssignmentUser.user_id
                            JOIN Field
                            ON AssignmentUser.field_id = Field.field_id
                            WHERE Company.company_id = :company_id");
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        return $stmt;
    }

    function getStudentsAppointmentsPerCompany($pdo, $company_id, $isExcluded) {
        if ($isExcluded == 1) {
            return getStudentsPerCompanyWishList($pdo, $company_id);
        } else {
            $stmt = $pdo->prepare("SELECT Field.name, User.username, TIME_FORMAT(Appointment.start, '%H:%i') as start, TIME_FORMAT(ADDTIME(Appointment.start, Appointment.duration), '%H:%i') as duration
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
                                WHERE Company.company_id = :company_id");
            $stmt->bindParam(':company_id', $company_id);
            $stmt->execute();
            return $stmt;
        }
    }

    function getEntreprises($pdo, $field_ids, $recherche) {
        // Utilisez la fonction implode pour convertir le tableau en une chaîne séparée par des virgules
        $field_ids_str = implode(', ', $field_ids);

        if ($field_ids_str == null) {
            return null;
        }

        // Requête SQL de base
        $sql = "SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector
                FROM Company
                JOIN Speaker ON Company.company_id = Speaker.company_id
                JOIN AssignmentSpeaker ON AssignmentSpeaker.speaker_id = Speaker.speaker_id
                WHERE field_id IN ($field_ids_str)";

        // Ajoutez la condition de recherche à la requête si elle est fournie
        if ($recherche != null) {
            $sql .= " AND Company.name LIKE :recherche";
        }

        // Ajout de l'ordre de tri à la requête
        $sql .= " ORDER BY Company.name";

        // Préparation de la requête
        $stmt = $pdo->prepare($sql);

        // Si une recherche est fournie, liez le paramètre
        if ($recherche != null) {
            $stmt->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
        }

        // Exécution de la requête
        $stmt->execute();

        // Retourne le résultat de la requête
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

    function search($query) {
        $query = htmlspecialchars($query);
        $stmt = $pdo->prepare("SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector
                            FROM Company
                            WHERE (Company.name LIKE '%".$query."%')
                            ORDER BY Company.name");
        $stmt->execute();
        return $stmt;
    }
?>