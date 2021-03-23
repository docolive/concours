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
class ExcelController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }
    /**
     * @Route("/echantillons", name="echantillons-liste-excel")
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
        $sheet->setCellValue('J2', 'Catégorie');
        $sheet->setCellValue('k2', 'Procédé');
        $sheet->setCellValue('l2', 'Variété OT');
        $sheet->setCellValue('M2', 'Description');
        $sheet->setCellValue('N2', 'Lot');
        $sheet->setCellValue('O2', 'Volume');
        $sheet->setCellValue('P2', 'Code public');
        $sheet->setCellValue('Q2', 'Code ano');
        $sheet->setCellValue('R2', 'Mode paiement');
        $sheet->setCellValue('S2', 'Payé');
        $sheet->setCellValue('T2', 'Lieu de livraison');
        $sheet->setCellValue('U2', 'Observation');

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
            $sheet->setCellValue('J'.$l, $e->getCategorie()->getName());
            if(is_null($e->getProcede())){
                $procede = '';
            }else{
                $procede = $e->getProcede()->getName();
            }
            $sheet->setCellValue('K'.$l, $procede);
            $sheet->setCellValue('L'.$l, $e->getVariety());
            $sheet->setCellValue('M'.$l, $e->getDescription());
            $sheet->setCellValue('N'.$l, $e->getLot());
            $sheet->setCellValue('O'.$l, $e->getVolume());
            $sheet->setCellValue('P'.$l, $e->getPublicRef());
            $sheet->setCellValue('Q'.$l, '');
            if(is_null($e->getPaiement())){
                $paiement = '';
            }else{
                $paiement = $e->getPaiement()->getId();
            }
            $sheet->setCellValue('R'.$l, $paiement);
            $sheet->setCellValue('S'.$l, $e->getPaye());
            if(is_null($e->getLivraison())){
                $livraison = '';
            }else{
                $livraison = $e->getLivraison()->getId();
            }
            $sheet->setCellValue('T'.$l, $livraison);
            $sheet->setCellValue('U'.$l, $e->getObservation());

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
