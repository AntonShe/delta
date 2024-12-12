<?php
namespace common\models\userProfile;

use backend\controllers\AbstractController;

class UserProfileController extends AbstractController
{
    protected UserProfileService $userProfileService;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->userProfileService = new UserProfileService();
    }
}