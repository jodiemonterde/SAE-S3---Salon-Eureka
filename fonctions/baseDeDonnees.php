<?php
    function connecteBD() {
        // Database configuration
        $host = 'mysql-sae-nmms.alwaysdata.net';
        $dbName = 'sae-nmms_eureka';
        $username = 'sae-nmms';
        $password = 'NicolMonterdeMiquelSchardt';
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        
        // Set PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
                AND AssignmentUser.user_id = WishList.user_id
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

    function isPlanningGenerated($pdo) {
        $stmt = $pdo->prepare("SELECT generated FROM Meeting WHERE meeting_id = 1");
        $stmt->execute();
        $ligne = $stmt->fetch();
        return $ligne['generated'] === 1;
    }

    function genererPlanning($pdo) {
        $stmt = $pdo->prepare("SELECT generatePlanning()");
        $stmt->execute();
        return $stmt->fetch()[0];
    }

    function launchPhase2($pdo) {
        $stmt = $pdo->prepare("UPDATE Meeting SET phase = 2 WHERE meeting_id = 1");
        $stmt->execute();
    }

    function cancelPlanning($pdo) {
        $stmt = $pdo->prepare("DELETE FROM Appointment;");
        $stmt->execute();
        $stmt = $pdo->prepare("UPDATE Meeting SET generated = 0 WHERE meeting_id = 1;");
        $stmt->execute();
    }

    function setPlanningGenerated($pdo, $value) {
        $stmt = $pdo->prepare("UPDATE Meeting SET generated = :value WHERE meeting_id = 1");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    }

    function addNewStudent($pdo, $prenom, $nom, $email, $mdp, $filiere) {
        $nom = htmlspecialchars($nom);
        $prenom = htmlspecialchars($prenom);
        $email = htmlspecialchars($email);
        $mdp = htmlspecialchars($mdp);
        $filiere = htmlspecialchars($filiere);

        $username = $prenom.' '.$nom;
        $stmt = $pdo->prepare("INSERT INTO User (username, password, responsibility, email)
                            VALUES (:username, :password, 'E', :email)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $mdp);
        $stmt->bindParam(':email', $email);
        $stmt->execute(); 

        // Récupérer l'ID généré automatiquement
        $user_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO AssignmentUser (field_id, user_id)
                            VALUES (:field_id, :user_id)");
        $stmt->bindParam(':field_id', $filiere);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    function addNewSupervisor($pdo, $prenom, $nom, $email, $mdp, $filiere) {
        $nom = htmlspecialchars($nom);
        $prenom = htmlspecialchars($prenom);
        $email = htmlspecialchars($email);
        $mdp = htmlspecialchars($mdp);

        $username = $prenom.' '.$nom;
        $stmt = $pdo->prepare("INSERT INTO User (username, password, responsibility, email)
                            VALUES (:username, :password, 'G', :email)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $mdp);
        $stmt->bindParam(':email', $email);
        $stmt->execute(); 

        // Récupérer l'ID généré automatiquement
        $user_id = $pdo->lastInsertId();

        foreach($filiere as $value) {
        $stmt = $pdo->prepare("INSERT INTO AssignmentUser (field_id, user_id)
                            VALUES (:field_id, :user_id)");
        $stmt->bindParam(':field_id', $value);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        }
        
    }

    function deleteStudent($pdo, $user_id) {
        $user_id = htmlspecialchars($user_id);

        
        $stmt = $pdo->prepare("DELETE 
                            FROM WishList
                            WHERE WishList.user_id = :user_id;");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $stmt = $pdo->prepare("DELETE 
                            FROM Appointment
                            WHERE Appointment.user_id = :user_id;");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $stmt = $pdo->prepare("DELETE 
                            FROM AssignmentUser
                            WHERE AssignmentUser.user_id = :user_id;");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute(); 

        $stmt = $pdo->prepare("DELETE 
                            FROM User
                            WHERE User.user_id = :user_id;");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    function modifyCompany($pdo, $company_id, $nom, $description, $secteur, $adresse, $codePostal, $ville, $logo) {

        if (!empty($logo['name'])) {
            $targetDirectory = "../../../../ressources/logosentreprises/";
            $imageFileType = strtolower(pathinfo($logo["name"], PATHINFO_EXTENSION));
    
            // Générer un nom de fichier unique basé sur le nom de l'entreprise
            // Supression des caractères non autorisés
            $cleanedFileName = preg_replace('/[^\w\d\-_.]/', '', $nom);
        
            // Limiter la longueur du nom de fichier si nécessaire
            $cleanedFileName = substr($cleanedFileName, 0, 80);
            $newFileName = $cleanedFileName . '_' . uniqid() . '.' . $imageFileType;
            $targetFile = $targetDirectory . $newFileName;
            var_dump($targetFile);
            $uploadOk = 1;
        
            // Vérifier si le fichier est une image réelle
            $check = getimagesize($logo["tmp_name"]);
            if ($check === false) {
                echo "Le fichier n'est pas une image.";
                $uploadOk = 0;
            }
        
            // Vérifier si le fichier existe déjà
            if (file_exists($targetFile)) {
                echo "Désolé, le fichier existe déjà.";
                $uploadOk = 0;
            }
    
            // Autoriser certains formats de fichiers
            $allowedFormats = array("jpg", "jpeg", "png", "gif");
            if (!in_array($imageFileType, $allowedFormats)) {
                echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
                $uploadOk = 0;
            }
        
            // Vérifier si $uploadOk est défini à 0 par une erreur
            if ($uploadOk == 0) {
                echo "Désolé, votre fichier n'a pas été téléchargé.";
            } else {
                // Si tout est correct, essayez de télécharger le fichier
                if (move_uploaded_file($_FILES["logoEntreprise"]["tmp_name"], $targetFile)) {
                    echo "Le fichier " . htmlspecialchars(basename($_FILES["logoEntreprise"]["name"])) . " a été téléchargé.";
        
                    // Maintenant, vous pouvez utiliser $targetFile comme chemin à stocker dans la base de données
                    $pathToLogo = $targetFile;
        
                } else {
                    echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
                }
            }
        } else {
            $newFileName = null;
        }
    
        $nom = empty($nom) ? null : htmlspecialchars($nom);
        $description = empty($description) ? null : htmlspecialchars($description);
        $adresse = empty($adresse) ? null : htmlspecialchars($adresse . ', ' . $codePostal . ' ' . $ville);
        $secteur = empty($secteur) ? null : htmlspecialchars($secteur);    
        
        $stmt = $pdo->prepare("UPDATE Company
                               SET name = IFNULL(:nom, name), logo_file_name = IFNULL(:newFileName, logo_file_name), description = IFNULL(:description, description), address = IFNULL(:adresse, address), sector = IFNULL(:secteur, sector)
                               WHERE Company.company_id = :id");
    
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':newFileName', $newFileName);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':secteur', $secteur);
        $stmt->bindParam(':id', $company_id);
    
        return $stmt->execute();
    
      
    }

    function deleteSupervisor($pdo, $user_id) {
        $user_id = htmlspecialchars($user_id);

        $stmt = $pdo->prepare("DELETE 
                            FROM AssignmentUser
                            WHERE AssignmentUser.user_id = :user_id;");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute(); 

        $stmt = $pdo->prepare("DELETE 
                            FROM User
                            WHERE User.user_id = :user_id;");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
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

    function getFieldsPerUsers($pdo, $user_id) {
        $sql = "SELECT * FROM `Field` WHERE field_id IN (SELECT field_id FROM AssignmentUser WHERE user_id = :user_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
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
                AND f.field_id IN (:fields)
                GROUP BY u.username, filiere";
    
        if ($recherche != null) {
            $sql .= " HAVING u.username LIKE :recherche";
        }
    
        $sql .= " ORDER BY u.username";
    
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fields', $field_ids_str);
        if ($recherche != null) {
            $stmt->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
        }
    
        $stmt->execute();
        return $stmt;
    }

    function setSpecificationCompany($pdo, $action, $comp_id) {
        switch ($action) {
            case 'ajouterEntrepriseReduite' :
                $sql = "UPDATE Company SET useSecondary = 1 WHERE company_id = :comp_id;";
                break;
            case 'retirerEntrepriseReduite' :
                $sql = "UPDATE Company SET useSecondary = 0 WHERE company_id = :comp_id;";
                break;
            case 'ajouterEntrepriseExclusion' :
                $sql = "UPDATE Company SET excluded = 1 WHERE company_id = :comp_id;";
                break;
            case 'retirerEntrepriseExclusion' :
                $sql = "UPDATE Company SET excluded = 0 WHERE company_id = :comp_id;";
                break;
            default :
                return null;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':comp_id', $comp_id);
        $stmt->execute();
    }

    function getSpecificationCompany($pdo, $specification, $value) {
        switch ($specification) {
            case 'entrepriseReduite' :
                $sql = "SELECT company_id, name FROM Company WHERE useSecondary = :value;";
                break;
            case 'entrepriseExclusion' :
                $sql = "SELECT company_id, name FROM Company WHERE excluded = :value;";
                break;
            default :
                return null;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':value', $value);
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

    function getInfoStudentsSort($pdo, $recherche, $field_ids, $sort) {
        $field_ids_str = implode(', ', $field_ids);

        $sort = htmlspecialchars($sort);

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

        if ($sort == "default" || $sort == "alpha") { 
            $sql .= " ORDER BY u.username";
        } else if ($sort == "croissant") {
            $sql .= " ORDER BY nbSouhait";
        } else if ($sort == "decroissant") {
            $sql .= " ORDER BY nbSouhait DESC";
        }
        

        $stmt = $pdo->prepare($sql);

        if ($recherche != null) {
            $stmt->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt;
    }

    function getInfosSupervisors($pdo, $recherche, $field_ids) {

        $parameters = $field_ids;

        if (!is_array($parameters)) {
            $parameters = [$field_ids];
        }

        if ($field_ids == null || count($field_ids) == 0) {
            return null;
        }

        $parametersAsQuestionMarks = implode(',', array_fill(0, count($parameters), '?'));
        
        $sql = "SELECT u.username, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ', ') AS filieres, u.user_id
                FROM User u
                JOIN AssignmentUser au 
                ON u.user_id = au.user_id
                JOIN Field f 
                ON au.field_id = f.field_id
                JOIN (
                    SELECT DISTINCT u.user_id
                    FROM User u
                    JOIN AssignmentUser au 
                    ON u.user_id = au.user_id
                    JOIN Field f 
                    ON au.field_id = f.field_id
                    WHERE u.responsibility = 'G'
                    AND f.field_id IN ($parametersAsQuestionMarks)
                ) AS filtered_users 
                ON u.user_id = filtered_users.user_id
                GROUP BY u.username, u.user_id";

        if ($recherche != null) {
            $sql .= " HAVING u.username LIKE ?";
        }

        $sql .= " ORDER BY u.username";

        $stmt = $pdo->prepare($sql);

        if ($recherche != null) {
            array_push($parameters,'%' . $recherche . '%');
        }
        $stmt->execute($parameters);
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

    function getEntreprisesPhase2($pdo, $field_ids, $recherche) {
        // Utilisez la fonction implode pour convertir le tableau en une chaîne séparée par des virgules
        $field_ids_str = implode(', ', $field_ids);

        if ($field_ids_str == null) {
            return null;
        }

        // Requête SQL de base
        $sql = "SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector, Company.excluded, Company.logo_file_name
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

    function getSpeakersPerCompany($pdo, $company_id) {
        $stmt = $pdo->prepare("SELECT speaker_id, name, role FROM Speaker WHERE company_id = :company_id");
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        return $stmt;
    }

    function getSpeakersPerCompanyAdministrateur($tableau_intervenants) {
        // Séparer la chaîne principale en utilisant ';' comme délimiteur pour obtenir les intervenants
        $intervenants_array = explode(';', $tableau_intervenants);
            
        // Initialiser un tableau pour stocker les données finales
        $final_array = array();
    
        // Parcourir chaque intervenant
        foreach ($intervenants_array as $intervenant) {
            // Exploser les différents éléments de l'intervenant en utilisant ',' comme délimiteur
            $intervenant_elements = explode(',', $intervenant);
    
            // Vérifier si les éléments attendus existent avant d'y accéder
            $nom = isset($intervenant_elements[0]) ? $intervenant_elements[0] : '';
            $fonction = isset($intervenant_elements[1]) ? $intervenant_elements[1] : '';
            $id = isset($intervenant_elements[3]) ? $intervenant_elements[3] : '';
    
            // Exploser les filières (fields) de l'intervenant en utilisant '/' comme délimiteur
            $fields_array = isset($intervenant_elements[2]) ? explode('/', $intervenant_elements[2]) : array();
    
            // Ajouter les éléments au tableau final
            $final_array[] = array(
                'id' => $id,
                'nom' => $nom,
                'fonction' => $fonction,
                'fields' => $fields_array
            );
        }
    
        // Utiliser $final_array comme nécessaire
        return $final_array;
    }

    function deleteCompany($pdo, $company_id) {
        $stmt = $pdo->prepare("DELETE FROM AssignmentSpeaker
                               WHERE speaker_id IN (SELECT speaker_id
                                                    FROM Speaker
                                                    WHERE company_id = :id)");
        $stmt->bindParam(':id', $company_id);
        $stmt->execute();
        
        $stmt = $pdo->prepare("DELETE FROM Speaker
                               WHERE company_id = :id");
        $stmt->bindParam(':id', $company_id);
        $stmt->execute();
    
    
        $stmt = $pdo->prepare("DELETE FROM Company
                               WHERE company_id = :id");
        $stmt->bindParam(':id', $company_id);
        $stmt->execute();
    }

    function addSpeaker($pdo, $company_id, $name, $role, $fields)  {
        // Ajoutez les Intervenants (Speakers) dans la table Speaker
        $stmt = $pdo->prepare("INSERT INTO Speaker (name, role, company_id) VALUES (:nom, :role, :company_id)");
        $stmt->bindParam(':nom', $name);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
    
        // Obtenez l'id du dernier Intervenant ajouté
        $speakerId = $pdo->lastInsertId();
    
        // Ajoutez les assignations dans la table AssignmentSpeaker
        foreach ($fields as $field) {
            $stmtAddAssignment = $pdo->prepare("INSERT INTO AssignmentSpeaker (speaker_id, field_id) VALUES (:speakerId, :filiereId)");
            $stmtAddAssignment->bindParam(':speakerId', $speakerId);
            $stmtAddAssignment->bindParam(':filiereId', $field);
            $stmtAddAssignment->execute();
        }
    }

    function addCompany($pdo, $nom, $description, $adresse, $codePostal, $ville, $secteur, $logo, $intervenants) {
        if (!empty($logo['name'])) {
            $targetDirectory = "../../../../ressources/logosentreprises/";
            $imageFileType = strtolower(pathinfo($logo["name"], PATHINFO_EXTENSION));
    
            // Générer un nom de fichier unique basé sur le nom de l'entreprise
            // Supression des caractères non autorisés
            $cleanedFileName = preg_replace('/[^\w\d\-_.]/', '', $nom);
        
            // Limiter la longueur du nom de fichier si nécessaire
            $cleanedFileName = substr($cleanedFileName, 0, 80);
            $newFileName = $cleanedFileName . '_' . uniqid() . '.' . $imageFileType;
            $targetFile = $targetDirectory . $newFileName;
            var_dump($targetFile);
            $uploadOk = 1;
        
            // Vérifier si le fichier est une image réelle
            $check = getimagesize($logo["tmp_name"]);
            if ($check === false) {
                echo "Le fichier n'est pas une image.";
                $uploadOk = 0;
            }
        
            // Vérifier si le fichier existe déjà
            if (file_exists($targetFile)) {
                echo "Désolé, le fichier existe déjà.";
                $uploadOk = 0;
            }
    
            // Autoriser certains formats de fichiers
            $allowedFormats = array("jpg", "jpeg", "png", "gif");
            if (!in_array($imageFileType, $allowedFormats)) {
                echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
                $uploadOk = 0;
            }
        
            // Vérifier si $uploadOk est défini à 0 par une erreur
            if ($uploadOk == 0) {
                echo "Désolé, votre fichier n'a pas été téléchargé.";
            } else {
                // Si tout est correct, essayez de télécharger le fichier
                if (move_uploaded_file($_FILES["logoEntreprise"]["tmp_name"], $targetFile)) {
                    echo "Le fichier " . htmlspecialchars(basename($_FILES["logoEntreprise"]["name"])) . " a été téléchargé.";
        
                    // Maintenant, vous pouvez utiliser $targetFile comme chemin à stocker dans la base de données
                    $pathToLogo = $targetFile;
        
                    // Insérez le chemin dans la base de données
                    $query = "INSERT INTO entreprises (nom, description, adresse, secteur, logo) VALUES ('$nom', '$description', '$adresse', '$secteur', '$pathToLogo')";
                    // ... exécutez la requête ...
        
                } else {
                    echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
                }
            }
        } else {
            $newFileName = "no-photo.png";
        }
    
        $nom = htmlspecialchars($nom);
        $description = htmlspecialchars($description);
        $adresse = htmlspecialchars($adresse . ', ' . $codePostal . ' ' . $ville);
        $secteur = htmlspecialchars($secteur);
    
        $stmt = $pdo->prepare("INSERT INTO Company (name, logo_file_name, description, address, sector, excluded, useSecondary)
                               VALUES (:nom, :logo, :description, :adresse, :secteur, 0, 0)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':secteur', $secteur);
        $stmt->bindParam(':logo', $newFileName);
        $stmt->execute(); 
    
        $companyId = $pdo->lastInsertId();
    
        // Ajoutez les Intervenants (Speakers) dans la table Speaker
        $stmtAddSpeaker = $pdo->prepare("INSERT INTO Speaker (name, role, company_id) VALUES (:nom, NULL, :companyId)");
        foreach ($intervenants as $intervenant) {
            $stmtAddSpeaker->bindParam(':nom', $intervenant['nom']);
            $stmtAddSpeaker->bindParam(':companyId', $companyId);
            $stmtAddSpeaker->execute();
    
            // Obtenez l'id du dernier Intervenant ajouté
            $speakerId = $pdo->lastInsertId();
    
            // Ajoutez les assignations dans la table AssignmentSpeaker
            foreach ($intervenant['filieres'] as $filiereId) {
                $stmtAddAssignment = $pdo->prepare("INSERT INTO AssignmentSpeaker (speaker_id, field_id) VALUES (:speakerId, :filiereId)");
                $stmtAddAssignment->bindParam(':speakerId', $speakerId);
                $stmtAddAssignment->bindParam(':filiereId', $filiereId);
                $stmtAddAssignment->execute();
            }
        }
    
    }

    function deleteSpeaker($pdo, $id) {
        $stmt = $pdo->prepare("DELETE FROM AssignmentSpeaker
                               WHERE AssignmentSpeaker.Speaker_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    
        $stmt = $pdo->prepare("DELETE FROM Speaker
                               WHERE Speaker.Speaker_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    function modifySpeaker($pdo, $nom, $role, $fields, $id) {
        $nom = htmlspecialchars($nom);
        $role = htmlspecialchars($role);
        $fields = array_map('htmlspecialchars', $fields); // Appliquer htmlspecialchars à chaque élément du tableau
    
        // Modification des données dans la table Speaker
        $stmt = $pdo->prepare("UPDATE Speaker
                                SET name = :nom,
                                    role = :role
                                WHERE speaker_id = :id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    
        // Suppression des anciennes filières qui ne sont plus présentes dans $fields
        $stmt = $pdo->prepare("DELETE FROM AssignmentSpeaker
        WHERE speaker_id = ?
        AND field_id NOT IN (" . implode(',', array_fill(0, count($fields), '?')) . ")");
        $stmt->bindParam(1, $id);
    
        // Ajout des liaisons pour les paramètres dans $fields
        foreach ($fields as $index => $field) {
        $stmt->bindParam($index + 2, $field);
        }
        $stmt->execute();
    
        // Ajout des nouvelles filières qui ne sont pas déjà présentes dans assignmentSpeaker
        $stmt = $pdo->prepare("INSERT IGNORE INTO AssignmentSpeaker (field_id, speaker_id)
                                VALUES (?, ?)");
        foreach ($fields as $field) {
            $stmt->execute([$field, $id]);
        }    
    }

    function getAppointmentPerSpeaker($pdo, $speaker_id) {
        $stmt = $pdo->prepare("SELECT TIME_FORMAT(app.start, '%H:%i') as start, TIME_FORMAT(ADDTIME(app.start, app.duration), '%H:%i') as end, us.username, fie.name
                                FROM Appointment app
                                JOIN User us
                                ON app.user_id = us.user_id
                                JOIN AssignmentUser ass
                                ON ass.user_id = us.user_id
                                JOIN Field fie
                                ON ass.field_id = fie.field_id
                                WHERE app.speaker_id = :speaker_id;");
        $stmt->bindParam(':speaker_id', $speaker_id);
        $stmt->execute();
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
        $sql = "SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector, Company.logo_file_name
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

    function getEntreprisesAdministrateur($pdo, $field_ids, $recherche) {
        // Utilisez la fonction implode pour convertir le tableau en une chaîne séparée par des virgules
        $field_ids_str = implode(', ', $field_ids);
    
        if ($field_ids_str == null) {
            return null;
        }
    
        // Requête SQL de base
        $sql = "SELECT c.company_id, c.name, c.description, c.address, c.sector, c.logo_file_name as logo, GROUP_CONCAT(DISTINCT CONCAT(s.name, ',', COALESCE(s.role, ''), ',', af.fields, ',', s.speaker_id) SEPARATOR ';') AS intervenants_roles
                FROM Company c
                JOIN Speaker s ON c.company_id = s.company_id
                JOIN (SELECT a.speaker_id, GROUP_CONCAT(DISTINCT f.name SEPARATOR '/') AS fields
                FROM AssignmentSpeaker a
                JOIN Field f ON a.field_id = f.field_id
                WHERE a.field_id IN ($field_ids_str)
                GROUP BY a.speaker_id) af ON s.speaker_id = af.speaker_id";
    
        // Ajoutez la condition de recherche à la requête si elle est fournie
        if ($recherche != null) {
            $sql .= " WHERE c.name LIKE :recherche";
        }
    
        // Ajout de l'ordre de tri à la requête
        $sql .= " 
                  GROUP BY c.company_id, c.name, c.description, c.address, c.sector
                  ORDER BY c.name";
    
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
                               JOIN WishList
                               ON WishList.company_id = Company.company_id
                               JOIN User
                               ON WishList.user_id = User.user_id
                               JOIN AssignmentUser
                               ON User.user_id = AssignmentUser.user_id
                               JOIN Field
                               ON AssignmentUser.field_id = Field.field_id
                               WHERE Company.company_id = :company_id;");
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        return $stmt;
    }

    function modifyPassword($pdo, $user_id, $new_password) {
        $new_password = htmlspecialchars($new_password);
    
        $stmt = $pdo->prepare("UPDATE User
                               SET password = :new_password
                               WHERE User.user_id = :user_id");
    
        $stmt->bindParam(':new_password', $new_password);
        $stmt->bindParam(':user_id', $user_id);
        
        $stmt->execute();
    }
?>