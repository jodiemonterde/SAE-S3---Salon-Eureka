<?php
    require('lib/fpdf.php');
    require_once("baseDeDonnees.php");
    
    function exportEntreprise($company_id,$pdo){
        $nomEntreprise = getCompanyName($pdo,$company_id);
        $nom = 'planning'.$nomEntreprise.'.pdf';
        $pdfEntreprise = new FPDF();
        $pdfEntreprise->AddPage();
        $pdfEntreprise->SetFont('Arial','B',20);
        $pdfEntreprise->Cell(0, 10, $nomEntreprise, 0, 1, 'C');
        $pdfEntreprise->SetDrawColor(217,217,217);
        $listeSpeaker = getSpeakersPerCompany($pdo, $company_id);
        while($row1 = $listeSpeaker->fetch()){
            $rendezVous = getAppointmentPerSpeaker($pdo,$row1["speaker_id"]);
            $pdfEntreprise->SetFont('Arial','B',11);
            $pdfEntreprise->Cell(0, 10, $row1["name"], 0, 1, 'C');
            while($row2 = $rendezVous->fetch()){
                $pdfEntreprise->Cell(0, 10, $row2["start"].'-'.$row2["end"], 'LTR', 1, 'C');
                $pdfEntreprise->SetFont('Arial','',11);
                $pdfEntreprise->SetTextColor(255 , 168, 0);
                $pdfEntreprise->Cell(0, 10,$row2["username"] , 'LBR', 1, 'C');
                $pdfEntreprise->SetTextColor(0 , 0, 0);
                $pdfEntreprise->SetFont('Arial','B',11);
                $pdfEntreprise->Ln();
            }
        }
        $pdfEntreprise->Output($nom,'D');
    }

    function exportEtudiant($student_id,$pdo){
        $nom = 'planningEtudiant.pdf';
        $pdfEtudiant = new FPDF();
        $pdfEtudiant->AddPage();
        $pdfEtudiant->SetDrawColor(217,217,217);
        $planning = planningPerUser($pdo,$student_id)
       foreach($planning as $rdv){
            $pdfEtudiant->SetFont('Arial','B',11);
            $pdfEtudiant->Cell(0, 10, $rdv["start"].'-'.$rdv["end"], 'LTR', 1, 'C');
            $pdfEtudiant->SetFont('Arial','',11);
            $pdfEtudiant->SetTextColor(255 , 168, 0);
            $pdfEtudiant->Cell(0, 10,$rdv["company_name"] , 'LBR', 1, 'C');
            $pdfEtudiant->SetTextColor(0 , 0, 0);
            $pdfEtudiant->Ln();
        }
        $pdfEntreprise->Output($nom,'D');
    }
?>