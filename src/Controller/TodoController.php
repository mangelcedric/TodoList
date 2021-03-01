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

        // $todo est soit un tableau soit la valeur false
        // si $todo == false ça veut dire que la tache n'existe pas 
        // on genere donc une 404
        if($todo == false) {
            throw $this->createNotFoundException();
        }

        return $this->render('todo/single.html.twig', [
            'todo' => $todo
        ]);
    }

    /**
     * Changement de statut
     *
     * @Route("/{id}/{status}",
     *          name="set_status",
     *          requirements={"id": "\d+", "status": "(done|undone)" },
     *          methods={"POST"}
     * )
     * Autre solution pour la RegEx : "(un)?done"
     */
    public function setStatus($id, $status)
    {
        // on recupere la valeur de retour de setStatus
        $result = TodoModel::setStatus($id, $status);

        // si $result vaut true on ajoute un msg flash de succes 
        // sinon un msg flash d'erreur
        if ($result) {
            $this->addFlash('success', 'La tâche à été marquée comme ' . $status);
        } else {
            $this->addFlash('danger', 'La tâche n\'a pas été modifiée car elle n\'existe pas');
        }
        
        return $this->redirectToRoute('todo_list');
    }

    /**
     * Ajout d'une tâche
     *
     * @Route("/add", name="add", methods={"POST"})
     *
     * La route est définie en POST parce qu'on veut ajouter une ressource sur le serveur
     */
    public function add(Request $request)
    {   
        // $request contient toutes les informations de la requête
        // $request->request contient toutes les données en POST
        $task = $request->request->get('task');
        // Pour ajouter une tâche, on utilise la méthode add de TodoModel
        TodoModel::add($task);

        // une fois la tache ajoutée un ajoute un msg flash
        $this->addFlash('success', 'La tâche ' . $task . ' a bien été ajoutée');
 
        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id": "\d+"}, methods={"POST"})
     * 
     */
    public function delete(int $id)
    {
        // on supprime la tache de la session
        $result = TodoModel::delete($id);

        if ($result) {
            $this->addFlash('success', 'la tâche a bien été supprimée');
        } else {
            $this->addFlash('danger', 'Vous essayez de supprmier une tâche qui n\'existe pas!');
        }
        // on redirige l'utilisateur en GET sur la page des taches
        return $this->redirectToRoute('todo_list');
    }

    /**
     * @Route("s/reset", name="reset")
     */
    public function reset(Request $request)
    {
        // On pourrait autoriser la réinitialisation de la liste des tâches que si on est en dev
        // On récupère l'environnement dans $request
        $env = $request->server->get('APP_ENV');
        // Si on est en dev, on fait le reset
        if ($env == 'dev') {
            // On réinitialise la liste des tâches
            TodoModel::reset();
        }
        // Autorisé ou non, on redirige vers la page de la liste
        return $this->redirectToRoute('todo_list');
    }
}

