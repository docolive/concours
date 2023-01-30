<?php
namespace App\Controller;

use App\Entity\Echantillon;
use App\Form\EchantillonType;
use App\Service\ConcoursSession;
use App\Form\EchantillonEditType;
use App\Repository\UserRepository;
use App\Repository\ProcedeRepository;
use App\Repository\ConcoursRepository;
use App\Repository\CategorieRepository;
use App\Repository\EchantillonRepository;
use App\Service\PDFPalmaresService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/echantillon")
 * @IsGranted("ROLE_CANDIDAT")
 */
class EchantillonController extends AbstractController
{
    private $session;
    public function __construct(ConcoursSession $concoursSession){
        $this->session = $concoursSession;
    }

    /**
    * @Route("/palmares", name="echantillon_palmares")
    * @IsGranted("ROLE_ADMIN")
    */
    public function palmares(EchantillonRepository $echantillonRepository, CategorieRepository $categorieRepository, PDFPalmaresService $pDFPalmaresService): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $echantillons = $echantillonRepository->findPalmares($concours);
        // foreach($echantillons as $e){
        //     if(null !== $e->getProcede()){
        //         dump($e->getCategorie()->getName()." ".$e->getProcede()->getName());
        //     }else{
        //         dump($e->getCategorie()->getName());
        //     }
        // }
        // dd($echantillons);
        $liste = [];
        $categories = [];
        $medailles = [];
        $c = 0;
        $m = 0;
        $ech = 0;
        foreach($echantillons as $e){
            //dump($e->getCategorie()->getName());
            //dd($e);
            
            $categorie = $e->getCategorie()->getId();

            $medaille = $e->getMedaille()->getId();
            
            if(empty($categories)){
                //dump($e->getProcede());
                if(null === ($e->getProcede())){
                    $liste[$c]['categorie'] = $categorie."?".$e->getCategorie()->getName();
                    $liste[$c]['unite'] = $e->getCategorie()->getType()->getUnite();
                    $categories[] = $categorie;

                }else{
                    $liste[$c]['categorie'] = $categorie."?".$e->getCategorie()->getName()." - ".$e->getProcede()->getName();
                    $liste[$c]['unite'] = $e->getCategorie()->getType()->getUnite();
                    $categories[] = $categorie.$e->getProcede()->getId();
                }

                if(empty($medailles)){
                    $liste[$c]['medaille'][$medaille] = $medaille."-".$e->getMedaille()->getNom();
                    $medailles[] = $medaille.$categorie;
                    
                }else{
                    if(!in_array($medaille.$categorie,$medailles)){
                        $liste[$c]['medaille'][$medaille] = $medaille."-".$e->getMedaille()->getNom();
                        $medailles[] = $medaille.$categorie;
                    }
                }
                
            }
            else{
                    if(null === ($e->getProcede())){
                        if(!in_array($categorie,$categories)){
                            $c++;
                            $liste[$c]['categorie'] = $categorie."?".$e->getCategorie()->getName();
                            $liste[$c]['unite'] = $e->getCategorie()->getType()->getUnite();
                            $categories[] = $categorie;
                        }
                        if(empty($medailles)){
                            $liste[$c]['medaille'][$medaille] = $medaille."-".$e->getMedaille()->getNom();
                            $medailles[] = $medaille.$categorie;
                        }else{
                            if(!in_array($medaille.$categorie,$medailles)){
                                $liste[$c]['medaille'][$medaille] = $medaille."-".$e->getMedaille()->getNom();
                                $medailles[] = $medaille.$categorie;
                            }
                        }
                    }else{
                        if(!in_array($categorie.$e->getProcede()->getId(),$categories)){
                            $c++;
                            $liste[$c]['categorie'] = $categorie."?".$e->getCategorie()->getName()." - ".$e->getProcede()->getName();
                            $liste[$c]['unite'] = $e->getCategorie()->getType()->getUnite();
                            $categories[] = $categorie.$e->getProcede()->getId();
                        }
                        if(empty($medailles)){
                            $liste[$c]['medaille'][$medaille] = $medaille."-".$e->getMedaille()->getNom();
                            $medailles[] = $medaille.$categorie.$e->getProcede()->getId();
                        }else{
                            if(!in_array($medaille.$categorie.$e->getProcede()->getId(),$medailles)){
                                $liste[$c]['medaille'][$medaille] = $medaille."-".$e->getMedaille()->getNom();
                                $medailles[] = $medaille.$categorie.$e->getProcede()->getId();
                            }
                        }
                    }
                    
            }

            // $liste[$c]['medaille'][$medaille] = array();
            // $liste[$c]['medaille'][$medaille][] = $e;
            
        }
       //dd($liste);

