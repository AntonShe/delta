<?php

namespace common\models\pin;

class PinService
{
    protected PinRepository $repository;

    public function __construct()
    {
        $this->repository = new PinRepository();
    }

    public function setParams(array $params): void
    {
        $this->repository->setParams($params);
    }

    public function getPin(): array
    {
        return $this->repository->getPin();
    }

    public function createPin(): array
    {
        $this->repository->removeAllOldPins();

        return $this->repository->createPin();
    }

    public function removePin(): void
    {
        $this->repository->removePin();
    }

    public function getLastPin(): array
    {
        return $this->repository->getLastPin();
    }
}