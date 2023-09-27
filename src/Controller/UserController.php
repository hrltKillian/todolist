<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_users')]
    public function index(
        UserRepository $userRepository
    ): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/users/new', name: 'app_users_new')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $user = $form->getData();
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            $this->addFlash("success", "Vous avez bien créé votre compte client.");
            return $this->redirectToRoute("app_users");
        }
        return $this->render('user/new.html.twig', [
            "form" => $form,
        ]);
    }

    #[Route('/users/{slug}/edit', name: 'app_users_edit')]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        User $user
    ): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $entityManagerInterface->flush();
            $this->addFlash("success", "Vous avez bien édité votre compte client.");
            return $this->redirectToRoute("app_users");
        }
        return $this->render('user/edit.html.twig', [
            "form" => $form,
            "user" => $user
        ]);
    }

    #[Route('/users/{slug}/delete', name: 'app_users_delete')]
    public function delete(
        EntityManagerInterface $entityManagerInterface,
        User $user
    ): Response
    {
        $entityManagerInterface->remove($user);
        $entityManagerInterface->flush();
        $this->addFlash("success", "Vous avez bien supprimé votre compte client.");
        return $this->redirectToRoute("app_users");
    }
}