       foreach($liste as $k => $li){
        //dump($li);
        $echmeds = array();
            $categorie = $li['categorie'];
            //dump($categorie);
            $categorieId = explode("?",$categorie)[0];
            //dump($categorieId);
            $categorieComplete = $categorieRepository->find($categorieId);
            //dd($categorieComplete);
            //dump($categorieComplete->getProcedes());
            if(!null == $categorieComplete->getProcedes()){
                foreach($categorieComplete->getProcedes() as $p){
                    //dump($p->getName());
                    foreach($li['medaille'] as $j => $medaille){
                        //dump($li['medaille']);
                        foreach($li['medaille'] as $m => $nommedaille){
                        // dump($nommedaille);
                        //échantillons dans cette catégorie avec cette médaille
                        $medId = explode("-",$nommedaille)[0];
                        $echmeds = $echantillonRepository->findEchCategorieWithMedailleAndProcede($categorie,$medId,$p);
                        //dd($echmeds);
                        if(null !== $echmeds){
                        $liste[$k]['medaille'][$m]=array();
                        $liste[$k]['medaille'][$m][] = $echmeds;
                        }
                        //dump($liste[$k]['medaille']);
                        
                        }
                     
                      
                   }
                    
                }

            }
            if(empty($echmeds)){
                foreach($li['medaille'] as $j => $medaille){
                    //dump($li['medaille']);
                    foreach($li['medaille'] as $m => $nommedaille){
                    // dump($nommedaille);
                    //échantillons dans cette catégorie avec cette médaille
                    $medId = explode("-",$nommedaille)[0];
                    $echmeds = $echantillonRepository->findEchCategorieWithMedaille($categorie,$medId);
                    //dd($echmeds);
                    $liste[$k]['medaille'][$m]=array();
                    $liste[$k]['medaille'][$m][] = $echmeds;
                    //dump($liste[$k]['medaille']);
                    
                    }
                
                
                }
            }

       }
      //dd($liste);
      
