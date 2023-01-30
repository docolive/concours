<?php

namespace App\Controller;

use App\Repository\EchantillonRepository;
use App\Service\ConcoursSession;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/excel")
 */
class ExcelControllerWithAno extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }
    /**
     * @Route("/echantillons/ano", name="echantillons-liste-excel-with-ano")
     */
    public function index(EchantillonRepository $echantillonRepository): Response
    {
        $concours = $this->session->recup();
        $echantillons = $echantillonRepository->findEchConcours($concours);
        $spreadsheet = new Spreadsheet();

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Liste échantillons");

        $sheet->setCellValue('A1', 'Liste des échantillons du Concours '.$concours->getName().' au '.date('d/m/Y'));

        $sheet->setCellValue('A2', 'Raison sociale');
        $sheet->setCellValue('B2', 'Nom');
        $sheet->setCellValue('C2', 'Prénom');
        $sheet->setCellValue('D2', 'Adresse 1');
        $sheet->setCellValue('E2', 'Adresse 2');
        $sheet->setCellValue('F2', 'Adresse 3');
        $sheet->setCellValue('G2', 'Adresse 4');
        $sheet->setCellValue('H2', 'Adresse 5');
        $sheet->setCellValue('I2', 'Téléphone');
        $sheet->setCellValue('J2', 'Email');
        $sheet->setCellValue('K2', 'Catégorie');
        $sheet->setCellValue('L2', 'Sous-catégorie');
        $sheet->setCellValue('M2', 'Variété OT');
        $sheet->setCellValue('N2', 'Description');
        $sheet->setCellValue('O2', 'Lot');
        $sheet->setCellValue('P2', 'Volume');
        $sheet->setCellValue('Q2', 'Code public');
        $sheet->setCellValue('R2', 'Code ano');
        $sheet->setCellValue('S2', 'Mode paiement');
        $sheet->setCellValue('T2', 'Payé');
        $sheet->setCellValue('U2', 'Lieu de livraison');
        $sheet->setCellValue('V2', 'Observation');
        $sheet->setCellValue('W2', 'Médaille');

        $l = 3;
        foreach($echantillons as $e){
            $sheet->setCellValue('A'.$l, $e->getUser()->getProfil()->getRaisonSociale());
            $sheet->setCellValue('B'.$l, $e->getUser()->getProfil()->getNom());
            $sheet->setCellValue('C'.$l, $e->getUser()->getProfil()->getPrenom());
            $sheet->setCellValue('D'.$l, $e->getUser()->getProfil()->getAdress1());
            $sheet->setCellValue('E'.$l, $e->getUser()->getProfil()->getAdress2());
            $sheet->setCellValue('F'.$l, $e->getUser()->getProfil()->getAdress3());
            $sheet->setCellValue('G'.$l, $e->getUser()->getProfil()->getAdress4());
            $sheet->setCellValue('H'.$l, $e->getUser()->getProfil()->getAdress5());
            $sheet->setCellValue('I'.$l, $e->getUser()->getProfil()->getPhone());
            $sheet->setCellValue('J'.$l, $e->getUser()->getEmail());
            $sheet->setCellValue('K'.$l, $e->getCategorie()->getName());
            if(is_null($e->getProcede())){
                $procede = '';
            }else{
                $procede = $e->getProcede()->getName();
            }
            $sheet->setCellValue('L'.$l, $procede);
            $sheet->setCellValue('M'.$l, $e->getVariety());
            $sheet->setCellValue('N'.$l, $e->getDescription());
            $sheet->setCellValue('O'.$l, $e->getLot());
            $sheet->setCellValue('P'.$l, $e->getVolume());
            $sheet->setCellValue('Q'.$l, $e->getPublicRef());
            $sheet->setCellValue('R'.$l, $e->getCode());
            if(is_null($e->getPaiement())){
                $paiement = '';
            }else{
                $paiement = $e->getPaiement()->getId();
            }
            $sheet->setCellValue('S'.$l, $paiement);
            $sheet->setCellValue('T'.$l, $e->getPaye());
            if(is_null($e->getLivraison())){
                $livraison = '';
            }else{
                $livraison = $e->getLivraison()->getId();
            }
            $sheet->setCellValue('U'.$l, $livraison);
            $sheet->setCellValue('V'.$l, $e->getObservation());
            if($e->getMedaille() !== null){
                $sheet->setCellValue('W'.$l, $e->getMedaille()->getNom());
            }

            $l++;
        }

        

        // Create your Office 2007 Excel (XLSX Format)
        //$writer = new Xlsx($spreadsheet);
                
        // redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="liste_echantillons.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
       
    }
}
