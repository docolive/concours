<?php
namespace App\Service;

use Qipsius\TCPDFBundle\Controller\TCPDFController;

class PDFPalmaresService
{
    private $tcpdf;

    public function __construct(TCPDFController $pdf) 
    {
        $this->tcpdf = $pdf;
    }
    
    public function palmares($concours,$liste){
        $pdf = $this->tcpdf->create('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Jean-Michel Duriez');
        $pdf->SetTitle('Palmarès Concours');
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
        $pdf->Image('images/logoFDB.png', 170, 8, 25, 15, '', 'https://concours.ctolivier.org', '', true, 150, '', false, false, 0, false, false, false);

        //titre
        $pdf->SetXY(1,15);
        $pdf->SetFont('dejavusans', 'B', 12, '', true);

        //Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

        $pdf->MultiCell(100,10,"Palmarès du Concours ".$concours->getName(),$border=1,$align='C',$fill=0,$ln=0,$x='50',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=0,$valign='M',$fitcell=false);

        $pdf->Write(5,"",'',false,'C',1);
        $pdf->Write(5,"",'',false,'C',1);
        $pdf->Write(5,"",'',false,'C',1);
        $pdf->Write(5,"",'',false,'C',1);
        $pdf->SetFont('dejavusans', '', 10, '', true);

        $html = "";
        //dd($liste);
        foreach($liste as $li){
            //dump($li);
            //dump($li['categorie']);
            $unite = $li['unite'];
            $categorie = explode('?',$li['categorie'])[1];
            //dump($categorie);
            $html .= '<br><br><hr><div><span style="font-weight:bold;text-align:center">Catégorie : '.$categorie.'</span>';
            //dump($html);
           // dump($li['medaille']);
            foreach($li['medaille'] as $k => $lm){
                
                //dump($lm);
                if($k == 1){
                    if(!empty($lm)){
                    $medaille = "Médaille d'Or";
                    $html .= '<div><span style="font-weight:bold; color:gold">'.$medaille.'</span>';
                    //dump($html);

                    foreach($lm as $k => $l){
                        $html .= "<div>";//echantillonS
                        foreach($l as $e){
                            $html .= "<div>";//echantillon
                                $raisonSociale = $e->getUser()->getProfil()->getRaisonSociale();
                                $html .= strtoupper($raisonSociale)."<br>";
                                $html .= $raisonSociale."<br>";
                                $nom = $e->getUser()->getProfil()->getNom();
                                $prenom = $e->getUser()->getProfil()->getPrenom();
                                // $nom = strtolower($nom);
                                // $nom = ucwords($nom);
                                //$prenom = strtolower($prenom);
                                //$prenom = ucwords($prenom);
                                $html .= $nom." ".$prenom;
                                //dump($html);
                                $html .= "<br>".$e->getUser()->getProfil()->getAdress1();
                                if(null !== $e->getUser()->getProfil()->getAdress2()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress2();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress3()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress3();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress4()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress4();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress5()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress5();
                                }
                                $html .= "<br>".$e->getUser()->getProfil()->getPhone();
                                $html .= " - ".$e->getUser()->getEmail();

                                $html .='<div style="font-size:80%">';//description
                                $html .= "lot : ".$e->getLot()." - volume du lot : ".$e->getVolume()." ".$unite;
                                $html .= "<br>".$e->getDescription();

                                $html .="</div>";//fin description

                                //dump($html);
                    
                            $html .= "</div>";//fin échantillon
                        }
                        $html .= "<div>";//fin échantillonS
                        
                        $html .= "</div>";//fin médaille
                    }
                    // dd($html);
                }
                }
                //dd($html);
                if($k == 2){
                    $medaille = "Médaille d'Argent";
                    $html .= '<div><span style="font-weight:bold; color:silver">'.$medaille.'</span>';
                    foreach($lm as $k => $l){
                        $html .= "<div>";//echantillonS
                        foreach($l as $e){
                            $html .= "<div>";//echantillon
                                $raisonSociale = $e->getUser()->getProfil()->getRaisonSociale();
                                $html .= $raisonSociale."<br>";
                                $nom = $e->getUser()->getProfil()->getNom();
                                $prenom = $e->getUser()->getProfil()->getPrenom();
                                // $nom = strtolower($nom);
                                // $nom = ucwords($nom);
                                $html .= $nom." ".$prenom;
                                $html .= "<br>".$e->getUser()->getProfil()->getAdress1();
                                if(null !== $e->getUser()->getProfil()->getAdress2()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress2();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress3()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress3();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress4()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress4();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress5()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress5();
                                }
                                $html .="<br>".$e->getUser()->getProfil()->getPhone();
                                $html .=" - ".$e->getUser()->getEmail();

                                $html .='<div style="font-size:80%">';//description
                                $html .= "lot : ".$e->getLot()." - volume du lot : ".$e->getVolume()." ".$unite;
                                $html .= "<br>".$e->getDescription();

                                $html .="</div>";//fin description

                            $html .= "</div>";//fin échantillon
                        }
                        $html .= "<div>";//fin échantillonS
                        
                        $html .= "</div>";//fin médaille
                    }
                }
                if($k == 3){
                    $medaille = "Médaille de Bronze";
                     
                    $html .= '<div><span style="font-weight:bold; color:darkSalmon">'.$medaille.'</span>';
                    foreach($lm as $k => $l){
                        $html .= "<div>";//echantillonS
                        foreach($l as $e){
                            $html .= "<div>";//echantillon
                                $raisonSociale = $e->getUser()->getProfil()->getRaisonSociale();
                                $html .= $raisonSociale."<br>";
                                $nom = $e->getUser()->getProfil()->getNom();
                                $prenom = $e->getUser()->getProfil()->getPrenom();
                                // $nom = strtolower($nom);
                                // $nom = ucwords($nom);
                                $html .= $nom." ".$prenom;
                                $html .= "<br>".$e->getUser()->getProfil()->getAdress1();
                                if(null !== $e->getUser()->getProfil()->getAdress2()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress2();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress3()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress3();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress4()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress4();
                                }
                                if(null !== $e->getUser()->getProfil()->getAdress5()){
                                    $html .= "<br>".$e->getUser()->getProfil()->getAdress5();
                                }
                                $html .="<br>".$e->getUser()->getProfil()->getPhone();
                                $html .=" - ".$e->getUser()->getEmail();

                                $html .='<div style="font-size:80%">';//description
                                $html .= "lot : ".$e->getLot()." - volume du lot : ".$e->getVolume()." ".$unite;
                                $html .= "<br>".$e->getDescription();

                                $html .="</div>";//fin description

                            $html .= "</div>";//fin échantillon
                        }
                        $html .= "<div>";//fin échantillonS
                        
                        $html .= "</div>";//fin médaille
                    }
                }
                
                
            
        }
            $html .= "</div>";//categorie
            //dump($html);
        }
        //dd($html);
        

        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');


// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');
$pdf->Output($_SERVER['DOCUMENT_ROOT'].'palmares_concours.pdf', 'I');

    }
   
}