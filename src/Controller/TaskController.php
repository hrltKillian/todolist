<?php

namespace App\Controller;

use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/users/{user_slug}/tasks', name: 'app_tasks_show')]
    public function index(
        string $user_slug,
        TaskRepository $taskRepository
    ): Response
    {
        return $this->render('task/show.html.twig', [
            'tasks' => $taskRepository->createQueryBuilder("t")
                ->select('t')
                ->join('t.user','u')
                ->where('t.user = u.id AND u.slug = :user_slug')
                ->setParameter('user_slug', $user_slug)
                ->getQuery()->getResult(),
            "user_slug" => $user_slug
        ]);
    }

    #[Route('users/{user_slug}/tasks/new', name: 'app_tasks_new')]
    public function new(
        string $user_slug,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ): Response
    {
        $form = $this->createForm(TaskType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $task = $form->getData();
            $entityManagerInterface->persist($task);
            $entityManagerInterface->flush();
            //$this->addFlash("success", "Vous avez bien créé votre tâche.");
            return $this->redirectToRoute("app_tasks_show",['user_slug' => $user_slug]);
        }
        return $this->render('task/new.html.twig', [
            "form" => $form,
            "user_slug" => $user_slug
        ]);
    }
}
