<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\ConcoursSession;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController{
    private $session;
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder,ConcoursSession $concoursSession){
        $this->encoder = $encoder;
        $this->session = $concoursSession;
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
    public function candidatsIndex(UserRepository $userRepository): Response
    {
        $concours = $this->session->recup();
        if($concours == 'vide'){
            return $this->redirectToRoute('concours_choix');
        }
        $users = $userRepository->findCandidats($concours);
        //dd($users);
        return $this->render('user/index.html.twig', [
            'titre' => "Liste des candidats",
            'droits' => false,
            'email' => false,
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
}