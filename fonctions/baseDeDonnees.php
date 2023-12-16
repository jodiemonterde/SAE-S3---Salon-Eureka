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
			$maRequete = $connexion->prepare("SELECT user_id, username, password from User where username = :leLogin and password = :lePWD");
			$maRequete->bindParam(':leLogin', $identifient);
			$maRequete->bindParam(':lePWD', $motDepasse);
			if ($maRequete->execute()) {
				$maRequete->setFetchMode(PDO::FETCH_OBJ);
				while ($ligne=$maRequete->fetch()) {				
					$connecte=true;
				}
			}
			return $connecte;
		}
		catch ( Exception $e ) {
			echo "<h1>Erreur de connexion à la base de données ! </h1>";
			return false;
		} 
    }

    function infoUtilisateur($pdo, $motDepasse, $identifiant){
        try{ 
			$maRequete = $connexion->prepare("SELECT user_id, responsability from User where username = :leLogin and password = :lePWD");
			$maRequete->bindParam(':leLogin', $identifient);
			$maRequete->bindParam(':lePWD', $motDepasse);
			if ($maRequete->execute()) {
				$maRequete->setFetchMode(PDO::FETCH_OBJ);
				while ($ligne=$maRequete->fetch()) {				
					$_SESSION['idUtilisateur'] = $ligne->user_id;
                    $_SESSION['typeUtilisateur'] = $ligne->responsability;
				}
			}
		}
		catch ( Exception $e ) {
			echo "<h1>Erreur de connexion à la base de données ! </h1>";
			return false;
		} 
    }
?>