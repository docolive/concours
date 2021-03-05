<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Profil;
use App\Form\ProfilType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil/add", name="profil_add")
     */
    public function add(Request $request): Response
    {
        $user = $this->getUser();
        $profil = new Profil;

        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profil->setUser($user);
            //dd($profil);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('profil/add.html.twig', [
            'user' => $user,
            'profil' => $profil,
            'form' => $form->createView(),
        ]);

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    /**
     * @Route("/profil/add/admin/{userId}", name="profil_admin_add")
     */
    public function addAdmin(UserRepository $userRepository,Request $request,$userId): Response
    {
        $user = $userRepository->find($userId);

       
        $profil = new Profil;

        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profil->setUser($user);
            //dd($profil);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($profil);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('profil/add.html.twig', [
            'user' => $user,
            'profil' => $profil,
            'form' => $form->createView(),
        ]);

        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }
    /**
     * @Route("/profil/edit", name="profil_edit")
     */
    public function edit(Request $request): Response
    {
        $profil = $this->getUser()->getProfil();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($profil);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profil/edit/admin/{userId}", name="profil_admin_edit")
     */
    public function editAdmin(UserRepository $userRepository,Request $request,$userId): Response
    {
        $user = $userRepository->find($userId);
        $profil = $user->getProfil();
        $form = $this->createForm(ProfilType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($profil);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('profil/edit.html.twig', [
            'profil' => $profil,
            'form' => $form->createView(),
        ]);
    }
}
