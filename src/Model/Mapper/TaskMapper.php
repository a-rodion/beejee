<?php
declare(strict_types=1);

namespace Rodion\TaskList\Model\Mapper;

use PDO;
use Rodion\TaskList\Model\Entity\TaskEntity;

/**
 *
 */
class TaskMapper
{
    /** @var PDO */
    protected $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * @return TaskEntity[]
     */
    public function getList(int $limit, int $offset, string $orderBy, bool $orderAscending): array
    {
        $orderAscendingLiteral = $orderAscending ? 'ASC' : 'DESC';
        $allowedOrderCols = ['id', 'userName', 'userEmail', 'isCompleted'];
        if (!\in_array($orderBy, $allowedOrderCols)) {
            $orderBy = 'id';
        }
        $stmt = $this->db->prepare(
            "SELECT * FROM task ORDER BY {$orderBy} {$orderAscendingLiteral} LIMIT :offset, :limit"
        );
        $stmt->bindParam('offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $taskEntity = $this->fromDbRow($row);
            $result[] = $taskEntity;
        }

        return $result;
    }

    public function getOne(int $id): ?TaskEntity
    {
        $stmt = $this->db->prepare('SELECT * FROM task WHERE id = :id');
        $stmt->bindParam('id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return $this->fromDbRow($row);
    }

    public function insertTask(TaskEntity $taskEntity): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO task(userName, userEmail, taskText, isCompleted, isEdited) 
                   VALUES (:userName, :userEmail, :taskText, :isCompleted, :isEdited)'
        );
        $params = $this->stmtParams($taskEntity);
        foreach ($params as $param => $val) {
            $stmt->bindParam($param, $val[0], $val[1]);
        }

        return $stmt->execute();
    }

    public function updateTask(TaskEntity $taskEntity): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE task SET 
                       userName = :userName,
                       userEmail = :userEmail,
                       taskText = :taskText,
                       isCompleted = :isCompleted,
                       isEdited = :isEdited
                   WHERE 
                       id = :id'
        );
        $stmt->bindParam('id', $taskEntity->id, PDO::PARAM_INT);
        $params = $this->stmtParams($taskEntity);
        foreach ($params as $param => $val) {
            $stmt->bindParam($param, $val[0], $val[1]);
        }

        return $stmt->execute();
    }

    public function getRowCount(): int
    {
        return (int) $this->db
                        ->query('SELECT COUNT(*) FROM task')
                        ->fetchColumn();
    }

    protected function stmtParams(TaskEntity $taskEntity): array
    {
        return [
            'userName'      => [$taskEntity->userName, PDO::PARAM_STR],
            'userEmail'     => [$taskEntity->userEmail, PDO::PARAM_STR],
            'taskText'      => [$taskEntity->taskText, PDO::PARAM_STR],
            'isCompleted'   => [$taskEntity->isCompleted, PDO::PARAM_INT],
            'isEdited'      => [$taskEntity->isEdited, PDO::PARAM_INT],
        ];
    }

    protected function fromDbRow(array $row): TaskEntity
    {
        $taskEntity = new TaskEntity();
        $taskEntity->id = (int) $row['id'];
        $taskEntity->userName = $row['userName'];
        $taskEntity->userEmail = $row['userEmail'];
        $taskEntity->taskText = $row['taskText'];
        $taskEntity->isCompleted = (bool) $row['isCompleted'];
        $taskEntity->isEdited = (bool) $row['isEdited'];

        return $taskEntity;
    }
}
