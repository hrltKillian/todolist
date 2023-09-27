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
    #[Route('/users/{user_slug}/tasks', name: 'app_tasks_show')]
    public function index(
        string $user_slug,
        TaskRepository $taskRepository,
        PaginatorInterface $paginationInterface,
        Request $request
    ): Response
    {
        $pagination = $paginationInterface->paginate(
            $taskRepository->createQueryBuilder("tasks")
                ->select('tasks')
                ->join('tasks.user','u')
                ->where('tasks.user = u.id AND u.slug = :user_slug')
                ->setParameter('user_slug', $user_slug)
                ->getQuery(),
            $request->query->getInt('page', 1),
            3
        );
        return $this->render('task/show.html.twig', [
            'tasks' => $pagination,
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
            $this->addFlash("success", "Vous avez bien créé votre tâche.");
            return $this->redirectToRoute("app_tasks_show",['user_slug' => $user_slug]);
        }
        return $this->render('task/new.html.twig', [
            "form" => $form,
            "user_slug" => $user_slug
        ]);
    }

    #[Route('users/{user_slug}/tasks/{slug}/edit', name: 'app_tasks_edit')]
    public function edit(
        string $user_slug,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        Task $task
    ): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $entityManagerInterface->flush();
            $this->addFlash("success", "Vous avez bien modifié votre tâche.");
            return $this->redirectToRoute("app_tasks_show",['user_slug' => $user_slug]);
        }
        return $this->render('task/edit.html.twig', [
            "form" => $form,
            "task" => $task,
            "user_slug" => $user_slug
        ]);
    }

    #[Route('users/{user_slug}/tasks/{slug}/delete', name: 'app_tasks_delete')]
    public function delete(
        string $user_slug,
        EntityManagerInterface $entityManagerInterface,
        Task $task
    ): Response
    {
        $entityManagerInterface->remove($task);
        $entityManagerInterface->flush();
        $this->addFlash("success", "Vous avez bien supprimé votre tâche.");
        return $this->redirectToRoute("app_tasks_show",['user_slug' => $user_slug]);
    }
}
