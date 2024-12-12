<?php

namespace common\models\feed;

use common\models\genre\GenreRepository;
use common\models\product\ProductRepository;
use yii\db\Exception;

class FeedService
{
    protected ProductRepository $productRepository;
    protected GenreRepository $genreRepository;


    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->genreRepository = new GenreRepository();
    }


    /**
     * @param array $ids
     * @return array
     * @throws Exception
     */
    public function getProductByGenres(array $ids): array
    {
        return $this->productRepository->getProductByGenre($ids);
    }


    /**
     * @param int $id
     * @param $
     * @param bool $isCourse
     * @return array
     */
    public function getCoursesByIdProduct(int $id, bool $isCourse = true): array
    {
        return $this->genreRepository->getCourses($id, isCourse: $isCourse);
    }

    /**
     * @param array $ids
     * @return array
     * @throws Exception
     */
    public function getGenresByParentIds(array $ids): array
    {
        return $this->genreRepository->getGenresByParentIds($ids);
    }

}