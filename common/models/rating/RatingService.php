<?php

namespace common\models\rating;

class RatingService
{
    protected RatingRepository $ratingRepository;
    protected string $template = '';

    public function __construct()
    {
        $this->ratingRepository = new RatingRepository();
    }

    public function setParams($params): void
    {
        $this->ratingRepository->setParams($params);
    }

    public function getRatingByProductAndUser(): int
    {
        $this->ratingRepository->setUserIdInParams();
        return $this->ratingRepository->getRatingByProductAndUser();
    }

    public function setRating(): array
    {
        $this->ratingRepository->setUserIdInParams();
        $id = $this->ratingRepository->checkIsExist();

        if ($id) {
            return $this->ratingRepository->update($id);
        } else {
            return $this->ratingRepository->create();
        }
    }
}