<?php

namespace common\models\redirect;

use common\models\AbstractRepository;
use Yii;
use yii\db\Connection;
use yii\db\Exception;

class RedirectRepository extends AbstractRepository
{
    protected Connection $db;

    public function __construct()
    {
        $this->db = Yii::$app->get('db');
        parent::__construct();
    }

    /**
     * @param $id
     * @return array|bool
     * @throws Exception
     */
    public function getRedirectId($id): array|bool
    {
        return $this->db->createCommand("
                SELECT id_to
                FROM redirects
                WHERE id_from = $id
        ")->queryOne();

    }
}