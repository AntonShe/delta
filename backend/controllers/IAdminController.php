<?php

namespace backend\controllers;

interface IAdminController
{
    /**
     * To display content on main page;
     * @return array;
     */
    public function actionIndex(): array;

    /**
     * To create new records in DB;
     * @return array;
     */
    public function actionCreate(): array;

    /**
     * To update existing records in DB;
     * @return array;
     */
    public function actionUpdate(): array;

    /**
     * To delete/mark as deleted existing records in DB;
     * @return array;
     */
    public function actionDelete(): array;
}