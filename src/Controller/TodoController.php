<?php

namespace App\Controller;

use App\Model\TodoModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/todo", name="todo_")
 */
class TodoController extends AbstractController
{
    /**
     * Liste des tâches
     *
     * @Route("s", name="list", methods={"GET"})
     */
    public function list()
    {
        $todos = TodoModel::findAll();

        return $this->render('todo/list.html.twig', [
            'todos' => $todos,
        ]);
    }

    /**
     * Affichage d'une tâche
     *
     * @Route("/{id}", name="show", methods={"GET"}, requirements={"id" = "\d+"})
     */
    public function show(int $id)
    {
        $todo = TodoModel::find($id);
        if($todo == false) {
            throw $this->createNotFoundException();
        }

        return $this->render('todo/single.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * @Route("/{id}/{status}",
     *          name="set_status",
     *          requirements={"id": "\d+", "status": "(done|undone)" },
     *          methods={"POST"}
     * )
     * Autre solution pour la RegEx : "(un)?done"
     */
    public function setStatus($id, $status)
    {
        $result = TodoModel::setStatus($id, $status);
        if ($result) {
            $this->addFlash('success', 'La tâche à été marquée comme ' . $status);
        } else {
            $this->addFlash('danger', 'La tâche n\'a pas été modifiée car elle n\'existe pas');
        }
        
        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("/add", name="add", methods={"POST"})
     *
     * La route est définie en POST parce qu'on veut ajouter une ressource sur le serveur
     */
    public function add(Request $request)
    {   
        $task = $request->request->get('task');
        TodoModel::add($task);
        $this->addFlash('success', 'La tâche ' . $task . ' a bien été ajoutée');
 
        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id": "\d+"}, methods={"POST"})
     * 
     */
    public function delete(int $id)
    {
        $result = TodoModel::delete($id);

        if ($result) {
            $this->addFlash('success', 'la tâche a bien été supprimée');
        } else {
            $this->addFlash('danger', 'Vous essayez de supprmier une tâche qui n\'existe pas!');
        }

        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("s/reset", name="reset")
     */
    public function reset(Request $request)
    {
        $env = $request->server->get('APP_ENV');
        if ($env == 'dev') {
            TodoModel::reset();
        }
        return $this->redirectToRoute('todo_list');
    }
}

