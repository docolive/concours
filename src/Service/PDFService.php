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

        $pdf->MultiCell(190,10,"Bulletin de participation au Concours ".$concours->getName(),$border=1,$align='C',$fill=0,$ln=0,$x='40',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=0,$valign='M',$fitcell=false);

        $pdf->Write(5,"",'',false,'C',1);
        $pdf->Write(5,"",'',false,'C',1);
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

        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

        // échantillons
        $pdf->Write(5,"",'',false,'C',1);
        $pdf->SetFont('dejavusans', '', 8, '', true);


        $html = '<h2 align="center"><strong>Échantillons présentés au Concours</strong></h2>
            <table border="1" cellspacing="0" cellpadding="4">
            <tr>
            <th align="center"><strong>Catégorie</strong></th>
            <th align="center"><strong>Procédé</strong></th>
            <th align="center"><strong>Variété</strong></th>
            <th align="center"><strong>Numéro de lot</strong></th>
            <th align="center"><strong>Volume</strong></th>
            <th align="center"><strong>Description</strong></th>
            <th align="center"><strong>Référence</strong></th>
            </tr>';
            
        foreach($echantillons as $e){
            $mode = $e->getPaiement()->getName();
            $depot = $e->getLivraison()->getName();
            $procede = '';
            if($e->getProcede() != null){
                $procede = $e->getProcede()->getName();
            }
            $html .= '<tr>
                <td>'.$e->getCategorie()->getName().'</td>
                <td>'.$procede.'</td>
                <td align="center">'.$e->getVariety().'</td>
                <td align="center">'.$e->getLot().'</td>
                <td align="center">'.$e->getVolume().' '.$e->getCategorie()->getType()->getUnite().'</td>
                <td>'.$e->getDescription().'</td>
                <td align="center">'.$e->getPublicRef().'</td>
                </tr>';
            }
            $html .= '</table>';

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        $HT = round($concours->getCout() * count($echantillons),2);
        $TVA = round($concours->getTVA() * $HT / 100,2);
        $TTC = round(count($echantillons) * 20,2);

        $pdf->Write(5,"",'',false,'C',1);
        $pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->Cell(270,7,'Frais de participation : '.count($echantillons).' échantillons X '.$concours->getCout().' = '.$HT.' € HT + '.$TVA.' € de TVA à '.$concours->getTVA().'% = '.$TTC.' € TTC',1,1,'C');

        $pdf->Cell(270,7,'Je règle '.$TTC.' € par '.$mode,1,1,'C');
        $pdf->Cell(270,7,'Je déposerai mes échantillons à '.$depot,1,1,'C');







// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');
$pdf->Output($_SERVER['DOCUMENT_ROOT'].'bulletin_inscription_concours'.$user->getId().'.pdf', 'F');

    }
   
}