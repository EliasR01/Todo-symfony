<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use App\Repository\TodoRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TodoController extends AbstractController
{

    private $todoRepository;

    public function __construct(TodoRepository $repository)
    {
        $this->todoRepository = $repository;
    }

    /**
     * @Route("todo/get_all", name="get_all_todos", methods={"GET"})
     */

    public function getAll(): JsonResponse
    {

        $todos = $this->todoRepository->findAll();
        $data = [];
        foreach ($todos as $todo) {
            $data[] = [
                'id' => $todo->getId(),
                'task' => $todo->getTask(),
                'email' => $todo->getEmail(),
                'date' => $todo->getDate(),
                'status' => $todo->getStatus(),
            ];
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("todo/add", name="add_todo", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $task = $data['task'];
        $date = new \DateTime($data['date']);
        $email = $data['email'];
        $status = $data['status'];

        $this->todoRepository->addTodo($task, $email, $date, $status);

        return new JsonResponse(['status' => 'Todo created successfully!'], Response::HTTP_OK);
    }

    /**
     * @Route("todo/delete/{id}", name="delete_todo", methods={"DELETE"});
     */
    public function delete($id): JsonResponse
    {
        $todo = $this->todoRepository->findOneBy(['id' => $id]);

        $this->todoRepository->deleteTodo($todo);

        return new JsonResponse(['status' => 'Todo deleted successfully!'], Response::HTTP_OK);
    }

    /**
     * @Route("todo/update/{id}", name="update_todo", methods={"PUT"})
     */
    public function update($id, Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $task = $data['task'];
        $date = new \DateTime($data['date']);
        $email = $data['email'];
        $status = $data['status'];

        $this->todoRepository->updateTodo($id, $task, $email, $date, $status);

        return new JsonResponse(['status' => 'Todo updated successfully!'], Response::HTTP_OK);
    }

    /**
     * @Route("todo/add_many", name="add_many_todos", methods={"POST"})
     */
    public function addMany(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // var_dump($data);
        // die();
        $newTodos = [];
        $email = "";
        foreach ($data as $todo) {
            $email = $todo['email'];
            $newTodos[] = [
                // 'id' => $todo['id'],
                'task' => $todo['task'],
                'email' => $todo['email'],
                'date' => $todo['date'],
                'status' => $todo['status'],
            ];
        }

        $this->todoRepository->addManyTodos($newTodos, $email);

        return new JsonResponse(['status' => 'Todos created successfully!'], Response::HTTP_OK);
    }

    /**
     * @Route("todo/get_by_email", name="get_by_email", methods={"GET"})
     */
    public function getByEmail(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $todos = $this->todoRepository->getByEmail($email);

        return new JsonResponse($todos, Response::HTTP_OK);
    }

    /**
     * @Route("todo/get/{id}", name="get_todo", methods={"GET"})
     */
    public function getOne($id): JsonResponse
    {
        $todo = $this->todoRepository->getOne($id);
        return new JsonResponse($todo, Response::HTTP_OK);
    }
}
