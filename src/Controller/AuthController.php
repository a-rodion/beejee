<?php
declare(strict_types=1);

namespace Rodion\TaskList\Controller;

/**
 *
 */
class AuthController extends BaseController
{
    public function actionIndex(): void
    {
        if ($this->isUserAuthorized()) {
            exit('Already authorized');
        }
        $this->render('loginForm');
    }

    public function actionAuthenticate(): void
    {
        $login = $_POST['login'];
        $pass = $_POST['pass'];
        $success = false;
        if ($login === 'admin' && $pass === '123') {
            $_SESSION['isAdmin'] = true;
            $success = true;
        }
        echo json_encode(['success' => $success]);
    }

    public function actionLogout(): void
    {
        $_SESSION['isAdmin'] = false;
        header('Location: ' . $this->getBaseUrl());
    }
}
