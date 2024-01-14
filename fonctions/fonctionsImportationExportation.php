<?php
    /* fichiers nécessaire au fonctionnements des différentes fonctions  */
    require('lib/fpdf.php'); // librairies utilisé pour créer des pdf
    require_once("baseDeDonnees.php");
    
    /* fonction qui exporte le planning d'une entreprise */
    function exportEntreprise($company_id,$pdo){
        // Récupération du nom de l'entreprise
        $nomEntreprise = getCompanyName($pdo,$company_id);
        // Création du nom du fichier pdf à exporter 
        $nom = 'planning-'.$nomEntreprise.'.pdf';
        // Création d'un nouveau pdf et on lui ajoute une page
        $pdfEntreprise = new FPDF();
        $pdfEntreprise->AddPage();
        // Initialise la couleur des cadres 
        $pdfEntreprise->SetDrawColor(217,217,217);
        // Récupération des intervenants de l'entreprise
        $listeSpeaker = getSpeakersPerCompany($pdo, $company_id);
        $i = 0; 
        //Boucle sur les différents intervenants
        while($row1 = $listeSpeaker->fetch()){
            $i = $i+ 1;
            //Récupère les rendez-vous de l'intervenant
            $rendezVous = getAppointmentPerSpeaker($pdo,$row1["speaker_id"]);
            //Initialise la famille de la police, sa taille et son style
            $pdfEntreprise->SetFont('Arial','B',20);
            //Écrit le nom de l'entreprise en haut au centre, de la page puis passe à la ligne suivante
            $pdfEntreprise->Cell(0, 10, conversion($nomEntreprise), 0, 1, 'C');
            $pdfEntreprise->SetFont('Arial','B',11);
            //Écrit le nom de l'intervenant sous le nom de l'entreprise
            $pdfEntreprise->Cell(0, 10, conversion($row1["name"]), 0, 1, 'C');
            while($row2 = $rendezVous->fetch()){
                //Écrit l'heure de début et de fin du rendez-vous dans une cellule de 9,5 cm de large avec un cadre
                $pdfEntreprise->Cell(95, 10, $row2["start"].'-'.$row2["end"], 'LTBR', 0, 'C');
                $pdfEntreprise->SetFont('Arial','',11);
                //Change la couleur du texte 
                $pdfEntreprise->SetTextColor(139, 45, 45);
                //Écrit le nom de l'étudiant à la suite de l'autre cellule
                $pdfEntreprise->Cell(95, 10, conversion($row2["username"]) , 'LTBR', 1, 'C');
                //Remet le texte en noir
                $pdfEntreprise->SetTextColor(0 , 0, 0);
                $pdfEntreprise->SetFont('Arial','B',11);
            }
            //S'il ne s'agit pas du dernier intervenant de l'entreprise ajoute une nouvelle page
            if($i < $listeSpeaker->rowCount()){
                $pdfEntreprise->AddPage();
            }
        }
        // Téléchargement du pdf 
        $pdfEntreprise->Output($nom,'D');
    }

    // fonction qui convertit le texte passé en paramètre en un texte codé en windows-1252
    function conversion($texte){
        $res = iconv('UTF-8', 'windows-1252', $texte);
        return $res;
    }

    //Fonction qui exporte le planning d'un étudiant 
    function exportEtudiant($student_id,$pdo){
        // Récupération du nom de l'étudiant
        $nomEtudiant = getStudentName($pdo,$student_id);
        // Création du nom du fichier pdf à exporter 
        $nom = 'planning-'.$nomEtudiant.'.pdf';
        //Création du fichier pdf
        $pdfEtudiant = new FPDF();
        //ajout d'une page
        $pdfEtudiant->AddPage();
        //Initialise la famille de la police, sa taille et son style
        $pdfEtudiant->SetFont('Arial','B',20);
        //On écrit le nom de l'étudiant en haut de la page
        $pdfEtudiant->Cell(0, 10, conversion($nomEtudiant),0, 1, 'C');
        // Initialise la couleur des cadres 
        $pdfEtudiant->SetDrawColor(217,217,217);
        //Récupère le planning de l'utilisateur 
        $planning = planningPerUser($pdo, $student_id);
        foreach ($planning as $rdv) {
            //Mets le texte en gras et de taille 11
            $pdfEtudiant->SetFont('Arial','B',11);
            $pdfEtudiant->Cell(95, 10, $rdv['start'].'-'.$rdv['end'], 'LTBR', 0, 'C');
            //remet le texte normal
            $pdfEtudiant->SetFont('Arial','',11);
            //Change la couleur du texte
            $pdfEtudiant->SetTextColor(139, 45, 45);
            $pdfEtudiant->Cell(95, 10,conversion($rdv['company_name']) , 'LTBR', 0, 'C');
            //Remet le texte en noir
            $pdfEtudiant->SetTextColor(0 , 0, 0);
            //saut de ligne
            $pdfEtudiant->Ln();
        }
        //Récupère la liste des entreprise exclue que l'étudiant voulais voir
        $unlisted = unlistedCompanyPerUser($pdo, $student_id);
        //Regarde si l'étudiant à des entreprise exclues
        if ($unlisted->rowCount() > 0) {
            $pdfEtudiant->SetFont('Arial','B',20);
            //saut de ligne
            $pdfEtudiant->Ln();
            $pdfEtudiant->Cell(0, 10, conversion("Entreprises hors planning à aller voir"),0, 1, 'C');
            $pdfEtudiant->SetFont('Arial','',11);
            //Change la couleur du texte
            $pdfEtudiant->SetTextColor(139, 45, 45);
            //saut de ligne
            $pdfEtudiant->Ln();

            while($row = $unlisted->fetch()){
                //écrit le nom de l'entreprise exclue dans un cadre
                $pdfEtudiant->Cell(0, 10,conversion($row["name"]) , 'LTBR', 0, 'C');
            }
        }
        //Créer le pdf à l'aide du nom initialisé puis le télécharge 
        $pdfEtudiant->Output($nom,'D');
    }

    function exportEntrepriseExclu($company_id,$pdo){
        // Récupération du nom de l'entreprise
        $nomEntreprise = getCompanyName($pdo,$company_id);
        // Création du nom du fichier pdf à exporter 
        $nom = 'planning-'.$nomEntreprise.'.pdf';
        //Création du fichier pdf
        $pdfExclu = new FPDF();
         //ajout d'une page
        $pdfExclu->AddPage();
        //Initialise la famille de la police, sa taille et son style
        $pdfExclu->SetFont('Arial','B',20);
        //On écrit le nom de l'entreprise en haut de la page
        $pdfExclu->Cell(0, 10, conversion($nomEntreprise), 0, 1, 'C');
        // Initialise la couleur des cadres 
        $pdfExclu->SetDrawColor(217,217,217);
        //Récupère la liste des étudiants qui souhaité voir l'entreprise
        $listeEtudiant = studentByUnlistedCompany($pdo,$company_id);
        while($row = $listeEtudiant->fetch()){
            $pdfExclu->SetFont('Arial','',11);
            //Change la couleur du texte 
            $pdfExclu->SetTextColor(139, 45, 45);
            //ecrit dans une cellule le nom de l'étudiant
            $pdfExclu->Cell(95, 10,conversion($row["username"]) , 'LBTR', 0, 'C');
            //écrit dans une cellule à la suite de l'autre le nom de la fillière de l'étudiants
            $pdfExclu->Cell(95, 10,conversion($row["name"]) , 'LTBR', 0, 'C');
            //saut de ligne
            $pdfExclu->Ln();
        }
        //Créer le pdf à l'aide du nom initialisé puis le télécharge 
        $pdfExclu->Output($nom,'D');
    }

    function exportAllEntreprise($pdo){
        //Récupération de la liste des entreprise non exlues
        $ListeEntrepriseNonExclue = getCompanyNotExcluded($pdo);
        // Création du nom du fichier pdf à exporter 
        $nom = 'planningAllEntreprise.pdf';
        //Création du fichier pdf
        $pdfEntreprise = new FPDF();
        // Initialise la couleur des cadres 
        $pdfEntreprise->SetDrawColor(217,217,217);
        //Boucle sur les différentes entreprise
        while($ligne = $ListeEntrepriseNonExclue->fetch()){
            // Récupération des intervenants de l'entreprise
            $listeSpeaker = getSpeakersPerCompany($pdo, $ligne["company_id"]);
            //ajout d'une page
            $pdfEntreprise->AddPage();
            $i = 0;
            //Boucle sur les différents intervenants
            while($row1 = $listeSpeaker->fetch()){
                $i =$i+1;
                // Récupération des rendez-vous de l'intervenant
                $rendezVous = getAppointmentPerSpeaker($pdo,$row1["speaker_id"]);
                //Initialise la famille de la police, sa taille et son style
                $pdfEntreprise->SetFont('Arial','B',20);
                //Ecrit le nom de l'entreprise en haut de la page
                $pdfEntreprise->Cell(0, 10, conversion($ligne["name"]), 0, 1, 'C');
                $pdfEntreprise->SetFont('Arial','B',11);
                //Ecrit le nom de l'intervenant sous le nom de l'entreprise
                $pdfEntreprise->Cell(0, 10, conversion($row1["name"]), 0, 1, 'C');
                while($row2 = $rendezVous->fetch()){
                    //écrit l'heure de début et de fin du rendez vous dans une cellule de 95mm de large 
                    $pdfEntreprise->Cell(95, 10, $row2["start"].'-'.$row2["end"], 'LTBR', 0, 'C');
                    $pdfEntreprise->SetFont('Arial','',11);
                    //Change la couleur du texte 
                    $pdfEntreprise->SetTextColor(139, 45, 45);
                    //écrit le nom de l'étudiant à la suite de la cellule précédente
                    $pdfEntreprise->Cell(95, 10, conversion($row2["username"]) , 'LBTR', 0, 'C');
                    //Remet le texte en noir
                    $pdfEntreprise->SetTextColor(0 , 0, 0);
                    $pdfEntreprise->SetFont('Arial','B',11);
                    //saut de ligne
                    $pdfEntreprise->Ln();
                }
                //Si il ne s'agit pas du dernier intervenant ajoute une nouvelle page
                if($i < $listeSpeaker->rowCount()){
                    $pdfEntreprise->AddPage();
                }
            }

        }
        //Créer le pdf à l'aide du nom initialisé puis le télécharge 
        $pdfEntreprise->Output($nom,'D');
    }

    function exportAllEtudiant($pdo){
        //Récupération de la liste des etudiants
        $listeEtudiant = getStudent($pdo);
        //Création du fichier pdf
        $pdfEtudiant = new FPDF();
        // Initialise la couleur des cadres 
        $pdfEtudiant->SetDrawColor(217,217,217);
        // Création du nom du fichier pdf à exporter 
        $nom = 'planningAllEtudiant.pdf';
        while($ligne = $listeEtudiant->fetch()){ 
            $planning = planningPerUser($pdo,$ligne["user_id"]);
            if(count($planning)){
                 //ajout d'une page
                $pdfEtudiant->AddPage();
                //Initialise la famille de la police, sa taille et son style
                $pdfEtudiant->SetFont('Arial','B',20);
                //écrit le nom de l'étudiant en haut de la page
                $pdfEtudiant->Cell(0, 10,conversion($ligne["username"]),0, 1, 'C');
                foreach ($planning as $rdv) {
                    //Mets le texte en gras 
                    $pdfEtudiant->SetFont('Arial','B',11);
                    $pdfEtudiant->SetTextColor(0 , 0, 0);
                    //écrit l'heure du rendez-vous dans une cellule de 95mm
                    $pdfEtudiant->Cell(95, 10, $rdv['start'].'-'.$rdv['end'], 'LBTR', 0, 'C');
                    //Remet le texte normal
                    $pdfEtudiant->SetFont('Arial','',11);
                    //Change la couleur du texte 
                    $pdfEtudiant->SetTextColor(139, 45, 45);
                    //écrit le nom de l'entreprise à la suite de la cellule précédente
                    $pdfEtudiant->Cell(95, 10,conversion($rdv['company_name']) , 'LTBR', 0, 'C');
                    //saut de ligne
                    $pdfEtudiant->Ln();
                }
            }
            //Récupère la liste des entreprise exclue que l'étudiant souhaité voir
            $unlisted = unlistedCompanyPerUser($pdo, $ligne["user_id"]);
            if ($unlisted->rowCount() > 0) {
                //saut de ligne
                $pdfEtudiant->Ln();
                $pdfEtudiant->SetFont('Arial','B',20);
                //Remet le texte en noir
                $pdfEtudiant->SetTextColor(0, 0, 0);
                $pdfEtudiant->Cell(0, 10, conversion("Entreprises hors planning à aller voir"),0, 1, 'C');
                $pdfEtudiant->SetFont('Arial','',11);
                //Change la couleur du texte 
                $pdfEtudiant->SetTextColor(139, 45, 45);
                //saut de ligne
                $pdfEtudiant->Ln();

                while($row = $unlisted->fetch()){
                    //écrit le nom de l'entreprise dans un cadre 
                    $pdfEtudiant->Cell(0, 10,conversion($row["name"]) , 'LTBR', 0, 'C');
                }
            }
            //Remet le texte en noir
            $pdfEtudiant->SetTextColor(0 , 0, 0);
        }
        //Créer le pdf à l'aide du nom initialisé puis le télécharge 
        $pdfEtudiant->Output($nom,'D');
    }

    function exportAllEntrepriseExclu($pdo){
        //Récupération de la liste des entreprise exclues
        $ListeEntrepriseExclue = getCompanyExcluded($pdo);
        // Création du nom du fichier pdf à exporter 
        $nom = 'planningAllEntrepriseExclu.pdf';
        //Création du fichier pdf
        $pdfExclu = new FPDF();
        $pdfExclu->SetDrawColor(217,217,217);
        //On boucle sur la liste des entreprises exclues
        while($ligne = $ListeEntrepriseExclue->fetch()){
            //ajout d'une page
            $pdfExclu->AddPage();
            //Initialise la famille de la police, sa taille et son style
            $pdfExclu->SetFont('Arial','',20);
            //On remet le texte en noir
            $pdfExclu->SetTextColor(0 , 0, 0);
            $pdfExclu->Cell(0, 10,conversion($ligne["name"]), 0, 1, 'C');
            //Récupération de la liste des étudiants
            $listeEtudiant = studentByUnlistedCompany($pdo,$ligne["company_id"]);
            //Boucle sur les étudaint qui souhaite voir l'entreprise
            while($row = $listeEtudiant->fetch()){
                $pdfExclu->SetFont('Arial','',11);
                //Change la couleur du texte 
                $pdfExclu->SetTextColor(139, 45, 45);
                //ecrit dans un tableau le nom de l'étudiant et sa fillière
                $pdfExclu->Cell(95, 10,conversion($row["username"]) , 'LTBR', 0, 'C');
                $pdfExclu->Cell(95, 10,conversion($row["name"]) , 'LTBR', 0, 'C');
                //saut de ligne
                $pdfExclu->Ln();
            }
        }
        //Créer le pdf à l'aide du nom initialisé puis le télécharge 
        $pdfExclu->Output($nom,'D');
    }

    function importerEtudiants($fichier, $filieres, $pdo) {
        $fichier = fopen($fichier, "r");
        $etudiants = array();
        while (!feof($fichier)) {
            $ligne = fgets($fichier);
            $etudiant = explode(";", $ligne);
            $etudiants[] = $etudiant;
            $etudiants[count($etudiants) - 1][4] = trim($etudiants[count($etudiants) - 1][4]);
        }
        fclose($fichier);
        $message = "";
        foreach ($etudiants as $key => $etudiant) {
            if (count($etudiant) != 5) {
                $message .= "Le fichier ne contient pas le bon nombre de colonnes. Ligne ".($key + 1)."<br>";
            } elseif ($etudiant[0] == '' || $etudiant[1] == '' || $etudiant[2] == '' || $etudiant[3] == '' || $etudiant[4] == '') {
                $message .= "L'étudiant ".($key + 1)." n'est pas correct, certaines informations sont vides.<br>";
            }
            if (!preg_match("/^(?=.*\d)(?=.*[_\W]).{8,}$/", $etudiant[3])) {
                $message .= "Le mot de passe de l'étudiant ".($key + 1)." n'est pas correct.<br>";
            }
            if (!in_array($etudiant[4], array_keys($filieres))) {
                $message .= "La filière de l'étudiant ".($key + 1)." n'est pas correcte.<br>";
            }
        } 
        if ($message == "") {
            inserer_etudiants($pdo, $etudiants, $filieres);
            return "Importation réussie";
        } else {
            return $message;
        }
    }
?>