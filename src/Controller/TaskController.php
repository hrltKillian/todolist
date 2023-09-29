<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/users/{userSlug}/tasks', name: 'app_tasks_show')]
    public function index(
        string $userSlug,
        TaskRepository $taskRepository,
        PaginatorInterface $paginationInterface,
        Request $request
    ): Response {
        $pagination = $paginationInterface->paginate(
            $taskRepository->createQueryBuilder("tasks")
                ->select('tasks')
                ->join('tasks.user', 'u')
                ->where('tasks.user = u.id AND u.slug = :userSlug')
                ->setParameter('userSlug', $userSlug)
                ->getQuery(),
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('task/show.html.twig', [
            'tasks' => $pagination,
            "userSlug" => $userSlug
        ]);
    }

    #[Route('users/{userSlug}/tasks/new', name: 'app_tasks_new')]
    public function new(
        string $userSlug,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ): Response {
        $form = $this->createForm(TaskType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $entityManagerInterface->persist($task);
            $entityManagerInterface->flush();
            $this->addFlash("success", "Vous avez bien créé votre tâche.");
            return $this->redirectToRoute("app_tasks_show", ['userSlug' => $userSlug]);
        }
        return $this->render('task/new.html.twig', [
            "form" => $form,
            "userSlug" => $userSlug
        ]);
    }

    #[Route('users/{userSlug}/tasks/{slug}/edit', name: 'app_tasks_edit')]
    public function edit(
        string $userSlug,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        Task $task
    ): Response {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManagerInterface->flush();
            $this->addFlash("success", "Vous avez bien modifié votre tâche.");
            return $this->redirectToRoute("app_tasks_show", ['userSlug' => $userSlug]);
        }
        return $this->render('task/edit.html.twig', [
            "form" => $form,
            "task" => $task,
            "userSlug" => $userSlug
        ]);
    }

    #[Route('users/{userSlug}/tasks/{slug}/delete', name: 'app_tasks_delete')]
    public function delete(
        string $userSlug,
        EntityManagerInterface $entityManagerInterface,
        Task $task
    ): Response {
        $entityManagerInterface->remove($task);
        $entityManagerInterface->flush();
        $this->addFlash("success", "Vous avez bien supprimé votre tâche.");
        return $this->redirectToRoute("app_tasks_show", ['userSlug' => $userSlug]);
    }
}