        $pDFPalmaresService->palmares($concours,$liste);
        return $this->redirectToRoute('dashboard');


    }

    /**
    * @Route("/search", name="echantillon_search")
    * @IsGranted("ROLE_ADMIN")
    */
    public function search(EchantillonRepository $echantillonRepository, Request $request): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }

        $param = $request->query->get('term');
        $echs = $echantillonRepository->findSearch($param,$concours);
        //dd($echs);
        $result = array();
        foreach($echs as $e){
            // dd($e);
            $name = $e->getId();
            $label = $e->getPublicRef()."-".$e->getCategorie()->getName()."-".$e->getUser()->getProfil()->getNom()." ".$e->getUser()->getProfil()->getPrenom();
            $result[] = [
                'name' => $name,
                'label' => $label
            ];
        }
        return new JsonResponse($result);
    }

     /**
     * @Route("/choice", name="echantillon_choice")
     * @IsGranted("ROLE_ADMIN")
     */
    public function choice(Request $request)
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class,[
                'attr' => [
                    'placeholder' => "Code Public, Nom, Prénom, raison sociale"
                ]
            ])
            ->add('userName',HiddenType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
        }

        return $this->render('echantillon/choice.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/", name="echantillon_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(EchantillonRepository $echantillonRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $echantillons = $echantillonRepository->findEchConcours($concours);
        // dd($echantillons);
        return $this->render('echantillon/index.html.twig', [
            'concours' => $concours,
            'echantillons' => $echantillons
        ]);
    }

    public function codePublic($categorie): string
    {
            $repo = $this->getDoctrine()->getRepository(Echantillon::class);
            $random = rand(1,100);
            $shuffled = str_shuffle('ABCDEFGHIJKLM');
            $codePublic = $categorie.'-'.$random.'-'.substr($shuffled,0,3);
            $existe = $repo->findBy(
                ['public_ref' => $codePublic]
            );
            if(empty($existe)){
                return $codePublic;
            }else{
                $this->codePublic($categorie);
            }
    }

    /**
     * @Route("/add/{userId}", name="echantillon_admin_add", methods={"GET","POST"})
     */
    public function AdminAdd(Request $request,ProcedeRepository $procedeRepository,UserRepository $userRepository,$userId): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        //dd($userId);
        $user = $userRepository->find($userId);

        if($user->getProfil()){
            $SIRET = $user->getProfil()->getSiret();
            if(strlen($SIRET) == 0){
                $this->addFlash(
                    'error',
                    'Votre numéro de SIRET est obligatoire pour participer au Concours.'
                );
                return $this->redirectToRoute('profil_admin_edit',[
                    'user'=>$user
                ]);
            }
        }else{
            $this->addFlash(
                'error',
                'Votre numéro de SIRET est obligatoire pour participer au Concours.'
            );
            return $this->redirectToRoute('profil_admin_add',[
                'userId'=>$userId
            ]);
        }
        $echantillon = new Echantillon();
        $echantillon->setUser($user);
        $form = $this->createForm(EchantillonType::class, $echantillon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            
           //code public
            $categorie = $echantillon->getCategorie()->getId();
            $codePublic = $this->codePublic($categorie);
            //dd($codePublic);
            $echantillon->setPublicRef($codePublic);
            //$user = $this->getUser();
            $echantillon->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($echantillon);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('echantillon/add.html.twig', [
            //'echantillon' => $echantillon,
            'admin' => 1,
            'concours' => $concours,
            'form' => $form->createView(),
            //'add' => true
        ]);
    }


    /**
     * @Route("/add", name="echantillon_add", methods={"GET","POST"})
     */
    public function add(Request $request,ProcedeRepository $procedeRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $user = $this->getUser();

        $roles = $user->getRoles();
        if(in_array('ROLE_ADMIN',$roles) || in_array('ROLE_SUPER_ADMIN',$roles)){
           return $this->redirectToRoute('candidat_index');
        }

        if($user->getProfil()){
            $SIRET = $user->getProfil()->getSiret();
            if(strlen($SIRET) == 0){
                $this->addFlash(
                    'error',
                    'Votre numéro de SIRET est obligatoire pour participer au Concours.'
                );
                return $this->redirectToRoute('profil_edit');
            }
        }else{
            $this->addFlash(
                'error',
                'Votre numéro de SIRET est obligatoire pour participer au Concours.'
            );
            return $this->redirectToRoute('profil_add');
        }
        $echantillon = new Echantillon();
        $echantillon->setUser($user);
        $form = $this->createForm(EchantillonType::class, $echantillon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
            
           //code public
            $categorie = $echantillon->getCategorie()->getId();
            $codePublic = $this->codePublic($categorie);
            //dd($codePublic);
            $echantillon->setPublicRef($codePublic);
            $user = $this->getUser();
            $echantillon->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($echantillon);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('echantillon/add.html.twig', [
            //'echantillon' => $echantillon,
            'concours' => $concours,
            'form' => $form->createView(),
            //'add' => true
        ]);
    }

    /**
     * @Route("/edit", name="echantillon_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, EchantillonRepository $echantillonRepository): Response
    {
        $echId = $request->query->get('echantillonId');
        $echantillon = $echantillonRepository->find($echId);

        $concours = $this->session->recup();
        $roles = $this->getUser()->getRoles();
        $admin = 0;
        if(in_array('ROLE_ADMIN',$roles) || in_array('ROLE_SUPER_ADMIN',$roles)){
            $admin = 1;
        }

        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }

        $form = $this->createForm(EchantillonEditType::class, $echantillon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('echantillon_index');

        }
        

        return $this->render('echantillon/edit.html.twig', [
            'admin' => $admin,
            'echantillon' => $echantillon,
            'concours' => $concours,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="echantillon_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Echantillon $echantillon): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echantillon->getId(), $request->request->get('_token'))) {
        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($echantillon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('dashboard');

    }
}
