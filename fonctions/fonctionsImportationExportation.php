<?php
    /* fichiers nécessaire au fonctionnements des différentes fonctions  */
    require('lib/fpdf.php'); // librairies utilisé pour créer des pdf
    require_once("baseDeDonnees.php");
    
    /* fonction qui exporte le planning d'une entreprise */
    function exportEntreprise($company_id,$pdo){
        // Récupération du nom de l'entreprise
        $nomEntreprise = getCompanyName($pdo,$company_id);
        $nom = 'planning-'.htmlspecialchars_decode($nomEntreprise).'.pdf';
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
            // ecrit le nom de l'entreprise en haut de la page 
            $pdfEntreprise->Cell(0, 10, conversion(htmlspecialchars_decode($nomEntreprise)), 0, 1, 'C');
            $pdfEntreprise->SetFont('Arial','B',11);
            // ecrit le nom de l'intervenant 
            $pdfEntreprise->Cell(0, 10, conversion(htmlspecialchars_decode($row1["name"])), 0, 1, 'C');
            // Remet les cadres en noir 
            $pdfEntreprise->SetDrawColor(0,0,0);   
            // créer une cellule vide pour mettre une ligne noir en haut
            $pdfEntreprise->Cell(185, 0, "", 'T', 0, 'C');
            // Remet les cadres à leur valeur précédente
            $pdfEntreprise->SetDrawColor(217,217,217);
            $pdfEntreprise->Ln();
            $j = 0;
            while($row2 = $rendezVous->fetch()){
                if ($j == 12) {
                    $j = 0;
                    //Rajoute une page 
                    $pdfEntreprise->AddPage();
                    // Remet les cadres en noir 
                    $pdfEntreprise->SetDrawColor(0,0,0);
                    $pdfEntreprise->Cell(185, 0, "", 'T', 0, 'C');
                    // Remet les cadres à leur valeur précédente
                    $pdfEntreprise->SetDrawColor(217,217,217);
                    $pdfEntreprise->Ln();
                }
                // Ecrit l'heure de début et de fin des rende-vous 
                $pdfEntreprise->Cell(30, 20, $row2["start"].'-'.$row2["end"], 'LR', 0, 'C');
                $pdfEntreprise->SetFont('Arial','',11);
                $pdfEntreprise->SetTextColor(139, 45, 45);
                //Ecrit le nom et le prénom de l'étudiant 
                $pdfEntreprise->Cell(155, 10, conversion(htmlspecialchars_decode($row2["firstname"]." ".$row2["lastname"])) , 'LBR', 0, 'C');
                //Saute une ligne 
                $pdfEntreprise->Ln();
                $pdfEntreprise->Cell(30, 10, "", '', 0, 'C');
                //Met le texte en rouge 
                $pdfEntreprise->SetTextColor(139, 45, 45);
                //Ecrit l'adresse mail de l'étudiant
                $pdfEntreprise->Cell(155, 10, conversion(htmlspecialchars_decode($row2['email'])) , 'LTR', 0, 'C');
                //Remet le text en noir
                $pdfEntreprise->SetTextColor(0 , 0, 0);
                $pdfEntreprise->SetFont('Arial','B',11);
                $pdfEntreprise->Ln();
                //Met le texte en noir
                $pdfEntreprise->SetDrawColor(0,0,0);
                $pdfEntreprise->Cell(185, 0, "", 'T', 0, 'C');
                // Remet les cadres à leur valeur précédente
                $pdfEntreprise->SetDrawColor(217,217,217);
                $pdfEntreprise->Ln();
                $j = $j+1;
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
        $nom = 'planning-'.htmlspecialchars_decode($nomEtudiant).'.pdf';
        $pdfEtudiant = new FPDF();
        //ajout d'une page
        $pdfEtudiant->AddPage();
        //Initialise la famille de la police, sa taille et son style
        $pdfEtudiant->SetFont('Arial','B',20);
        $pdfEtudiant->Cell(0, 10, conversion(htmlspecialchars_decode($nomEtudiant)),0, 1, 'C');
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
            $pdfEtudiant->Cell(95, 10,conversion(htmlspecialchars_decode($rdv['company_name'])) , 'LTBR', 0, 'C');
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
                $pdfEtudiant->Cell(0, 10,conversion(htmlspecialchars_decode($row["name"])) , 'LTBR', 0, 'C');
            }
        }
        //Créer le pdf à l'aide du nom initialisé puis le télécharge 
        $pdfEtudiant->Output($nom,'D');
    }

    function exportEntrepriseExclu($company_id,$pdo){
        // Récupération du nom de l'entreprise
        $nomEntreprise = getCompanyName($pdo,$company_id);
        $nom = 'planning-'.htmlspecialchars_decode($nomEntreprise).'.pdf';
        $pdfExclu = new FPDF();
         //ajout d'une page
        $pdfExclu->AddPage();
        //Initialise la famille de la police, sa taille et son style
        $pdfExclu->SetFont('Arial','B',20);
        $pdfExclu->Cell(0, 10, conversion(htmlspecialchars_decode($nomEntreprise)), 0, 1, 'C');
        $pdfExclu->SetDrawColor(217,217,217);
        $listeEtudiant = getStudentsPerCompanyWishList($pdo,$company_id);
        while($row = $listeEtudiant->fetch()){
            $pdfExclu->SetFont('Arial','',11);
            //Change la couleur du texte 
            $pdfExclu->SetTextColor(139, 45, 45);
            //Ecrit nom de l'étudiant et son prénom
            $pdfExclu->Cell(95, 10,conversion(htmlspecialchars_decode($row["firstname"]." ".$row["lastname"])) , 'LBTR', 0, 'C');
            //Ecrit sa fillière 
            $pdfExclu->Cell(95, 10,conversion(htmlspecialchars_decode($row["name"])) , 'LTBR', 0, 'C');
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
                $pdfEntreprise->Cell(0, 10, conversion(htmlspecialchars_decode($ligne["name"])), 0, 1, 'C');
                $pdfEntreprise->SetFont('Arial','B',11);
                $pdfEntreprise->Cell(0, 10, conversion(htmlspecialchars_decode($row1["name"])), 0, 1, 'C');
                //Met la couleur des cadre en noir
                $pdfEntreprise->SetDrawColor(0,0,0);
                $pdfEntreprise->Cell(185, 0, "", 'T', 0, 'C');
                // Remet les cadres à leur valeur précédente
                $pdfEntreprise->SetDrawColor(217,217,217);
                $pdfEntreprise->Ln();
                $j = 0;
                while($row2 = $rendezVous->fetch()){
                    if ($j == 12) {
                        $j = 0;
                        //Ajout d'une page
                        $pdfEntreprise->AddPage();
                        //Met les cadres en noir
                        $pdfEntreprise->SetDrawColor(0,0,0);
                        $pdfEntreprise->Cell(185, 0, "", 'T', 0, 'C');
                        // Remet les cadres à leur valeur précédente
                        $pdfEntreprise->SetDrawColor(217,217,217);
                        $pdfEntreprise->Ln();
                    }
                    //Ecrit l'heure de début et de fin du rendez-vous
                    $pdfEntreprise->Cell(30, 20, $row2["start"].'-'.$row2["end"], 'LR', 0, 'C');
                    $pdfEntreprise->SetFont('Arial','',11);
                    //Met le texte en rouge 
                    $pdfEntreprise->SetTextColor(139, 45, 45);
                    //Ecrit le prénom et le nom de l'étudiant 
                    $pdfEntreprise->Cell(155, 10, conversion(htmlspecialchars_decode($row2["firstname"]." ".$row2["lastname"])) , 'LBR', 0, 'C');
                    //Saut de page 
                    $pdfEntreprise->Ln();
                    $pdfEntreprise->Cell(30, 10, "", '', 0, 'C');
                    //Met le texte en rouge 
                    $pdfEntreprise->SetTextColor(139, 45, 45);
                    //Ecrit l'adresse mail de l'étudiant 
                    $pdfEntreprise->Cell(155, 10, conversion(htmlspecialchars_decode($row2['email'])) , 'LTR', 0, 'C');
                    //Met le texte en noir
                    $pdfEntreprise->SetTextColor(0 , 0, 0);
                    $pdfEntreprise->SetFont('Arial','B',11);
                    $pdfEntreprise->Ln();
                    //Met les cadres en noir
                    $pdfEntreprise->SetDrawColor(0,0,0);
                    $pdfEntreprise->Cell(185, 0, "", 'T', 0, 'C');
                    // Remet les cadres à leur valeur précédente
                    $pdfEntreprise->SetDrawColor(217,217,217);
                    $pdfEntreprise->Ln();
                    $j = $j+1;
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
                $pdfEtudiant->Cell(0, 10,conversion(htmlspecialchars_decode($ligne["firstname"]." ".$ligne["lastname"])),0, 1, 'C');
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
                    $pdfEtudiant->Cell(95, 10,conversion(htmlspecialchars_decode($rdv['company_name'])) , 'LTBR', 0, 'C');
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
                    $pdfEtudiant->Cell(0, 10,conversion(htmlspecialchars_decode($row["name"])) , 'LTBR', 0, 'C');
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
            $pdfExclu->Cell(0, 10,conversion(htmlspecialchars_decode($ligne["name"])), 0, 1, 'C');
            $listeEtudiant = getStudentsPerCompanyWishList($pdo,$ligne["company_id"]);
            while($row = $listeEtudiant->fetch()){
                $pdfExclu->SetFont('Arial','',11);
                //Change la couleur du texte 
                $pdfExclu->SetTextColor(139, 45, 45);
                $pdfExclu->Cell(95, 10,conversion(htmlspecialchars_decode($row["firstname"]." ".$row["lastname"])) , 'LTBR', 0, 'C');
                $pdfExclu->Cell(95, 10,conversion(htmlspecialchars_decode($row["name"])) , 'LTBR', 0, 'C');
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