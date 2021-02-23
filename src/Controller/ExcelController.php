<?php

namespace App\Controller;

use App\Repository\EchantillonRepository;
use App\Service\ConcoursSession;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExcelController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }
    /**
     * @Route("/excel", name="excel")
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
        $sheet->setCellValue('K2', 'Variété OT');
        $sheet->setCellValue('L2', 'Description');
        $sheet->setCellValue('M2', 'Lot');
        $sheet->setCellValue('N2', 'Volume');
        $sheet->setCellValue('O2', 'Code public');
        $sheet->setCellValue('P2', 'Code ano');
        $sheet->setCellValue('Q2', 'Mode paiement');
        $sheet->setCellValue('R2', 'Payé');

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
            $sheet->setCellValue('K'.$l, $e->getVariety());
            $sheet->setCellValue('L'.$l, $e->getDescription());
            $sheet->setCellValue('M'.$l, $e->getLot());
            $sheet->setCellValue('N'.$l, $e->getVolume());
            $sheet->setCellValue('O'.$l, $e->getCode());
            $sheet->setCellValue('P'.$l, '');
            $sheet->setCellValue('Q'.$l, '');
            $sheet->setCellValue('R'.$l, '');

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
