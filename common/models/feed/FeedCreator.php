<?php

namespace common\models\feed;

class FeedCreator
{
    protected FeedBuilder $builder;

    public function __construct(FeedBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string $params
     * @return void
     */
    public function create(string $params): void
    {
        $this->builder->setParams($params);
        $this->builder->create();
    }
}