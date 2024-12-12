<?php

namespace common\models\search;

class ManticoreSearchPDO
{

    protected \PDO $connection;

    public function __construct()
    {
        $this->connection = new \PDO(
            'mysql:host=' . \Yii::$app->params['manticore_host'] . ';port=' . \Yii::$app->params['manticore_port_mysql'],
            '',
            '',
            [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT            => 1,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );
    }

    /**
     * @return \PDO
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }
}