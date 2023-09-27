<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        ]);
    }
}
