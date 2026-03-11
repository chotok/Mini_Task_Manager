<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;


final class ListController extends AbstractController
{
    #[Route('/list', name: 'app_list')]
    public function index(TaskRepository $taskRepository, Request $request): Response
    {
        
        $searchTitle = '';
        $statusFilter = 'all';
        $user = $this->getUser();
        
        if($request->isMethod('POST')){
            $statusFilter = $request->getPayload()->get('status');
            $searchTitle = $request->getPayload()->get('search');
        }

        return $this->render('list/index.html.twig', [
            'controller_name' => 'ListController',
            
            'tasks' => $taskRepository->search($searchTitle,$statusFilter,$user)
                
        ]);
    }
    
    //Function for showing a selected task on another page
    #[Route('/task/{id<\d+>}', name: 'task_show')]
    public function show(Task $task): Response 
    {
        return $this -> render('list/show.html.twig', [
            'task' => $task
        ]);
    }

    //Function to create a new task and add it to the database
    #[Route('task/new', name: 'task_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response 
    {
        $user = $this->getUser();

        $task = new Task();
        $task -> setOwner($user);

        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($task);
            $manager->flush();

            return $this->redirectToRoute('task_show',[
                'id' => $task->getId(),
            ]);
        
        }

        return $this->render('list/new.html.twig', [
            'form' => $form,
        ]);
    }

    //Function to edit the owner of a task and modify it in the database
    #[Route('/task/{id<\d+>}/edit', name:'task_edit')]
    public function edit(Task $task, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createFormBuilder($task)
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('updatedAt', DateTimeType::class,[
                'data' => new \DateTime('now'),
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Edit'
                ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $manager->persist($task);

            $manager->flush();

            $this->addFlash(
                'notice',
                'Task has been updated!'
            );

            return $this->redirectToRoute('task_show',[
                'id' => $task->getId(),
            ]);
        
        }

        return $this->render('list/edit.html.twig', [
            'form' => $form,
            'title' => $task->getTitle(),
        ]);
    }

    //Function to delete an existing task from the database
    #[Route('/task/{id<\d+>}/delete', name: 'task_delete')]
    public function delete(Request $request, Task $task, EntityManagerInterface $manager): Response
    {
        if($request->isMethod('POST')){
            $manager -> remove($task);
            $manager -> flush();

            $this -> addFlash(
                'notice',
                'Task deleted'
            );

            return $this->redirectToRoute('app_list');
        }
        return $this->render('list/delete.html.twig', [
            'id' => $task->getId(),
            'title'=> $task->getTitle(),
        ]);
    }

    //Function for switching the status of a task directly in the listing page
    #[Route('/task/switch/{id<\d+>}', name:'task_switch')]
    public function switch($id, EntityManagerInterface $manager, TaskRepository $taskRepository): Response
    {
        $task = $taskRepository -> findOneBy(['id'=>$id]);
        $task -> setStatus(! $task->isStatus());
        $manager->flush();
        
        return $this->redirectToRoute('app_list');
    }

}
