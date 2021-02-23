<?php
namespace App\Service;

use Qipsius\TCPDFBundle\Controller\TCPDFController;

class PDFService
{
    private $tcpdf;

    public function __construct(TCPDFController $pdf) 
    {
        $this->tcpdf = $pdf;
    }
    
    public function bulletin($concours,$user,$echantillons){
        $pdf = $this->tcpdf->create('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jean-Michel Duriez');
        $pdf->SetTitle('Bulletin inscription Concours');
        $pdf->SetSubject('Concours');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);


        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/fr.php')) {
            require_once(dirname(__FILE__).'/lang/fr.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $pdf->SetFont('dejavusans', '', 12, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

        // Image example with resizing
        $pdf->Image('images/logo_cto-154x100.png', 10, 10, 15, 10, '', 'https://concours.ctolivier.org', '', true, 150, '', false, false, 0, false, false, false);
        $pdf->Image('images/logoFDB.png', 250, 10, 25, 15, '', 'https://concours.ctolivier.org', '', true, 150, '', false, false, 0, false, false, false);

        //titre
        $pdf->SetXY(1,15);
        $pdf->SetFont('dejavusans', 'B', 12, '', true);

        //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

        $pdf->Write(5,"Bulletin de participation au Concours ".$concours->getName(),'',false,'C',1);
        $pdf->Write(5,"",'',false,'C',1);
        $pdf->Write(5,"",'',false,'C',1);
        $pdf->Write(5,"Candidat ",'',false,'L',1);
        $pdf->SetFont('dejavusans', '', 10, '', true);

        if($user->getProfil()->getRaisonSociale()){
            $pdf->Write(5,$user->getProfil()->getRaisonSociale(),'',false,'L',1);
        }
        if($user->getProfil()->getNom()){
            $pdf->Write(5,$user->getProfil()->getNom().' '.$user->getProfil()->getPrenom(),'',false,'L',1);
        }
        if($user->getProfil()->getAdress1()){
            $pdf->Write(5,$user->getProfil()->getAdress1(),'',false,'L',1);
        }
        if($user->getProfil()->getAdress2()){
            $pdf->Write(5,$user->getProfil()->getAdress2(),'',false,'L',1);
        }
        if($user->getProfil()->getAdress3()){
            $pdf->Write(5,$user->getProfil()->getAdress3(),'',false,'L',1);
        }
        if($user->getProfil()->getAdress4()){
            $pdf->Write(5,$user->getProfil()->getAdress4(),'',false,'L',1);
        }
        if($user->getProfil()->getAdress5()){
            $pdf->Write(5,$user->getProfil()->getAdress5(),'',false,'L',1);
        }
        if($user->getProfil()->getPhone()){
            $pdf->Write(5,'Tél : '.$user->getProfil()->getPhone(),'',false,'L',1);
        }

        // échantillons
        $pdf->Write(5,"",'',false,'C',1);

        $pdf->SetFont('dejavusans', 'B', 10, '', true);
        $pdf->Cell(270,10,'Échantillons présentés au Concours',1,1,'C');
        $pdf->Cell(100,7,'Catégorie',1,0,'C');
        $pdf->Cell(30,7,'Numéro de lot',1,0,'C');
        $pdf->Cell(30,7,'Volume',1,0,'C');
        $pdf->Cell(110,7,'Description',1,1,'C');


        $pdf->SetFont('dejavusans', '', 10, '', true);
        foreach($echantillons as $e){
            $pdf->MultiCell(100,10,$e->getCategorie()->getName(),1,'C',0,0,'','',true,0,false,true,0,'M',true);
            $pdf->Cell(30,10,$e->getLot(),1,0,'C');
            $pdf->Cell(30,10,$e->getVolume().' '.$e->getCategorie()->getType()->getUnite(),1,0,'C');
            $pdf->MultiCell(110,10,$e->getDescription(),1,'C',0,1,'','',true,0,false,true,0,'M',true);
        }

        $HT = $concours->getCout() * count($echantillons);
        $TVA = $concours->getTVA() * $HT / 100;
        $TTC = count($echantillons) * 20;

        $pdf->Write(5,"",'',false,'C',1);
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->Cell(270,7,'Frais de participation : '.count($echantillons).' échantillons X '.$concours->getCout().' = '.$HT.'€ HT + '.$TVA.'€ de TVA à '.$concours->getTVA().'% = '.$TTC.' € TTC',1,1,'C');







// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');
$pdf->Output($_SERVER['DOCUMENT_ROOT'].'bulletin_inscription_concours'.$user->getId().'.pdf', 'F');

    }
   
}