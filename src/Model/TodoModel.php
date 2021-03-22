<?php

namespace App\Model;

use Symfony\Component\HttpFoundation\Session\Session;

class TodoModel
{
    private static $session = null;

    private static $initTodos = [
        [
            'task' => 'Installer Symfony',
            'status' => 'done',
            'created_at' => '2017-05-12 14:12:05',
        ],
        [
            'task' => 'Comprendre le Routing',
            'status' => 'undone',
            'created_at' => '2017-06-24 09:52:31',
        ],
        [
            'task' => 'Comprendre les Contrôleurs',
            'status' => 'undone',
            'created_at' => '2017-07-02 18:36:45',
        ],
    ];


    private static function checkSession()
    {
        if (self::$session == null) {
            self::$session = new Session();
        }

        if (self::$session->get('todos', false) === false) {
            self::$session->set('todos', self::$initTodos);
        }
    }

    private static function setTodos($todos)
    {
        self::checkSession();
        self::$session->set('todos', $todos);
    }

    private static function getTodos()
    {
        self::checkSession();
        return self::$session->get('todos');
    }

    public static function find($id)
    {

        $tasks = self::getTodos();
        if (isset($tasks[$id])) {
            return $tasks[$id];
        }
        return false;
    }

    public static function setStatus($id, $status)
    {
        $tasks = self::getTodos();
        if (isset($tasks[$id])) {
            $tasks[$id]['status'] = $status;
            self::setTodos($tasks);
            return true;
        }
        return false;
    }


    public static function delete($id)
    {
        $tasks = self::getTodos();
        if (isset($tasks[$id])) {
            unset($tasks[$id]);
            self::setTodos($tasks);
            return true;
        }
        return false;
    }

    public static function findAll()
    {
        return self::getTodos();
    }

    public static function add($title)
    {
        $date = new \DateTime();

        $task = [
            'task' => $title,
            'status' => 'undone',
            'created_at' => $date->format('Y-d-m H:i:s'),
        ];

        $todos = self::getTodos();
        $todos[] = $task;

        self::setTodos($todos);
    }

    public static function reset()
    {
        self::checkSession();
        self::$session->set('todos', self::$initTodos);
    }
}
