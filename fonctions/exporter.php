<?php
    require('lib/fpdf.php');
    require_once("baseDeDonnees.php");
    
    function exportEntreprise($company_id,$pdo){
        $nomEntreprise = getCompanyName($pdo,$company_id);
        $nom = 'planning-'.$nomEntreprise.'.pdf';
        $pdfEntreprise = new FPDF();
        $pdfEntreprise->AddPage();
        $pdfEntreprise->SetFont('Arial','B',20);
        $pdfEntreprise->Cell(0, 10, $nomEntreprise, 0, 1, 'C');
        $pdfEntreprise->SetDrawColor(217,217,217);
        $listeSpeaker = getSpeakersPerCompany($pdo, $company_id);
        while($row1 = $listeSpeaker->fetch()){
            $rendezVous = getAppointmentPerSpeaker($pdo,$row1["speaker_id"]);
            $pdfEntreprise->SetFont('Arial','B',11);
            $pdfEntreprise->Cell(0, 10, conversion($row1["name"]), 0, 1, 'C');
            while($row2 = $rendezVous->fetch()){
                $pdfEntreprise->Cell(0, 10, $row2["start"].'-'.$row2["end"], 'LTR', 1, 'C');
                $pdfEntreprise->SetFont('Arial','',11);
                $pdfEntreprise->SetTextColor(255 , 168, 0);
                $pdfEntreprise->Cell(0, 10, conversion($row2["username"]) , 'LBR', 1, 'C');
                $pdfEntreprise->SetTextColor(0 , 0, 0);
                $pdfEntreprise->SetFont('Arial','B',11);
                $pdfEntreprise->Ln();
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
        $pdfEtudiant->Cell(0, 10, $nomEtudiant,0, 1, 'C');
        $pdfEtudiant->SetDrawColor(217,217,217);
        $planning = planningPerUser($pdo,$student_id);
        foreach ($planning as $rdv) {
            $pdfEtudiant->SetFont('Arial','B',11);
            $pdfEtudiant->Cell(0, 10, $rdv['start'].'-'.$rdv['end'], 'LTR', 1, 'C');
            $pdfEtudiant->SetFont('Arial','',11);
            $pdfEtudiant->SetTextColor(255 , 168, 0);
            $pdfEtudiant->Cell(0, 10,$rdv['company_name'] , 'LBR', 1, 'C');
            $pdfEtudiant->SetTextColor(0 , 0, 0);
            $pdfEtudiant->Ln();
        }
        $pdfEtudiant->Output($nom,'D');
    }

    function exportEntrepriseExclu($company_id,$pdo){
        $nomEntreprise = getCompanyName($pdo,$company_id);
        $nom = 'planning-'.$nomEntreprise.'.pdf';
        $pdfExclu = new FPDF();
        $pdfExclu->AddPage();
        $pdfExclu->SetDrawColor(217,217,217);
        $listeEtudiant = studentByUnlistedCompany($pdo,$company_id);
        while($row = $listeEtudiant->fetch()){
            $pdfExclu->SetFont('Arial','',11);
            $pdfExclu->SetTextColor(255 , 168, 0);
            $pdfExclu->Cell(0, 10,$row["username"] , 'LTR', 1, 'C');
            $pdfExclu->Cell(0, 10,$row["name"] , 'LBR', 1, 'C');
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
            $pdfEntreprise->AddPage();
            $pdfEntreprise->SetFont('Arial','B',20);
            $pdfEntreprise->Cell(0, 10, $ligne["name"], 0, 1, 'C');
            $listeSpeaker = getSpeakersPerCompany($pdo, $ligne["company_id"]);
            while($row1 = $listeSpeaker->fetch()){
                $rendezVous = getAppointmentPerSpeaker($pdo,$row1["speaker_id"]);
                $pdfEntreprise->SetFont('Arial','B',11);
                $pdfEntreprise->Cell(0, 10, conversion($row1["name"]), 0, 1, 'C');
                while($row2 = $rendezVous->fetch()){
                    $pdfEntreprise->Cell(0, 10, $row2["start"].'-'.$row2["end"], 'LTR', 1, 'C');
                    $pdfEntreprise->SetFont('Arial','',11);
                    $pdfEntreprise->SetTextColor(255 , 168, 0);
                    $pdfEntreprise->Cell(0, 10, conversion($row2["username"]) , 'LBR', 1, 'C');
                    $pdfEntreprise->SetTextColor(0 , 0, 0);
                    $pdfEntreprise->SetFont('Arial','B',11);
                    $pdfEntreprise->Ln();
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
            $pdfEtudiant->AddPage();
            $pdfEtudiant->SetFont('Arial','B',20);
            $pdfEtudiant->Cell(0, 10,$ligne["username"],0, 1, 'C');
            $planning = planningPerUser($pdo,$ligne["user_id"]);
            foreach ($planning as $rdv) {
                $pdfEtudiant->SetFont('Arial','B',11);
                $pdfEtudiant->Cell(0, 10, $rdv['start'].'-'.$rdv['end'], 'LTR', 1, 'C');
                $pdfEtudiant->SetFont('Arial','',11);
                $pdfEtudiant->SetTextColor(255 , 168, 0);
                $pdfEtudiant->Cell(0, 10,$rdv['company_name'] , 'LBR', 1, 'C');
                $pdfEtudiant->SetTextColor(0 , 0, 0);
                $pdfEtudiant->Ln();
            }
        }
        $pdfEtudiant->Output($nom,'D');
    }

    function exportAllEntrepriseExclu($pdo){
        $ListeEntrepriseExclue = getCompanyExcluded($pdo);
        if(count($listeEntreprise) != 0){
            $nom = 'planningAllEntrepriseExclu.pdf';
            $pdfExclu = new FPDF();
            $pdfExclu->SetDrawColor(217,217,217);
            while($ligne = $ListeEntrepriseExclue->fetch()){
                $pdfExclu->AddPage();
                $pdfExclu->SetFont('Arial','',20);
                $pdfEtudiant->SetTextColor(0 , 0, 0);
                $pdfExclu->Cell(0, 10,$ligne["name"], 0, 1, 'C');
                $listeEtudiant = studentByUnlistedCompany($pdo,$ligne["company_id"]);
                while($row = $listeEtudiant->fetch()){
                    $pdfExclu->SetFont('Arial','',11);
                    $pdfExclu->SetTextColor(255 , 168, 0);
                    $pdfExclu->Cell(0, 10,$row["username"] , 'LTR', 1, 'C');
                    $pdfExclu->Cell(0, 10,$row["name"] , 'LBR', 1, 'C');
                    $pdfExclu->Ln();
                }
            }
            $pdfExclu->Output($nom,'D');
        }
    }

?>