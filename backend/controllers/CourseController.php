<?php

namespace backend\controllers;

use common\models\course\CourseService;
use Yii;

class CourseController extends AbstractController
{
    protected CourseService $courseService;

    public function __construct($id, $module, $config = [])
    {
        $this->courseService = new CourseService();

        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): array
    {
        $this->courseService->setParams($this->params);

        return $this->courseService->getCourses();
    }
}