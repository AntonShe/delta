<?php

namespace common\models\user;

trait UserTrait
{

    /**
     * @return array
     */
    protected function getUserParams(): array
    {
        $params = [];

        if (!\Yii::$app->user->isGuest) {
            $params['userId'] = \Yii::$app->user->getId();
        }

        $identifier = \Yii::$app->session->get(\Yii::$app->user::TOKEN_KEY);

        if (is_null($identifier)) {
            $identifier = \Yii::$app->request->cookies->get(\Yii::$app->user::TOKEN_KEY);
        }

        if (!is_null($identifier)) {
            $params['sessionKey'] = is_string($identifier) ? $identifier : $identifier->value;;
        }

        return $params;
    }
}