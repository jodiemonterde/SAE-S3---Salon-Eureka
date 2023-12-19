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

    function verifUtilisateur($pdo, $motDepasse, $identifiant){
        try{ 
			$connecte=false;
			$maRequete = $pdo->prepare("SELECT user_id, username, password from User where username = :leLogin and password = :lePWD");
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
			$maRequete = $pdo->prepare("SELECT user_id, responsibility from User where username = :leLogin and password = :lePWD");
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

	function getPhase($pdo){
        try{ 
			$maRequete = $pdo->prepare("SELECT phase from Meeting");
			$maRequete->execute();
			return $maRequete->fetch()[0];
		}
		catch ( Exception $e ) {
			echo "Connection failed: " . $e->getMessage();
			return false;
		} 
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
    
    function getEntreprisesPerStudent($pdo, $user_id) {
        $stmt = $pdo->prepare("SELECT Company.company_id,name,logo_file_name,address,sector
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