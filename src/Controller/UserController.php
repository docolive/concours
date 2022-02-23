<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\EchantillonRepository;
use App\Service\ConcoursSession;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController{
    private $session;
    private $encoder;
    private $userRepository;
    public function __construct(UserPasswordEncoderInterface $encoder,ConcoursSession $concoursSession, UserRepository $userRepository){
        $this->encoder = $encoder;
        $this->session = $concoursSession;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/admin/user/search", name="user-search-name")
     */
    public function userserSearchName(Request $request): Response
    {
        $param = $request->query->get('term');
        $users = $this->userRepository->findSearch($param);
        //dd($users);
        $result = array();
        foreach($users as $u){
            //dd($u->getPrenom());
            $r = "";
            foreach($u->getRoles() as $r){
                
                $r .= $r.", ";
                
            }
            
            $label = "Prénom : ";
            if(!is_null($u->getProfil()->getPrenom())){
                    $label .= $u->getProfil()->getPrenom();
            }

            $label .= " - Nom : ";
            if(!is_null($u->getProfil()->getNom())){
                    $label .= $u->getProfil()->getNom();
            }

            $label .= " - Email : ";
            if(!is_null($u->getEmail())){
                    $label .= $u->getEmail();
            }
            $label .= " - Raison sociale : ";
            if(!is_null($u->getProfil()->getRaisonSociale())){
                    $label .= $u->getProfil()->getRaisonSociale();
            }

            $result[] = array(
                'name' => $u->getEmail(),
                'label' => $label
                );
        }
    
        // dd(new JsonResponse($result));
        return new JsonResponse($result);
    }

/**
 * @Route("/add",name="user_add"))
 * @IsGranted("ROLE_ADMIN")
 * 
 */
public function add(Request $request){
    $user = new User();
    $user->setIsVerified(true);
    $form = $this->createForm(UserType::class,$user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        //dd($form['Roles']->getData());
        $user->setRoles( array($form['Roles']->getData()) );
        
        $user->setPassword($this->encoder->encodePassword($user,$user->getPassWord()));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_index');
    }

    return $this->render('user/add.html.twig',[
        'form' => $form->createView()
    ]);
}
/**
 * @Route("/{id}/edit",name="user_edit", methods={"GET","POST"}))
 * 
 */
public function edit( Request $request, User $user): Response
{
    
    $form = $this->createForm(UserType::class,$user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user->setPassword($this->encoder->encodePassword($user,$user->getPassWord()));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_index');
    }

    return $this->render('user/add.html.twig',[
        'form' => $form->createView()
    ]);
}

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'email'=>true,
            'droits'=>true,
            'titre' => "Liste de tous les utilisateurs",
            'users' => $userRepository
            ->findBy(
                array(),
                array('email' => 'ASC')
            ),
        ]);
        
    }   

    /**
    * @Route("/jures", name="jure_index")
    */
    public function jureIndex(UserRepository $userRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $datas = $userRepository->findJures(
            array('concoursId'=>$concours->getId())
        );
        $tab = array();
        $i = 0;
        foreach($datas as $d){
            $tab[$i]['id'] = $d->getId();
            $tab[$i]['nom'] = $d->getProfil()->getNom().' '.$d->getProfil()->getPrenom();
            $tab[$i]['email'] = $d->getEmail();
            $echs = $d->getEchantillons();
            $tab[$i]['categorie'] = array();
            foreach($echs as $e){
                if(!in_array($e->getCategorie()->getName(),$tab[$i]['categorie'])){
                    $tab[$i]['categorie'][] = $e->getCategorie()->getName();
                }
            }
            $tab[$i]['degust'] = array();
            foreach($d->getProfil()->getChoixDegustation() as $c){
                $tab[$i]['degust'][] = $c->getName();
            }
            $i++;
        }
        return $this->render('user/jures.html.twig', [
            'tab' => $tab,
        ]);
    }   

    /**
    * @Route("/candidats", name="candidat_index")
    */
    public function candidatsIndex(UserRepository $userRepository, EchantillonRepository $echantillonRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $users = $userRepository->findCandidats($concours);
        
        return $this->render('user/index.html.twig', [
            'titre' => "Liste des candidats",
            'droits' => false,
            'email' => false,
            'utilisateur' => 'candidat',
            'users' => $users
        ]);
    }   

    /**
    * @Route("/touscandidats", name="tous_candidat_index")
    */
    public function tousCandidatsIndex(UserRepository $userRepository): Response
    {
        $users = $userRepository->findCandidatsPourAdmin();
        //dd($users);
        return $this->render('user/index.html.twig', [
            'titre' => "Liste de tous les candidats",
            'droits' => false,
            'email' => true,
            'utilisateur' => 'candidat',
            'users' => $users
        ]);
    }  

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
             
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/admin/switch/choice",name="user-switch-choice")
     */
    public function switchChoice(Request $request){
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('search', TextType::class,[
                'attr' => [
                    'placeholder' => "Nom, Prénom, raison sociale, email"
                ]
            ])
            ->add('userName',HiddenType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();
        }

        return $this->render('user/switchchoice.html.twig',[
            'form' => $form->createView()
        ]);
    }
}