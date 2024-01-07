<?php
    require("C:/sae/UWamp-dev-cote-serv_2023_Vide_PHP_8_OK/www/GitHub/outils/fpdf/fpdf.php");
    require_once("baseDeDonnees.php");
    
    function exportEntreprise($company_id,$pdo){
        $nom = 'planningEntreprise.pdf';
        $pdfEntreprise = new FPDF();
        $pdfEntreprise->AddPage();
        $pdfEntreprise->SetFont('Arial','',11);
        $planningEntreprise= getStudentsAppointmentsPerCompany($pdo, $company_id, 0);
        while($row = $planningEntreprise->fetch()){
            $pdfEntreprise->SetFont('Arial','B',11);
            $pdfEntreprise->Cell(0, 10, $row["start"].'-'.$row["duration"], 'LTR', 1, 'C');
            $pdfEntreprise->SetFont('Arial','',11);
            $pdfEntreprise->Cell(0, 10,$row["name"] , 'LR', 1, 'C');
            $pdfEntreprise->Cell(0, 10,$row["username"] , 'LBR', 1, 'C');
            $pdfEntreprise->Ln();
        }
        $pdfEntreprise->Output($nom,'D');
    }
?>