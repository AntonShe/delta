<?php

namespace common\models\api\tokens;

class TokenService
{
    protected TokenRepository $tokenRepository;

    public function __construct()
    {
        $this->tokenRepository = new TokenRepository();
    }

    public function setParams(array $params): void
    {
        $this->tokenRepository->setParams($params);
    }

    public function getToken(): array
    {
        return $this->tokenRepository->getToken();
    }

    public function createToken(): bool
    {
        return $this->tokenRepository->createToken();
    }
}