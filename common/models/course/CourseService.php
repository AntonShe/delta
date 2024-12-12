<?php

namespace common\models\course;

class CourseService
{
    protected CourseRepository $courseRepository;

    public function __construct()
    {
        $this->courseRepository = new CourseRepository();
    }

    public function setParams($params): void
    {
        $this->courseRepository->setParams($params);
    }

    public function getCourses(): array
    {
        return $this->courseRepository->getCourses();
    }
}