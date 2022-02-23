<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use App\Security\AppUserAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppUserAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles( array('ROLE_CANDIDAT') );
            $user->setCreatedAt(new DateTime());
            //dd($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->send($user);
           
            // do anything else you need here, like send an email

            // return $guardHandler->authenticateUserAndHandleSuccess(
            //     $user,
            //     $request,
            //     $authenticator,
            //     'main' // firewall name in security.yaml
            // );
            return $this->render('registration/wait_confirm.html.twig',array(
                'user'=>$user
            ));
        
    }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request,UserRepository $userRepository): Response
    {
        $user = $userRepository->find($request->query->get('id'));
        //dd($user);
        if (!$user) {
            throw $this->createNotFoundException();
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', $exception->getReason());

            return $this->redirectToRoute('dashboard');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse mail est correcte !');

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/send/email", name="app_send_verify_email")
     */
    public function send($user){
       // $user = $this->getUser();
         // generate a signed url and email it to the user
         $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
         (new TemplatedEmail())
             ->from(new Address('contact@franceolive.com', 'Concours'))
             ->to($user->getEmail())
             ->subject('Merci de confirmer votre inscription au Concours')
             ->htmlTemplate('registration/confirmation_email.html.twig')
     );
     return $this->render('registration/wait_confirm.html.twig',array(
        'user'=>$user
    ));
    }

    /**
     * @Route("/edit/email", name="edit_email")
     */
    public function editMail(Request $request){
        $user = $this->getUser();
        $form = $this->createFormBuilder($user)
        ->add('email',EmailType::class,array(
            'attr' => array(
                'placeholder'=>"Votre email",
                'autofocus' => true,
                'required' => true
                 ),
            'label' => false
        ))
            
            //->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // dd($user);
            
           
            return $this->redirectToRoute('dashboard');
        }

     return $this->render('registration/editmail.html.twig',array(
        'form' => $form->createView(),
    ));
    }

}
