<?php

namespace common\models\genre;

use common\models\AbstractDTO;

class GenreService
{
    protected GenreRepository $genreRepository;

    public function __construct()
    {
        $this->genreRepository = new GenreRepository();
    }

    public function setParams($params): void
    {
        $this->genreRepository->setParams($params);
    }

    public function setOrder($order): void
    {
        $this->genreRepository->setOrder($order);
    }

    public function getGenres(): array
    {
        return $this->genreRepository->getGenres();
    }

    public function getGenresByCatalog(): array
    {
        $list = $this->genreRepository->getGenres();
        foreach ($list as &$item) {
            $item = GenreDTO::make($item);
        }
        return $list;
    }

    public function createGenre(): array
    {
        return $this->genreRepository->create();
    }

    public function updateGenre(int $id): array
    {
        return $this->genreRepository->update($id);
    }

    public function deleteGenre(int $id): array
    {
        return $this->genreRepository->delete($id);
    }

    public function getGenreChildrenByCatalog(int $id, int $minLevel = 0): array
    {
        $output = [];
        foreach ($this->genreRepository->getGenreChildren($id, $minLevel) as $item) {
            $output[] = GenreDTO::make($item);
        }
        return $output;
    }
}