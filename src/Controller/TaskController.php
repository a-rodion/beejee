<?php
declare(strict_types=1);

namespace Rodion\TaskList\Controller;

use Rodion\TaskList\Core\Factory;
use Rodion\TaskList\Helper\AntiXss;
use Rodion\TaskList\Model\Entity\TaskEntity;

/**
 *
 */
class TaskController extends BaseController
{
    public function actionList(): void
    {
        $isAuthorized = $this->isUserAuthorized();
        $this->render('index', [
            'isAuthorized'  => $isAuthorized,
        ]);
    }

    public function actionDataSource(): void
    {
        $orderBy = (string) ($_GET['sort'] ?? 'id');
        $orderAscending = (string) ($_GET['asc'] ?? 'false');
        $orderAscendingFlag = ($orderAscending === 'true');
        $limit = (int) ($_GET['limit'] ?? 3);
        $offset = (int) ($_GET['offset'] ?? 0);

        $taskMapper = Factory::getTaskMapper();
        $taskList = $taskMapper->getList($limit, $offset, $orderBy, $orderAscendingFlag);
        foreach ($taskList as $taskEntity) {
            $taskEntity->userName = AntiXss::encode($taskEntity->userName);
            $taskEntity->userEmail = AntiXss::encode($taskEntity->userEmail);
            $taskEntity->taskText = AntiXss::encode($taskEntity->taskText);
        }

        $data = [
            'total' => $taskMapper->getRowCount(),
            'rows'  => $taskList,
        ];
        echo json_encode($data);
    }

    public function actionCreate(): void
    {
        if (!empty($_POST)) {
            $taskEntity = new TaskEntity();
            $taskEntity->userName = $_POST['userName'];
            $taskEntity->userEmail = $_POST['userEmail'];
            $taskEntity->taskText = $_POST['taskText'];
            $taskEntity->isCompleted = $this->isUserAuthorized()
                ? isset($_POST['isCompleted'])
                : false;
            $taskEntity->isEdited = false;

            $success = false;
            $errors = $taskEntity->validate();
            if (empty($errors)) {
                $success = Factory::getTaskMapper()->insertTask($taskEntity);
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode([
                    'success' => $success,
                    'errors' => $errors,
                ]);
            }
        } else {
            $this->render('taskForm', ['isAuthorized' => $this->isUserAuthorized()]);
        }
    }

    public function actionUpdate(): void
    {
        $isAuthorized = $this->isUserAuthorized();
        if (!$isAuthorized) {
            http_response_code(403);
            exit('Forbidden');
        }

        $id = (int) $_GET['id'];
        $taskMapper = Factory::getTaskMapper();
        $taskEntity = $taskMapper->getOne($id);
        if (!$taskEntity) {
            http_response_code(404);
            exit('Not found');
        }

        if (!empty($_POST)) {
            $taskEntity->isEdited = ($_POST['taskText'] !== $taskEntity->taskText);
            $taskEntity->taskText = $_POST['taskText'];
            $taskEntity->isCompleted = isset($_POST['isCompleted']);

            $success = false;
            $errors = $taskEntity->validate();
            if (empty($errors)) {
                $success = $taskMapper->updateTask($taskEntity);
                echo json_encode(['success' => $success]);
            } else {
                echo json_encode([
                    'success' => $success,
                    'errors' => $errors,
                ]);
            }

        } else {
            $this->render('taskForm', [
                'taskEntity'    => $taskEntity,
                'isAuthorized'  => $isAuthorized,
            ]);
        }
    }
}
