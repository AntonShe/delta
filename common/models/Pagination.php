<?php

namespace common\models;

use Exception;
use InvalidArgumentException;
use yii\base\Model;

class Pagination
{
    const DEFAULT_COUNT_ON_PAGE = 40;
    const DEFAULT_COUNT_ON_SIDE = 1;

    protected int $totalCount = 0;
    protected int $currentPage = 1;
    protected int $countOnPage = self::DEFAULT_COUNT_ON_PAGE;
    protected int $pageCount = 1;
    protected bool $forSite = false;

    private static ?Pagination $instance = null;

    public static function getInstance(): Pagination
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(){}

    private function __clone(){}

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public function getCountOnPage(): int
    {
        return $this->countOnPage;
    }

    public function getOffset(): int
    {
        $this->checkCurrentPage();
        return $this->countOnPage * ($this->currentPage - 1);
    }

    public function getData(): array
    {
        if (!$this->forSite) {
            return [
                'currentPage' => $this->currentPage,
                'pageCount' => $this->pageCount,
            ];
        }

        $extraEnd = $this->currentPage + self::DEFAULT_COUNT_ON_SIDE > $this->pageCount ?
            abs($this->pageCount - ($this->currentPage + self::DEFAULT_COUNT_ON_SIDE)) : 0;
        $extraStart = $this->currentPage - 1 - self::DEFAULT_COUNT_ON_SIDE < 0 ?
            abs($this->currentPage - 1 - self::DEFAULT_COUNT_ON_SIDE) : 0;
        $startPage = $this->currentPage - self::DEFAULT_COUNT_ON_SIDE - $extraEnd;
        $endPage = $this->currentPage + self::DEFAULT_COUNT_ON_SIDE + $extraStart;

        if (
            ($this->currentPage - self::DEFAULT_COUNT_ON_SIDE < 1) ||
            ($this->currentPage - self::DEFAULT_COUNT_ON_SIDE - $extraEnd < 1)
        ) {
            $startPage = 1;
        }

        if (
            ($this->currentPage + self::DEFAULT_COUNT_ON_SIDE > $this->pageCount) ||
            ($this->currentPage + self::DEFAULT_COUNT_ON_SIDE + $extraStart > $this->pageCount)
        ) {
            $endPage = $this->pageCount;
        }

        return [
            'currentPage' => $this->currentPage,
            'pageCount' => $this->pageCount,
            'startPage' => $startPage,
            'endPage' => $endPage,
        ];
    }

    public function setTotalCount(int $value): void
    {
        if ($value == 0) {
            $value++;
        }
        $this->setValue('totalCount', $value);
        $this->setValue('pageCount', ceil($this->totalCount / $this->countOnPage));
    }

    public function setCurrentPage(int $value): void
    {
        $this->setValue('currentPage', $value);
    }

    public function setCountOnPage(int $value): void
    {
        $this->setValue('countOnPage', $value);
        $this->setValue('pageCount', ceil($this->totalCount / $this->countOnPage));
    }

    public function setForSite(bool $value): void
    {
        $this->forSite = $value;
    }

    private function setValue(string $name, int $value): void
    {
        if ($value < 1) {
            throw new InvalidArgumentException('Значение должно быть больше 0');
        }

        $this->$name = $value;
    }

    private function checkCurrentPage(): void
    {
        if ($this->currentPage > $this->pageCount) {
            $this->currentPage = $this->pageCount;
        }
    }
}