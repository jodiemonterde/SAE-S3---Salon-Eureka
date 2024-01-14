<?php
    // Fonction de connexion à la base de données : une BD my sql hebergée sur alwaysdata.net
    // Retourne le pdo une fois celui-ci crée 
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


    // Fonction de vérification lors d'une tentative de connexion d'un utilisateur.
    // Si les paramètres correspondent à une ligne dans la table User, cela renvoi les données de cette ligne.
    // Sinon, cela renvoi un tableau vide.
    function verifUtilisateur($pdo, $motDepasse, $identifiant){
        $connecte = array();
        $maRequete = $pdo->prepare("SELECT user_id, responsibility, firstname, lastname, password from User where email = :leLogin");
        $maRequete->bindParam(':leLogin', $identifiant);
        if ($maRequete->execute()) {
            while ($ligne = $maRequete->fetch()) {	
                if (strlen($ligne['password']) < 50 && $ligne["password"] == $motDepasse) { // Si le mot de passe n'est pas hashé, on le hash
                    $connecte['user_id'] = $ligne['user_id'];
                    $connecte['responsibility'] = $ligne['responsibility'];
                    $connecte['firstname'] = $ligne['firstname'];
                    $connecte['lastname'] = $ligne['lastname'];
                } else if (password_verify($motDepasse, $ligne['password'])) {
                    $connecte['user_id'] = $ligne['user_id'];
                    $connecte['responsibility'] = $ligne['responsibility'];
                    $connecte['firstname'] = $ligne['firstname'];
                    $connecte['lastname'] = $ligne['lastname'];
                }
            }
        }
        return $connecte;
    }

    // Récupère les données d'une ligne de la table User en fonction du nom et du mot de passe 
    // Retourne cette ligne si la requête aboutie, faux s'il y a eu un problème.
    function infoUtilisateur($pdo, $motDepasse, $identifiant){
        try{ 
			$maRequete = $pdo->prepare("SELECT user_id, responsibility, firstname, lastname FROM User WHERE email = :leLogin AND password = :lePWD");
			$maRequete->bindParam(':leLogin', $identifiant);
			$maRequete->bindParam(':lePWD', $motDepasse);
			$maRequete->execute();
			return $maRequete;
		}
		catch ( Exception $e ) {  // Retourne faux en cas d'erreur avec la BD
			echo "Connection failed: " . $e->getMessage();
			return false;
		} 
    }

	// Récupère les informations concernant le forum Eurêka.
    function infoForum($pdo){
        try{
            $maRequete=$pdo->prepare('SELECT date,start,end, primary_appointment_duration, secondary_appointment_duration, wish_period_end FROM Meeting');
            $maRequete->execute();
            return $maRequete;
        }
		catch ( Exception $e ) { // Retourne faux en cas d'erreur avec la BD
			echo "Connection failed: " . $e->getMessage();
			return false;
		} 
    }

    // Fonction permettant de modifier le forum afin d'attribuer de nouvelles valeurs à celui-ci
    function updateForum($pdo,$dateForum,$debut,$fin,$dureePrincipal,$dureeSecondaire,$jourFin){
        try{
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
        }catch(Exception $e){ // Retourne en arrière en cas d'erreur avec la BD
            $pdo->rollBack();
        }

    }

    // Fonction permettant d'obtenir la phase actuelle dans laquelle se trouve le forum actuellement
    // 3 phases sont possibles :
    //    - phase 1 : les étudiants peuvent ajouter des entreprises à leur liste de souhaits
    //    - phase 1.5 : un administrateur manipule les listes de souhaits des étudiants dans le but de créer un planning
    //    - phase 2 : le planning est généré et les étudiants y ont accès.
    function getPhase($pdo){
        try{ 
            $maRequete = $pdo->prepare("SELECT phase, wish_period_end from Meeting");
            $maRequete->execute();
            $ligne = $maRequete->fetch();
            $phase = $ligne['phase'];
            $phase = $phase == 1 && $ligne['wish_period_end'] < date("Y-m-d") ? 1.5 : $phase;
            return $phase;
		} catch ( Exception $e ) { // Retourne faux en cas d'erreur avec la BD
			echo "Connection failed: " . $e->getMessage();
			return false;
		} 
    }

    // Fonction permettant d'obtenir les entreprises que souhaitent rencontrer un étudiant passé en paramètre.
    // Paramètre recherche : permet d'affiner la recherche selon un champs de recherche
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
                WHERE AssignmentUser.user_id = :user_id ");
        if ($recherche != null) { // Si le paramètre recherche est nul, toutes les entreprises s'affichent alors.
            $sql.= " AND Company.name LIKE :recherche ";
        }

        $sql .= "ORDER BY Company.name";
        $stmt = $pdo->prepare($sql);
        
        if ($recherche != null) {
            $stmt->bindValue(':recherche', '%' . $recherche . '%');
        }
        $stmt->bindParam(':user_id', $user_id);

        $stmt->execute();

        return $stmt;
    }

    // Fonction permettant de vérifier si un planning est généré.
    function isPlanningGenerated($pdo) {
        $stmt = $pdo->prepare("SELECT generated FROM Meeting WHERE meeting_id = 1");
        $stmt->execute();
        $ligne = $stmt->fetch();
        return $ligne['generated'] === 1;
    }

    // Fonction faisant appel à une fonction dans la base de données permettant de générer un planning
    function genererPlanning($pdo) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT generatePlanning()");
        $stmt->execute();
        return $stmt->fetch()[0];
        $pdo->commit();
    }

    // Fonction permettant de changer de phase pour passer en phase 2.
    function launchPhase2($pdo) {
        $stmt = $pdo->prepare("UPDATE Meeting SET phase = 2 WHERE meeting_id = 1");
        $stmt->execute();
    }

    // Fonction permettant d'annuler la génération du planning si celui-ci ne convient pas 
    //à l'administateur en train d'essayer d'en générer un
    function cancelPlanning($pdo) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM Appointment;");
        $stmt->execute();
        $stmt = $pdo->prepare("UPDATE Meeting SET generated = 0 WHERE meeting_id = 1;");
        $stmt->execute();
        $pdo->commit();
    }

    // Focntion permettant de mettre générated (BIT verifiant si le planning à été générée ou non) à 1 ou 0 selon le choix utilisateur
    function setPlanningGenerated($pdo, $value) {
        $stmt = $pdo->prepare("UPDATE Meeting SET generated = :value WHERE meeting_id = 1");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
    }

    // Ajout d'un nouvel étudiant à la base de données. 
    // Gère aussi l'ajout de lassignement de cet étudiant (la filière à laquelle il est assigné)
    function addNewStudent($pdo, $prenom, $nom, $email, $mdp, $filiere) {
        $pdo->beginTransaction();
        $nom = strtoupper($nom);
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);
        $filiere = $filiere;

        $stmt = $pdo->prepare("INSERT INTO User (firstname, lastname, password, responsibility, email)
                            VALUES (:prenom, :nom, :password, 'E', :email)");
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
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
        $pdo->commit();
    }

    // Ajout d'un nouveau gestionnaire à la base de données. 
    // Gère aussi l'ajout des assignements de ce gestionnaires (les filières auxquelles il est assigné)
    function addNewSupervisor($pdo, $prenom, $nom, $email, $mdp, $filiere) {
        $pdo->beginTransaction();
        $nom = strtoupper($nom);
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO User (firstname, lastname, password, responsibility, email)
                            VALUES (:prenom, :nom, :password, 'G', :email)");
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
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
        $pdo->commit();
    }

    // Fonction permettant de supprimer un étudiant et toutes les données qui peuvent le concerner :
    //     - Ses souhaits de rendez-vous s'il en a émis
    //     - Ses rendez-vous idem
    //     - les filières qui lui sont assignés
    //     - L'utilisateur en question
    function deleteStudent($pdo, $user_id) {
        $pdo->beginTransaction();
        $user_id = $user_id;
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
        $pdo->commit();
    }

    // Permet de modifier une entreprise et de la mettre à jour selon les différents paramètres.
    // Si un ou plusieurs paramètres ont une valeur nulle, alors la modification de ce champs spécifique ne remplace pas l'ancienne valeur.
    function modifyCompany($pdo, $company_id, $nom, $description, $secteur, $adresse, $codePostal, $ville, $logo) {

        // Vérification du logo
        if (!empty($logo['name'])) {
            if (!empty($logo)) { // Vérification de s'il y a eu un changement dans le logo
                $nomPourChemin = $logo['name'];
            } else {
                $nomPourChemin = $ancienNom;
            }
            $newFileName = checkImage($logo, $nomPourChemin);
        } else {
            $newFileName = null;
        }
        
        // Vérification de différents paramètres : set à null s'ils sont vides
        $nom = empty($nom) ? null : $nom;
        $description = empty($description) ? null : $description;
        $adresse = empty($adresse) ? null : $adresse . ', ' . $codePostal . ' ' . $ville;
        $secteur = empty($secteur) ? null : $secteur;    
        
        // Requête de modification de l'entreprise
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

    // Fonction de suppression d'un intervenant. 
    // Permet également de supprimer les assignements de cet intervenant.
    function deleteSupervisor($pdo, $user_id) {
        $pdo->beginTransaction();
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
        $pdo->commit();
    }

    // Fonction permettant de récupérer les entreprises selon l'identifiant d'un étudiant passé en paramètres.
    // Il s'agit des entreprises que l'étudiant souhaite rencontrer.
    function getEntreprisesPerStudent($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT Company.company_id,name,logo_file_name,address,sector
                            FROM Company
                            JOIN WishList ON Company.company_id = WishList.company_id
                            WHERE user_id = :user_id
                            ORDER BY name");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Fonction permettant d'obtenir toutes les filières stockées dans la base de données
    function getFields($pdo) {
        $sql = "SELECT * FROM `Field` ORDER BY name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    // Fonction permettant d'obtenir toutes les filières attribuées à un utilisateur spécifié en paramètre. 
    function getFieldsPerUsers($pdo, $user_id) {
        $sql = "SELECT * FROM `Field` WHERE field_id IN (SELECT field_id FROM AssignmentUser WHERE user_id = :user_id) ORDER BY name";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Fonction permettant d'obtenir les informations liées à un étudiant :
    //    - son nom
    //    - son prénom
    //    - le nombre de souhait qu'il a émi (le nombre d'entreprises qu'il souhaite rencontrer)
    // Le résultat dépend de deux filtres : un filtre de recherche où les étudiants concernés doivent avoir leur nom
    // ou prénom contenant ce paramètre et un filtre par filières : seuls les étudiants assignés aux filières correspondantes seront
    // appelés par la requête. 
    function getInfoStudents($pdo, $recherche, $field_ids) {
        $field_ids_str = implode(', ', $field_ids);
    
        if ($field_ids_str == null) {
            return null;
        }
    
        $sql = "SELECT u.firstname, u.lastname, f.name AS filiere, COUNT(w.company_id) AS nbSouhait, u.user_id
                FROM User u
                JOIN AssignmentUser au
                ON u.user_id = au.user_id
                JOIN Field f
                ON au.field_id = f.field_id
                LEFT JOIN WishList w
                ON u.user_id = w.user_id
                WHERE u.responsibility = 'E'
                AND f.field_id IN (:fields)
                GROUP BY u.lastname, u.firstname, filiere";
    
        if ($recherche != null) { // Si le paramètre recherche est nul, tous les étudiants s'affichent alors.
            $sql .= " HAVING u.firstname LIKE :recherche
                      OR u.lastname LIKE :recherche";
        }
    
        $sql .= " ORDER BY u.lastname";
    
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fields', $field_ids_str);
        if ($recherche != null) {
            $stmt->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
        }
    
        $stmt->execute();
        return $stmt;
    }

    // Fonction permettant de changer les propriétées d'une entreprise, selon l'action passée en paramètre. Fonction utilisée dans la page de génération du planning.
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

    /// Fonction permettant d'obtenir les entreprises selon une spécification passée en paramètre et la valeur de cette spécification passée en paramètre.
    function getSpecificationCompany($pdo, $specification, $value) {
        switch ($specification) {
            case 'entrepriseReduite' :
                $sql = "SELECT company_id, name FROM Company WHERE useSecondary = :value ORDER BY name;";
                break;
            case 'entrepriseExclusion' :
                $sql = "SELECT company_id, name FROM Company WHERE excluded = :value ORDER BY name;";
                break;
            default :
                return null;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $stmt;
    }
    
    // Fonction permettant de supprimer le souhait d'un étudiant de sa liste de souhaits.
    function removeWishStudent($pdo, $user_id, $company_id) {
        $stmt = $pdo->prepare("DELETE FROM WishList
                            WHERE user_id = :user_id AND company_id = :company_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':company_id', $company_id);
        return $stmt->execute();
    }

    // Fonction permettant d'obtenir le planning de l'utilisateur passé en paramètre.
    // Retourne un tableau contenant les données liés à ce planning : l'heure de début de chaque rdv, l'heure de fin, la nom de l'entreprise...
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

    // Permet d'obtenir toutes les entreprises dont le planning n'a pas pu être généré
    // concernant un étudiant spécifique passé en paramètre.
    function unlistedCompanyPerUser($pdo, $user_id) {
        $requete = $pdo-> prepare("SELECT c.name 
                                FROM Company c
                                JOIN WishList w on c.company_id = w.company_id
                                WHERE c.excluded = 1
                                AND w.user_id = :user_id
                                ORDER BY name");
        $requete->bindParam(':user_id', $user_id);
        $requete->execute();
        return $requete;
    }

    // Permet d'obtenir la liste des étudiants selon les filtres passés en paramètre : 
    //    - Le champs de recherche
    //    - les filières
    // Le tout trié selon le dernier paramètre (soit par ordre alphabétique, soit pas nombre de souhaits)
    function getInfoStudentsSort($pdo, $recherche, $field_ids, $sort) {
        $field_ids_str = implode(', ', $field_ids);

        $sort = $sort;

        if ($field_ids_str == null) {
            return null;
        }

        $sql = "SELECT u.firstname, u.lastname, f.name AS filiere, COUNT(w.company_id) AS nbSouhait, u.user_id
                FROM User u
                JOIN AssignmentUser au
                ON u.user_id = au.user_id
                JOIN Field f
                ON au.field_id = f.field_id
                LEFT JOIN WishList w
                ON u.user_id = w.user_id
                WHERE u.responsibility = 'E'
                AND f.field_id IN ($field_ids_str)
                GROUP BY u.lastname, u.firstname, filiere";

        if ($recherche != null) {
            $sql .= " HAVING u.lastname LIKE :recherche
                      OR u.firstname LIKE :recherche";
        }

        if ($sort == "default" || $sort == "alpha") { 
            $sql .= " ORDER BY u.lastname";
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

    // Fonction permettant d'obtenir la liste des gestionnaires selon les filtres passés en paramètre.
    function getInfosSupervisors($pdo, $recherche, $field_ids) {
        $parameters = $field_ids;

        if (!is_array($parameters)) {
            $parameters = [$field_ids];
        }

        if ($field_ids == null || count($field_ids) == 0) {
            return null;
        }
        
        $parametersAsQuestionMarks = implode(',', array_fill(0, count($parameters), '?'));
        
        $sql = "SELECT u.firstname, u.lastname, GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ', ') AS filieres, u.user_id
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
                GROUP BY u.lastname, u.firstname, u.user_id";
        
        
        if ($recherche != null) {
            $sql .= " HAVING u.lastname LIKE ?
                      OR u.firstname LIKE ?";
        }

        $sql .= " ORDER BY u.lastname";
        $stmt = $pdo->prepare($sql);

        if ($recherche != null) {
            array_push($parameters,'%' . $recherche . '%');
            array_push($parameters,'%' . $recherche . '%');
        }
        
        $stmt->execute(array_values($parameters));
        return $stmt;
    }


    // Fonction permettant de supprimer un souhait de la liste des souhaits d'un étudiant
    function deleteWishStudent($pdo, $user_id, $company_id) {
        $stmt = $pdo->prepare("DELETE IGNORE FROM WishList
                               WHERE user_id = :user_id 
                               AND company_id = :company_id");
        $stmt->bindParam(':user_id', $user_id);                  
        $stmt->bindParam(':company_id', $company_id);
        return $stmt->execute();
    }

    // Fonction permettant d'ajouter un souhait à la liste des souhaits d'un étudiant
    function addWishStudent($pdo, $user_id, $company_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO WishList (user_id, company_id)
                              VALUES (:user_id, :company_id)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':company_id', $company_id);
        return $stmt->execute();
    }

    // Fonction permettant d'obtenir les entreprises en phase 2 : on souhaite ici aussi obtenir la ligne excluded, inutile en phase 1
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

    // Fonction permettant d'obtenir la liste des intervenants par entreprise. 
    function getSpeakersPerCompany($pdo, $company_id) {
        $stmt = $pdo->prepare("SELECT speaker_id, name, role FROM Speaker WHERE company_id = :company_id ORDER BY name");
        $stmt->bindParam(':company_id', $company_id);
        $stmt->execute();
        return $stmt;
    }

    // Fonction permettant d'obtenir une liste d'intervenant trié dans un tableau
    // à partir d'un tableau de données brutes où toutes les données sont stockées dans une même ligne.
    function getSpeakersPerCompanyAdministrateur($tableau_intervenants) {
        // Séparer la chaîne principale en utilisant ';' comme délimiteur pour obtenir les intervenants
        $intervenants_array = explode('&#30;', $tableau_intervenants);
            
        // Initialiser un tableau pour stocker les données finales
        $final_array = array();
    
        // Parcourir chaque intervenant
        foreach ($intervenants_array as $intervenant) {
            // Exploser les différents éléments de l'intervenant en utilisant ',' comme délimiteur
            $intervenant_elements = explode('&#31;', $intervenant);
    
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

        return $final_array;
    }

    // Fonction permettant la suppression d'une entreprise, mais également
    //    - La suppression de tous ses intervenants (et de leurs assignements)
    //    - La suppression des souhaits émis par rapport à cette entreprise.
    function deleteCompany($pdo, $company_id) {
        $pdo->beginTransaction();
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
    
        $stmt = $pdo->prepare("DELETE FROM WishList
                               WHERE company_id = :id");
        $stmt->bindParam(':id', $company_id);
        $stmt->execute();
        
        $stmt = $pdo->prepare("DELETE FROM Company
                               WHERE company_id = :id");
        $stmt->bindParam(':id', $company_id);
        $stmt->execute();
        $pdo->commit();
    }

    // Fonction permettant d'ajouter un nouvel intervenant. 
    // Cet intervenant est assignée à une entreprise en particulier, et il est également assignés 
    // à une ou plusieurs filières.
    // Son rôle (un titre expliquant sa fonction) n'est pas obligatoire. 
    function addSpeaker($pdo, $company_id, $name, $role, $fields)  {
        $pdo->beginTransaction();
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
        $pdo->commit();
    }

    // Ajout d'une nouvelle entreprises avec en paramètre tous les paramètres nécessaires.
    // La description et le logo sont facultatifs, et les intervenants sont stockés dans un tableau.
    function addCompany($pdo, $nom, $description, $adresse, $codePostal, $ville, $secteur, $logo, $intervenants) {
        $pdo->beginTransaction();
        // Vérification du logo : s'il est vide, un logo par défaut est attribué.
        if (!empty($logo['name'])) {
            $newFileName = checkImage($logo, $nom);
        } else {
            $newFileName = "no-photo.png";
        }
    
        // Empêche l'injection de code
        $nom = strtoupper($nom);
        $description = $description;
        $adresse = $adresse . ', ' . $codePostal . ' ' . strtoupper($ville);
        $secteur = $secteur;
    
        $stmt = $pdo->prepare("INSERT INTO Company (name, logo_file_name, description, address, sector, excluded, useSecondary)
                               VALUES (:nom, :logo, :description, :adresse, :secteur, 0, 0)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':secteur', $secteur);
        $stmt->bindParam(':logo', htmlspecialchars($newFileName));
        $stmt->execute(); 
    
        $companyId = $pdo->lastInsertId();
    
        // Ajout des Intervenants (Speakers) dans la table Speaker
        $stmtAddSpeaker = $pdo->prepare("INSERT INTO Speaker (name, role, company_id) VALUES (:nom, :role, :companyId)");
        foreach ($intervenants as $intervenant) {
            $stmtAddSpeaker->bindParam(':nom', $intervenant['nom']);
            $stmtAddSpeaker->bindParam(':role', $intervenant['role']);
            $stmtAddSpeaker->bindParam(':companyId', $companyId);
            $stmtAddSpeaker->execute();
    
            // Obtention de l'id du dernier Intervenant ajouté
            $speakerId = $pdo->lastInsertId();
    
            // Ajout des assignations dans la table AssignmentSpeaker
            foreach ($intervenant['filieres'] as $filiereId) {
                $stmtAddAssignment = $pdo->prepare("INSERT INTO AssignmentSpeaker (speaker_id, field_id) VALUES (:speakerId, :filiereId)");
                $stmtAddAssignment->bindParam(':speakerId', $speakerId);
                $stmtAddAssignment->bindParam(':filiereId', $filiereId);
                $stmtAddAssignment->execute();
            }
        }
        $pdo->commit();
    }

    // Fonction permettant de supprimer un intervenant.
    function deleteSpeaker($pdo, $id, $comp_id) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM AssignmentSpeaker
                               WHERE AssignmentSpeaker.Speaker_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $stmt = $pdo->prepare("DELETE w
                               FROM WishList w
                               JOIN AssignmentUser
                               ON AssignmentUser.user_id = w.user_id
                               WHERE w.company_id = :comp_id1
                               AND AssignmentUser.field_id NOT IN (SELECT AssignmentSpeaker.field_id
                                                                   FROM AssignmentSpeaker
                                                                   JOIN Speaker
                                                                   ON AssignmentSpeaker.speaker_id = Speaker.speaker_id
                                                                   WHERE Speaker.company_id = :comp_id2);");
        $stmt->bindParam(':comp_id1', $comp_id);
        $stmt->bindParam(':comp_id2', $comp_id);
        $stmt->execute();
    
        $stmt = $pdo->prepare("DELETE FROM Speaker
                               WHERE Speaker.Speaker_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $pdo->commit();
    }

    // Fonction permettant de modifier un intervenant
    function modifySpeaker($pdo, $nom, $role, $fields, $id) {
        $pdo->beginTransaction();
        // Verification injection de code
        $nom = $nom;
        $role = $role;
        $fields = array_map('htmlspecialchars', $fields); // permet d'appliquer htmlspecialchars à chaque élément du tableau
    
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
        $pdo->commit();
    }

    // Fonction permettant d'obtenir les rendez-vous par intervenant (grâce à l'identifiant de l'intervenant passé en paramètre).
    function getAppointmentPerSpeaker($pdo, $speaker_id) {
        $stmt = $pdo->prepare("SELECT TIME_FORMAT(app.start, '%H:%i') as start, TIME_FORMAT(ADDTIME(app.start, app.duration), '%H:%i') as end, us.firstname, us.lastname, fie.name
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

    // Fonction permettant d'obtenir la liste des étudiants en fonction de la liste de souhaits d'une entreprise.
    function getStudentsPerCompanyWishList($pdo, $company_id) {
        $stmt = $pdo->prepare("SELECT Field.name, User.lastname, User.firstname
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

    // Fonction permettant d'obtenir la liste planning des étudiants en fonction d'une entreprise passée en paramètre
    function getStudentsAppointmentsPerCompany($pdo, $company_id, $isExcluded) {
        // Si l'entreprise est exclu, fait appel à la fonction permettant d'obtenir la wishlist de cet étudiant concernant cette entreprise
        // car aucun planning n'a pu être généré.
        if ($isExcluded == 1) {
            return getStudentsPerCompanyWishList($pdo, $company_id);
        } else { 
            $stmt = $pdo->prepare("SELECT Field.name, User.firstname, User.lastname, TIME_FORMAT(Appointment.start, '%H:%i') as start, TIME_FORMAT(ADDTIME(Appointment.start, Appointment.duration), '%H:%i') as duration
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

    // Fonction permettant d'obtenir la liste des entreprises stockés dans la BD selon les filtres passés en paramètre
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

    // Fonction permettant d'obtenir la liste des entreprises stockés dans la BD selon les filtres passés en paramètre
    // Renvoi également la liste des intervenants liés à chaque entreprise.
    function getEntreprisesAdministrateur($pdo, $field_ids, $recherche) {
        // Utilisez la fonction implode pour convertir le tableau en une chaîne séparée par des virgules
        $field_ids_str = implode(', ', $field_ids);
    
        if ($field_ids_str == null) {
            return null;
        }
    
        // Requête SQL de base
        $sql = "SELECT c.company_id, c.name, c.description, c.excluded, c.address, c.sector, c.logo_file_name as logo, GROUP_CONCAT(DISTINCT CONCAT(s.name, '&#31;', COALESCE(s.role, ''), '&#31;', af.fields, '&#31;', s.speaker_id) SEPARATOR '&#30;') AS intervenants_roles
                FROM Company c
                RIGHT JOIN Speaker s ON c.company_id = s.company_id
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

    // Fonction permettant d'obtenir la liste des étudiants souhaitant rencontré l'entreprise passée en paramètre
    function getStudentsPerCompany($pdo, $company_id) {
        $stmt = $pdo->prepare("SELECT Field.name, User.firstname, User.lastname
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

    // Fonction permettant de changer le mot de passe d'un utilisateur pour un nouveau passé en paramètre.
    function modifyPassword($pdo, $user_id, $new_password) {
        // Verification injection de code
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
    
        $stmt = $pdo->prepare("UPDATE User
                               SET password = :new_password
                               WHERE User.user_id = :user_id");
    
        $stmt->bindParam(':new_password', $new_password);
        $stmt->bindParam(':user_id', $user_id);
        
        $stmt->execute();
    }

    // Fonction permettant d'insérer une liste d'étudiants en une fois avec les filières associées à chaque étudiant
    function inserer_etudiants($pdo, $etudiants, $filieres) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO User (firstname, lastname,  email, password, responsibility)
                            VALUES (:prenom, :nom, :email, :password, 'E')");
        $stmt2 = $pdo->prepare("INSERT INTO AssignmentUser (user_id, field_id)
                            VALUES (:user_id, :field_id)");
        foreach ($etudiants as $etudiant) {
            $etudiant[0] = strtoupper($etudiant[0]);

            $stmt->bindParam(':prenom', $etudiant[1]);
            $stmt->bindParam(':nom', $etudiant[0]);
            $stmt->bindParam(':email', $etudiant[2]);
            $stmt->bindParam(':password', password_hash($etudiant[3], PASSWORD_DEFAULT));
            $stmt->execute();
            $user_id = $pdo->lastInsertId();
            $stmt2->bindParam(':user_id', $user_id);
            $stmt2->bindParam(':field_id', $filieres[$etudiant[4]]);
            $stmt2->execute();
        }
        $pdo->commit();
    }

    // Fonction permettant d'obtenir toutes les entreprises non exclues du planning qui ont des rendez-vous
    function getCompanyNotExcluded($pdo){
        $maRequete = $pdo->prepare("SELECT company_id,name 
                                    FROM Company c1
                                    WHERE excluded = 0 AND (SELECT count(*)
                                                            FROM Appointment ap
                                                            JOIN Speaker s on ap.speaker_id = s.speaker_id
                                                            JOIN Company c on s.company_id = c.company_id
                                                            WHERE c.company_id = c1.company_id) > 0
                                    ORDER BY name;");
        $maRequete->execute();
        return $maRequete;
    }

    // Fonction permettant d'obtenir le nom d'une entreprise à partir de son identifiant
    function getCompanyName($pdo, $company_id){
        $maRequete = $pdo->prepare("SELECT name FROM Company WHERE company_id = :company_id");
        $maRequete->bindParam(':company_id', $company_id);
        $maRequete->execute();
        $res;
        while($row = $maRequete->fetch()){
            $res = $row["name"];
        }
        return $res;
    }

    // Fonction permettant d'obtenir le nom d'un étudiant à partir de son identifiant
    function getStudentName($pdo, $user_id){
        $maRequete = $pdo->prepare("SELECT firstname, lastname FROM User WHERE user_id = :user_id");
        $maRequete->bindParam(':user_id', $user_id);
        $maRequete->execute();
        while($row = $maRequete->fetch()){
            $res = $row["firstname"] . ' ' . $row['lastname'];
        }
        return $res;
    }

    // Fonction permettant d'obtenir toutes les entreprises exclues du planning
    function getCompanyExcluded($pdo){
        $maRequete = $pdo->prepare("SELECT company_id,name FROM Company WHERE excluded = 1 ORDER BY name;");
        $maRequete->execute();
        return $maRequete;
    }

    // Fonction permettant d'obtenir la liste de tous les étudiants
    function getStudent($pdo){
        $maRequete = $pdo->prepare("SELECT user_id, firstname, lastname FROM User WHERE responsibility = 'E'");
        $maRequete->execute();
        return $maRequete;
    }
    
    // Fonction permettant d'obtenir la liste de tous les étudiants ayant émis au moins un souhait
    function getStudentsWithMeeting($pdo){
        $maRequete = $pdo->prepare("SELECT DISTINCT User.user_id, User.firstname, User.lastname FROM User JOIN WishList ON WishList.user_id = User.user_id WHERE responsibility = 'E' ORDER BY lastname;");
        $maRequete->execute();
        return $maRequete;
    }

    
    function studentByUnlistedCompany($pdo, $company_id) {
        $requete = $pdo-> prepare("SELECT u.firstname, u.lastname , f.name
                                   FROM Field f
                                   JOIN AssignmentUser a on f.field_id = a.field_id
                                   JOIN User u on a.user_id = u.user_id
                                   JOIN WishList w on u.user_id = w.user_id
                                   WHERE  w.company_id = :company_id");
        $requete->bindParam(':company_id', $company_id);
        $requete->execute();
        return $requete;
    }

    // Fonction permettant de réinitialiser l'ensemble des données stockées dans la base de données à l'exception :
    //    - de la liste des Gestionnaires
    //    - de la liste des Administrateurs
    function reinitialiserDonnees($pdo) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM Appointment");
        $stmt->execute();
        $stmt = $pdo->prepare("DELETE FROM WishList");
        $stmt->execute();
        $stmt = $pdo->prepare("DELETE FROM AssignmentSpeaker");
        $stmt->execute();
        $stmt = $pdo->prepare("DELETE FROM AssignmentUser");
        $stmt->execute();
        $stmt = $pdo->prepare("DELETE FROM Speaker");
        $stmt->execute();
        $stmt = $pdo->prepare("DELETE FROM Company");
        $stmt->execute();
        $stmt = $pdo->prepare("DELETE FROM User WHERE responsibility = 'E'");
        $stmt->execute();
        $stmt = $pdo->prepare('UPDATE Meeting SET date = "9999-12-31", start = "00:00:00", end = "23:59:59", primary_appointment_duration = "00:15:00", secondary_appointment_duration = "00:10:00", wish_period_end = "9999-12-31", phase = 1, generated = 0 WHERE meeting_id = 1');
        $stmt->execute();
        $pdo->commit();
    }

    // Fonction permettant de déterminer si la filière passé en paramètre a des étudiant ou des intervenants qui lui sont attribués
    function isFieldInUse($pdo, $field_id) {
        $stmt = $pdo->prepare("SELECT (
                       (SELECT COUNT(*)
                        FROM Field 
                        JOIN AssignmentUser
                        ON Field.field_id = AssignmentUser.field_id
                        JOIN User
                        ON AssignmentUser.user_id = User.user_id
                        WHERE  User.responsibility = 'E'
                        AND Field.field_id = :field_id1)
                       + 
                       (SELECT COUNT(*) 
                        FROM Field 
                        JOIN AssignmentSpeaker
                        ON Field.field_id = AssignmentSpeaker.field_id
                        WHERE Field.field_id = :field_id2)) AS res;");
        $stmt->bindParam(':field_id1', $field_id);
        $stmt->bindParam(':field_id2', $field_id);
        $stmt->execute();
        $res = $stmt->fetch()[0];
        return $res > 0;
    }

    // Fonction permettant de créer une nouvelle filière
    function newField($pdo, $name) {
        $stmt = $pdo->prepare("INSERT INTO Field (name) VALUES (:name)");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
    }

    // Fonction permettant de supprimer une filière
    function deleteField($pdo, $field_id) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("DELETE FROM AssignmentUser WHERE field_id = :field_id");
        $stmt->bindParam(':field_id', $field_id);
        $stmt->execute();
        $stmt = $pdo->prepare("SELECT User.user_id FROM User LEFT JOIN AssignmentUser ON User.user_id = AssignmentUser.user_id WHERE User.responsibility = 'G' GROUP BY (User.user_id) HAVING COUNT(AssignmentUser.field_id) = 0;");
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $stmt2 = $pdo->prepare("DELETE FROM User WHERE user_id = :user_id");
            $stmt2->bindParam(':user_id', $row['user_id']);
            $stmt2->execute();
        }
        $stmt = $pdo->prepare("DELETE FROM Field WHERE field_id = :field_id");
        $stmt->bindParam(':field_id', $field_id);
        $stmt->execute();
        $pdo->commit();
    }

    // Fonction permettant de modifier une filière
    function modifyField($pdo, $field_id, $name) {
        $stmt = $pdo->prepare("UPDATE Field SET name = :name WHERE field_id = :field_id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':field_id', $field_id);
        $stmt->execute();
    }

    // Fonction permettant d'ajouter un nouvel utilisateur
    function addAdmin($pdo, $prenom, $nom, $email, $mdp) {
        $nom = strtoupper($nom);
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO User (firstname, lastname, password, responsibility, email)
                            VALUES (:prenom, :nom, :mdp, 'A', :email)");
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->bindParam(':email', $email);
        $stmt->execute();         
    }

    // Fcntion permettant d'obtenir la liste des administrateurs selon le champs de recherche passé en paramètre
    function getInfosAdmins($pdo, $recherche) {      
        $sql = "SELECT User.firstname, User.lastname, User.user_id
                FROM User
                WHERE User.responsibility = 'A'";
        
        
        if ($recherche != null) {
            $sql.= " AND User.firstname LIKE :recherche
                     OR User.lastname LIKE :recherche";
        }

        $sql .= " ORDER BY User.lastname";

        $stmt = $pdo->prepare($sql);
        
        if ($recherche != null) {
            $stmt->bindValue(':recherche', '%' . $recherche . '%');
        }

        $stmt->execute();
        return $stmt;
    }

    // Fonction permettant de supprimer un administrateur
    function deleteAdmin($pdo, $user_id) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("SELECT * 
                               FROM User
                               WHERE User.responsibility = 'A';");
        $stmt->execute();                       
        if ($stmt->rowCount() > 1) {
            $stmt = $pdo->prepare("DELETE 
                                FROM User
                                WHERE User.user_id = :user_id;");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
        }
        $pdo->commit();
    }

    // Fonction permettant de vérifier si une image est conforme et peut donc être insérer dans la base de données.
    // Si tel est le cas, cette fonction va upload l'image.
    function checkImage($logo, $nom) {
        // Vérification lié à la taille de l'image : si celle-ci est trop grande, alors un resize est effectué pour
        // rendre l'image plus petite.
        $maxDim = 800;
        $file_name = $logo['tmp_name'];
        list($width, $height, $type, $attr) = getimagesize( $file_name );
        if ( $width > $maxDim || $height > $maxDim ) {
            $target_filename = $file_name;
            $ratio = $width/$height;
            if( $ratio > 1) {
                $new_width = $maxDim;
                $new_height = $maxDim/$ratio;
            } else {
                $new_width = (int) $maxDim*$ratio;
                $new_height = (int) $maxDim;
            }
            $src = imagecreatefromstring( file_get_contents( $file_name ) );
            $dst = imagecreatetruecolor( (int) $new_width, (int) $new_height );
            imagecopyresampled( $dst, $src, 0, 0, 0, 0, (int) $new_width, (int) $new_height, $width, $height );
            imagedestroy( $src );
            imagepng( $dst, $target_filename ); // ajustement du format selon le besoin
            imagedestroy( $dst );
        }
        $targetDirectory = "../../ressources/logosentreprises/";
        $imageFileType = strtolower(pathinfo($logo["name"], PATHINFO_EXTENSION));

        // Génère un nom de fichier unique basé sur le nom de l'entreprise
        // Suppression des caractères non autorisés
        $cleanedFileName = preg_replace('/[^\w\d\-_.]/', '', $nom);

        // Limite la longueur du nom de fichier si nécessaire
        $cleanedFileName = substr($cleanedFileName, 0, 80);
        $newFileName = $cleanedFileName . '_' . uniqid() . '.' . $imageFileType;
        $targetFile = $targetDirectory . $newFileName;
        $uploadOk = 1;
    
        // Vérifie si le fichier existe déjà
        if (file_exists($targetFile)) {
            echo "Désolé, le fichier existe déjà.";
            $uploadOk = 0;
        }

        // Autorise les formats d'images uniquement
        $allowedFormats = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
            $uploadOk = 0;
        }
    
        // Vérifie si $uploadOk est défini à 0 par une erreur
        if ($uploadOk == 0) {
            echo "Désolé, votre fichier n'a pas été téléchargé.";
        } else {
            // Si tout est correct, essaye de télécharger le fichier
            if (move_uploaded_file($_FILES["logoEntreprise"]["tmp_name"], $targetFile)) {
                echo "Le fichier " . basename($_FILES["logoEntreprise"]["name"]) . " a été téléchargé.";
    
                // retourne $targetFile comme chemin à stocker dans la base de données
                return $targetFile;
    
            } else {
                echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
            }
        }
    }

    // Fonction permettant d'obtenir la date à laquelle la phase 1 se termine.
    function getDatePeriodEnd($pdo){
        $reponse = $pdo->query("SET lc_time_names = 'fr_FR'");
        $maRequete=$pdo->prepare('SELECT DATE_FORMAT(wish_period_end, "%e %M %Y") as dateFin FROM Meeting');
        $maRequete->execute();
        return $maRequete;
    }
?> 