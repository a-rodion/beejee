<?php
declare(strict_types=1);

namespace Rodion\TaskList\Model\Entity;

use EmailValidation\EmailValidatorFactory;

/**
 *
 */
class TaskEntity
{
    /** @var int */
    public $id;
    /** @var string */
    public $userName;
    /** @var string */
    public $userEmail;
    /** @var string */
    public $taskText;
    /** @var bool */
    public $isCompleted;
    /** @var bool */
    public $isEdited;

    /**
     * @return string[]
     */
    public function validate(): array
    {
        $errors = [];

        if (($this->userName == '') || (\strlen($this->userName) > 30)) {
            $errors[] = 'userName';
        }

        $validator = EmailValidatorFactory::create($this->userEmail);
        $emailValidationResult = $validator->getValidationResults()->asArray();
        if ($emailValidationResult['valid_format'] == false) {
            $errors[] = 'userEmail';
        }

        if (($this->taskText == '') || (\strlen($this->userName) > 1000)) {
            $errors[] = 'taskText';
        }

        return $errors;
    }
}
