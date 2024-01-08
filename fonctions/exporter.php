<?php
    require('lib/fpdf.php');
    require_once("baseDeDonnees.php");
    
    function exportEntreprise($company_id,$pdo){
        $nom = 'planningEntreprise.pdf';
        $pdfEntreprise = new FPDF();
        $pdfEntreprise->AddPage();
        $pdfEntreprise->SetDrawColor(217,217,217);
        $planningEntreprise= getStudentsAppointmentsPerCompany($pdo, $company_id, 0);
        while($row = $planningEntreprise->fetch()){
            $pdfEntreprise->SetFont('Arial','B',11);
            $pdfEntreprise->Cell(0, 10, $row["start"].'-'.$row["duration"], 'LTR', 1, 'C');
            $pdfEntreprise->SetFont('Arial','',11);
            $pdfEntreprise->SetTextColor(255 , 168, 0);
            $pdfEntreprise->Cell(0, 10,$row["name"] , 'LR', 1, 'C');
            $pdfEntreprise->Cell(0, 10,$row["username"] , 'LBR', 1, 'C');
            $pdfEntreprise->SetTextColor(0 , 0, 0);
            $pdfEntreprise->Ln();
        }
        $pdfEntreprise->Output($nom,'D');
    }
?>