<?php

namespace backend\controllers;

use common\models\course\CourseService;
use common\models\description\DescriptionService;
use common\models\genre\GenreService;
use JetBrains\PhpStorm\ArrayShape;

class DescriptionController extends AbstractController implements IAdminController
{
    private CourseService $courseService;
    private GenreService $genreService;

    public function __construct($id, $module, $config = [])
    {
        $this->courseService = new CourseService();
        $this->genreService = new GenreService();
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    public function actionIndex(): array
    {
        if ($this->params['flagCourses']) {
            return $this->courseService->getCourses();
        }

        if ($this->params['flagGenres']) {
            return $this->genreService->getGenres();
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    public function actionCreate(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function actionUpdate(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function actionDelete(): array
    {
        return [];
    }
}