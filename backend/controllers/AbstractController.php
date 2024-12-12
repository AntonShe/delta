<?php

namespace backend\controllers;

use common\models\Pagination;
use Yii;
use yii\base\UnknownClassException;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use common\models\AbstractDataValidator;
use common\models\AbstractParamsValidator;
use yii\filters\AccessControl;
use yii\web\Response;

class AbstractController extends Controller
{
    protected AbstractDataValidator $dataValidator;
    protected AbstractParamsValidator $paramsValidator;
    protected Pagination $pagination;
    protected array $data = [];
    protected array $params = [];
    protected array $rolesList;
    protected bool $isLocal = false;

    public $request;

    /**
     * @throws UnknownClassException
     */
    public function __construct($id, $module, $config = [])
    {
        $host = explode(":", $_SERVER['HTTP_HOST'])[0];
        $this->isLocal = \Yii::$app->params['localserver'] && $host == 'localhost';
        $this->rolesList = array_keys(Yii::$app->authManager->getRoles());
        $this->pagination = Pagination::getInstance();
        $this->request = Yii::$app->request;
        $this->data = array_merge($this->request->post(), $_FILES);
        $this->params = $this->request->get();

        try {
            $this->dataValidator = $this->loadValidator($id, 'Data');
            $this->paramsValidator = $this->loadValidator($id, 'Params');
        } catch (\Exception $e) {
            $this->asJson([
                'errors' => [$e->getMessage()]
            ]);
            $this->response->send();
        } catch (\Throwable $e) {
            $this->asJson([
                'errors' => $e->getMessage()
            ]);
            $this->response->send();
        }

        parent::__construct($id, $module, $config);
    }

    public function getIsLocal(): bool
    {
        return $this->isLocal;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => $this->rolesList,
            ],
        ];
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!Yii::$app->user->isGuest && !Yii::$app->user->isAdmin())  {
            $this->redirect(Yii::$app->urlManagerFrontEnd->createUrl('site/error'));
            return false;
        }

        if (!empty($this->data)) {
            $this->validate($this->data, $this->dataValidator);
            $this->data = $this->dataValidator->getData();
        }
        if (!empty($this->params)) {
            $this->validate($this->params, $this->paramsValidator);
            $this->params = $this->paramsValidator->getData();
        }

        if (isset($this->params['page'])) {
            $this->pagination->setCurrentPage($this->params['page']);
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    private function loadValidator(string $name, string $type): AbstractDataValidator|AbstractParamsValidator
    {
        $snakeCaseName = str_replace('-', '_', $name);
        $array = explode('\\', get_called_class());
        $camelCaseName = str_replace('Controller', '', end($array));

        $dataValidatorName = "common\models\\$snakeCaseName\\$camelCaseName" . ucfirst($type) . 'Validator';

        return new $dataValidatorName();
    }

    private function validate($data, $validator)
    {
        try {
            if (!$validator->load($data, '') || !$validator->validate()) {
                $this->asJson([
                    'errors' => $this->formatErrors($validator->errors)
                ]);
                $this->response->send();
            }
        } catch (\Exception $e) {
            $this->asJson([
                'errors' => $e->getMessage()
            ]);
            $this->response->send();
        } catch (\Throwable $e) {
            $this->asJson([
                'errors' => $e->getMessage()
            ]);
            $this->response->send();
        }
    }

    protected function formatErrors(array $errors): array
    {
        $output = [];
        foreach ($errors as $field => $errorArray) {
            $output[] = "Поле '{$field}': " . implode(' ', $errorArray);
        }
        return $output;
    }
}
