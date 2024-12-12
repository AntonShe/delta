<?php

namespace common\models\role;

use yii\base\ExitException;
use yii\rbac\DbManager;

class RoleService
{
    protected DbManager $authManager;

    public function __construct()
    {
        $this->authManager = \Yii::$app->authManager;
    }

    public function getRoles(string|int $id = 0): array
    {
        if ($id === 0) {
            $roles = $this->authManager->getRoles();
        } else if (intval($id) !== 0) {
            $roles = $this->authManager->getAssignments($id);
        } else {
            $roles = $this->authManager->getRole($id);
        }

        return $roles;
    }

    public function setRole(string $name,int $userId): bool
    {
        try {
            $role = $this->authManager->getRole($name);
            $this->authManager->assign($role, $userId);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function removeAll(int $userId): bool
    {
        return $this->authManager->revokeAll($userId);
    }
}