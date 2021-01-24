<?php

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{

    private $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Todo::class);
        $this->entityManager = $entityManager;
    }

    public function addTodo($task, $email, $date, $status)
    {
        $todo = new Todo();
        $todo->setTask($task)->setEmail($email)->setDate($date)->setStatus($status);

        $this->entityManager->persist($todo);
        $this->entityManager->flush();
    }

    public function deleteTodo(Todo $todo)
    {
        $this->entityManager->remove($todo);
        $this->entityManager->flush();
    }

    public function updateTodo($id, $task, $email, $date, $status)
    {
        $todo = $this->findOneBy(['id' => $id]);
        $todo->setTask($task)->setEmail($email)->setDate($date)->setStatus($status);

        $this->entityManager->persist($todo);
        $this->entityManager->flush();
    }

    public function addManyTodos(array $todos, $email)
    {

        $this->entityManager->createQuery("DELETE FROM App\Entity\Todo todo WHERE todo.email = :email")->setParameter("email", $email)->getResult();
        foreach ($todos as $todo) {
            $date = new \DateTime($todo['date']);
            $todoEntity = new Todo();
            $todoEntity->setTask($todo['task'])->setDate($date)->setEmail($todo['email'])->setStatus($todo['status']);

            $this->entityManager->persist($todoEntity);
        }
        $this->entityManager->flush();
    }

    public function getOne($id)
    {
        $columns = new ResultSetMapping();
        $columns->addScalarResult('task', 'task');
        $columns->addScalarResult('date', 'date');
        $result = $this->entityManager->createNativeQuery("SELECT task, date FROM todo where id = :id", $columns)->setParameter('id', $id)->getResult();
        return $result;
    }

    public function getByEmail($email): array
    {
        $columns = new ResultSetMapping();
        $columns->addScalarResult('id', 'id');
        $columns->addScalarResult('task', 'task');
        $columns->addScalarResult('date', 'date');
        $columns->addScalarResult('email', 'email');
        $columns->addScalarResult('status', 'status');
        $query = "SELECT * FROM todo WHERE email = :email";
        $result = $this->entityManager->createNativeQuery($query, $columns)->setParameter("email", $email)->getResult();

        return $result;
    }
}
