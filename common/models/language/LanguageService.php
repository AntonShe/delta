<?php

namespace common\models\language;

class LanguageService
{
    protected LanguageRepository $languageRepository;

    public function __construct()
    {
        $this->languageRepository = new LanguageRepository();
    }

    public function setParams($params): void
    {
        $this->languageRepository->setParams($params);
    }

    public function getLanguages(): array
    {
        return $this->languageRepository->getLanguages();
    }
}