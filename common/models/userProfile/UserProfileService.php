<?php

namespace common\models\userProfile;

class UserProfileService
{
    protected UserProfileRepository $userProfileRepository;

    public function __construct()
    {
        $this->userProfileRepository = new UserProfileRepository();
    }
}