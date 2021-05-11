<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\ConcoursSession;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JureExcelController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }
    /**
     * @Route("/excel/jures", name="jures-liste-excel")
     */
    public function index(UserRepository $userRepository): Response
    {
        $concours = $this->session->recup();
        $datas = $userRepository->findJures(
            array('concoursId'=>$concours->getId())
        );
        //dd($datas);
        $spreadsheet = new Spreadsheet();

        /* @var $sheet \PhpOffice\PhpSpreadsheet\Writer\Xlsx\Worksheet */
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Liste jurés");

        $sheet->setCellValue('A1', 'Liste des jurés du Concours '.$concours->getName().' au '.date('d/m/Y'));

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
        $sheet->setCellValue('K2', 'Souhait Dégustation');
        $sheet->setCellValue('L2', 'Echantillons');

        $l = 3;
        foreach($datas as $d){
            //dd($d->getEchantillons());
            $sheet->setCellValue('A'.$l, $d->getProfil()->getRaisonSociale());
            $sheet->setCellValue('B'.$l, $d->getProfil()->getNom());
            $sheet->setCellValue('C'.$l, $d->getProfil()->getPrenom());
            $sheet->setCellValue('D'.$l, $d->getProfil()->getAdress1());
            $sheet->setCellValue('E'.$l, $d->getProfil()->getAdress2());
            $sheet->setCellValue('F'.$l, $d->getProfil()->getAdress3());
            $sheet->setCellValue('G'.$l, $d->getProfil()->getAdress4());
            $sheet->setCellValue('H'.$l, $d->getProfil()->getAdress5());
            $sheet->setCellValue('I'.$l, $d->getProfil()->getPhone());
            $sheet->setCellValue('J'.$l, $d->getEmail());
            
            $souhait = '';
            foreach($d->getProfil()->getChoixDegustation() as $c){
                $souhait .= $c->getName()."\n";
            }
            $sheet->setCellValue('K'.$l, $souhait);

            $echs = '';
            foreach($d->getEchantillons() as $e){
                //dd($e);
                $echs .= $e->getCategorie()->getName()."\n";
            }
            $sheet->setCellValue('L'.$l, $echs);
            

            $l++;
        }

        

        // Create your Office 2007 Excel (XLSX Format)
        //$writer = new Xlsx($spreadsheet);
                
        // redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="liste_jures.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
       
    }
}
