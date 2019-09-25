<?php

namespace App\Controller;

use App\Entity\TaskItem;
use App\Entity\TaskList;
use App\Form\ContributorType;
use App\Repository\TaskListRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="tasklist_")
 */
class TaskListController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     */
    public function index(TaskListRepository $taskListRepository, Request $request)
    {
        switch ($request->query->get('filter')) {
            case 'own':
                $taskLists = $taskListRepository->findListsOwnedBy($this->getUser());
                break;
            case 'contributing':
                $taskLists = $taskListRepository->findListsContributedBy($this->getUser());
                break;
            case 'active':
                $taskLists = $taskListRepository->findActive($this->getUser());
                break;
            case 'archived':
                $taskLists = $taskListRepository->findArchived($this->getUser());
                break;
            default:
                $taskLists = $taskListRepository->findAllFor($this->getUser());
        }

        return $this->render('tasks/index.html.twig', [
            'task_lists' => $taskLists,
        ]);
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     */
    public function create(ManagerRegistry $managerRegistry, Request $request)
    {
        $entityManager = $managerRegistry->getManagerForClass(TaskList::class);
        $list = new TaskList($this->getUser(), $request->request->get('name'));

        $entityManager->persist($list);
        $entityManager->flush();
        $entityManager->clear();

        return $this->redirectToRoute('tasklist_list');
    }

    /**
     * @Route("/update/{id}", name="item_update", methods={"POST"})
     */
    public function update(ManagerRegistry $managerRegistry, TaskItem $taskItem, Request $request)
    {
        $entityManager = $managerRegistry->getManagerForClass(TaskItem::class);

        if ($taskItem->isDone()) {
            $taskItem->reopen();
        } else {
            $taskItem->close();
        }

        $entityManager->flush();
        $entityManager->clear();

        return $this->redirectToRoute('tasklist_show', ['id' => $taskItem->getList()->getId()]);
    }

    /**
     * @Route("/contributors/{id}", name="contributors", methods={"GET", "POST"})
     */
    public function contributors(ManagerRegistry $managerRegistry, TaskList $taskList, Request $request)
    {
        $form = $this->createForm(ContributorType::class, null, ['list' => $taskList]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newContributor = $form->get('contributor')->getData();

            $taskList->addContributor($newContributor);

            $entityManager = $managerRegistry->getManagerForClass(TaskItem::class);
            $entityManager->flush();
            $entityManager->clear();

            return $this->redirectToRoute('tasklist_show', ['id' => $taskList->getId()]);
        }

        return $this->render('tasks/contributors.html.twig', [
            'task_list' => $taskList,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(TaskList $taskList)
    {
        return $this->render('tasks/show.html.twig', ['task_list' => $taskList]);
    }

    /**
     * @Route("/{id}", name="add", methods={"POST"})
     */
    public function add(ManagerRegistry $managerRegistry, TaskList $taskList, Request $request)
    {
        $entityManager = $managerRegistry->getManagerForClass(TaskList::class);
        $taskList->addItem($request->request->get('summary'));

        $entityManager->flush();
        $entityManager->clear();

        return $this->redirectToRoute('tasklist_show', ['id' => $taskList->getId()]);
    }
}
