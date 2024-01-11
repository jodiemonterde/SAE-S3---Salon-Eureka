<?php
    require('lib/fpdf.php');
    require_once("baseDeDonnees.php");
    
    function exportEntreprise($company_id,$pdo){
        $nomEntreprise = getCompanyName($pdo,$company_id);
        $nom = 'planning-'.$nomEntreprise.'.pdf';
        $pdfEntreprise = new FPDF();
        $pdfEntreprise->AddPage();
        $pdfEntreprise->SetDrawColor(217,217,217);
        $listeSpeaker = getSpeakersPerCompany($pdo, $company_id);
        $i = 0; 
        while($row1 = $listeSpeaker->fetch()){
            $i = $i+ 1;
            $rendezVous = getAppointmentPerSpeaker($pdo,$row1["speaker_id"]);
            $pdfEntreprise->SetFont('Arial','B',20);
            $pdfEntreprise->Cell(0, 10, conversion($nomEntreprise), 0, 1, 'C');
            $pdfEntreprise->SetFont('Arial','B',11);
            $pdfEntreprise->Cell(0, 10, conversion($row1["name"]), 0, 1, 'C');
            while($row2 = $rendezVous->fetch()){
                $pdfEntreprise->Cell(95, 10, $row2["start"].'-'.$row2["end"], 'LTBR', 0, 'C');
                $pdfEntreprise->SetFont('Arial','',11);
                $pdfEntreprise->SetTextColor(139, 45, 45);
                $pdfEntreprise->Cell(95, 10, conversion($row2["username"]) , 'LTBR', 1, 'C');
                $pdfEntreprise->SetTextColor(0 , 0, 0);
                $pdfEntreprise->SetFont('Arial','B',11);
            }
            if($i < $listeSpeaker->rowCount()){
                $pdfEntreprise->AddPage();
            }
        }
        $pdfEntreprise->Output($nom,'D');
    }

    function conversion($texte){
        $res = iconv('UTF-8', 'windows-1252', $texte);
        return $res;
    }

    function exportEtudiant($student_id,$pdo){
        $nomEtudiant = getStudentName($pdo,$student_id);
        $nom = 'planning-'.$nomEtudiant.'.pdf';
        $pdfEtudiant = new FPDF();
        $pdfEtudiant->AddPage();
        $pdfEtudiant->SetFont('Arial','B',20);
        $pdfEtudiant->Cell(0, 10, conversion($nomEtudiant),0, 1, 'C');
        $pdfEtudiant->SetDrawColor(217,217,217);
        $planning = planningPerUser($pdo, $student_id);
        foreach ($planning as $rdv) {
            $pdfEtudiant->SetFont('Arial','B',11);
            $pdfEtudiant->Cell(95, 10, $rdv['start'].'-'.$rdv['end'], 'LTBR', 0, 'C');
            $pdfEtudiant->SetFont('Arial','',11);
            $pdfEtudiant->SetTextColor(139, 45, 45);
            $pdfEtudiant->Cell(95, 10,conversion($rdv['company_name']) , 'LTBR', 0, 'C');
            $pdfEtudiant->SetTextColor(0 , 0, 0);
            $pdfEtudiant->Ln();
        }
        $unlisted = unlistedCompanyPerUser($pdo, $student_id);
        if ($unlisted->rowCount() > 0) {
            $pdfEtudiant->SetFont('Arial','B',20);
            $pdfEtudiant->Ln();
            $pdfEtudiant->Cell(0, 10, conversion("Entreprises hors planning à aller voir"),0, 1, 'C');
            $pdfEtudiant->SetFont('Arial','',11);
            $pdfEtudiant->SetTextColor(139, 45, 45);
            $pdfEtudiant->Ln();

            while($row = $unlisted->fetch()){
                $pdfEtudiant->Cell(0, 10,conversion($row["name"]) , 'LTBR', 0, 'C');
            }
        }
        $pdfEtudiant->Output($nom,'D');
    }

    function exportEntrepriseExclu($company_id,$pdo){
        $nomEntreprise = getCompanyName($pdo,$company_id);
        $nom = 'planning-'.$nomEntreprise.'.pdf';
        $pdfExclu = new FPDF();
        $pdfExclu->AddPage();
        $pdfExclu->SetFont('Arial','B',20);
        $pdfExclu->Cell(0, 10, conversion($nomEntreprise), 0, 1, 'C');
        $pdfExclu->SetDrawColor(217,217,217);
        $listeEtudiant = studentByUnlistedCompany($pdo,$company_id);
        while($row = $listeEtudiant->fetch()){
            $pdfExclu->SetFont('Arial','',11);
            $pdfExclu->SetTextColor(139, 45, 45);
            $pdfExclu->Cell(95, 10,conversion($row["username"]) , 'LBTR', 0, 'C');
            $pdfExclu->Cell(95, 10,conversion($row["name"]) , 'LTBR', 0, 'C');
            $pdfExclu->Ln();
        }
        $pdfExclu->Output($nom,'D');
    }

    function exportAllEntreprise($pdo){
        $ListeEntrepriseNonExclue = getCompanyNotExcluded($pdo);
        $nom = 'planningAllEntreprise.pdf';
        $pdfEntreprise = new FPDF();
        $pdfEntreprise->SetDrawColor(217,217,217);
        while($ligne = $ListeEntrepriseNonExclue->fetch()){
            $listeSpeaker = getSpeakersPerCompany($pdo, $ligne["company_id"]);
            $pdfEntreprise->AddPage();
            $i = 0;
            while($row1 = $listeSpeaker->fetch()){
                $i =$i+1;
                $rendezVous = getAppointmentPerSpeaker($pdo,$row1["speaker_id"]);
                $pdfEntreprise->SetFont('Arial','B',20);
                $pdfEntreprise->Cell(0, 10, conversion($ligne["name"]), 0, 1, 'C');
                $pdfEntreprise->SetFont('Arial','B',11);
                $pdfEntreprise->Cell(0, 10, conversion($row1["name"]), 0, 1, 'C');
                while($row2 = $rendezVous->fetch()){
                    $pdfEntreprise->Cell(95, 10, $row2["start"].'-'.$row2["end"], 'LTBR', 0, 'C');
                    $pdfEntreprise->SetFont('Arial','',11);
                    $pdfEntreprise->SetTextColor(139, 45, 45);
                    $pdfEntreprise->Cell(95, 10, conversion($row2["username"]) , 'LBTR', 0, 'C');
                    $pdfEntreprise->SetTextColor(0 , 0, 0);
                    $pdfEntreprise->SetFont('Arial','B',11);
                    $pdfEntreprise->Ln();
                }
                if($i < $listeSpeaker->rowCount()){
                    $pdfEntreprise->AddPage();
                }
            }

        }
        $pdfEntreprise->Output($nom,'D');
    }

    function exportAllEtudiant($pdo){
        $listeEtudiant = getStudent($pdo);
        $pdfEtudiant = new FPDF();
        $pdfEtudiant->SetDrawColor(217,217,217);
        $nom = 'planningAllEtudiant.pdf';
        while($ligne = $listeEtudiant->fetch()){ 
            $planning = planningPerUser($pdo,$ligne["user_id"]);
            if(count($planning)){
                $pdfEtudiant->AddPage();
                $pdfEtudiant->SetFont('Arial','B',20);
                $pdfEtudiant->Cell(0, 10,conversion($ligne["username"]),0, 1, 'C');
                foreach ($planning as $rdv) {
                    $pdfEtudiant->SetFont('Arial','B',11);
                    $pdfEtudiant->SetTextColor(0 , 0, 0);
                    $pdfEtudiant->Cell(95, 10, $rdv['start'].'-'.$rdv['end'], 'LBTR', 0, 'C');
                    $pdfEtudiant->SetFont('Arial','',11);
                    $pdfEtudiant->SetTextColor(139, 45, 45);
                    $pdfEtudiant->Cell(95, 10,conversion($rdv['company_name']) , 'LTBR', 0, 'C');
                    $pdfEtudiant->Ln();
                }
            }
            $unlisted = unlistedCompanyPerUser($pdo, $ligne["user_id"]);
            if ($unlisted->rowCount() > 0) {
                $pdfEtudiant->Ln();
                $pdfEtudiant->SetFont('Arial','B',20);
                $pdfEtudiant->SetTextColor(0, 0, 0);
                $pdfEtudiant->Cell(0, 10, conversion("Entreprises hors planning à aller voir"),0, 1, 'C');
                $pdfEtudiant->SetFont('Arial','',11);
                $pdfEtudiant->SetTextColor(139, 45, 45);
                $pdfEtudiant->Ln();

                while($row = $unlisted->fetch()){
                    $pdfEtudiant->Cell(0, 10,conversion($row["name"]) , 'LTBR', 0, 'C');
                }
            }
            $pdfEtudiant->SetTextColor(0 , 0, 0);
        }
        $pdfEtudiant->Output($nom,'D');
    }

    function exportAllEntrepriseExclu($pdo){
        $ListeEntrepriseExclue = getCompanyExcluded($pdo);
        $nom = 'planningAllEntrepriseExclu.pdf';
        $pdfExclu = new FPDF();
        $pdfExclu->SetDrawColor(217,217,217);
        while($ligne = $ListeEntrepriseExclue->fetch()){
            $pdfExclu->AddPage();
            $pdfExclu->SetFont('Arial','',20);
            $pdfExclu->SetTextColor(0 , 0, 0);
            $pdfExclu->Cell(0, 10,conversion($ligne["name"]), 0, 1, 'C');
            $listeEtudiant = studentByUnlistedCompany($pdo,$ligne["company_id"]);
            while($row = $listeEtudiant->fetch()){
                $pdfExclu->SetFont('Arial','',11);
                $pdfExclu->SetTextColor(139, 45, 45);
                $pdfExclu->Cell(95, 10,conversion($row["username"]) , 'LTBR', 0, 'C');
                $pdfExclu->Cell(95, 10,conversion($row["name"]) , 'LTBR', 0, 'C');
                $pdfExclu->Ln();
            }
        }
        $pdfExclu->Output($nom,'D');
    }

    function importerEtudiants($fichier, $filieres, $pdo) {
        $fichier = fopen($fichier, "r");
        $etudiants = array();
        while (!feof($fichier)) {
            $ligne = fgets($fichier);
            $etudiant = explode(";", $ligne);
            $etudiants[] = $etudiant;
            $etudiants[count($etudiants) - 1][3] = trim($etudiants[count($etudiants) - 1][3]);
        }
        fclose($fichier);
        $message = "";
        foreach ($etudiants as $key => $etudiant) {
            echo "|".$etudiant[3]."|";
            if (count($etudiant) != 4) {
                $message .= "Le fichier ne contient pas le bon nombre de colonnes. Ligne ".($key + 1)."<br>";
            } elseif ($etudiant[0] == '' || $etudiant[1] == '' || $etudiant[2] == '' || $etudiant[3] == '') {
                $message .= "L'étudiant ".($key + 1)." n'est pas correct, certaines informations sont vides.<br>";
            }
            if (!preg_match("/^(?=.*\d)(?=.*[_\W]).{8,}$/", $etudiant[2])) {
                $message .= "Le mot de passe de l'étudiant ".($key + 1)." n'est pas correct.<br>";
            }
            if (!in_array($etudiant[3], array_keys($filieres))) {
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