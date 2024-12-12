<?php

namespace frontend\controllers;

use common\models\AbstractDataValidator;
use common\models\AbstractParamsValidator;
use common\models\Pagination;
use Yii;
use yii\base\UnknownClassException;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Cookie;
use yii\web\Response;

class AbstractController extends Controller
{
    const EXCEPTION_ROUTES = [
        'site/cart',
        'site/profile',
        'order/index'
    ];

    protected AbstractDataValidator $dataValidator;
    protected AbstractParamsValidator $paramsValidator;
    protected Pagination $pagination;
    protected array $data = [];
    protected array $params = [];
    protected bool $isLocal = false;
    protected bool $isGuest = true;

    public $request;
    public $needSearch = true;
    public string $title = 'DeltaBook. Иностранная литература.';
    public string $metaTitle = 'DeltaBook. Иностранная литература.';
    public string $metaDescription = 'DeltaBook. Иностранная литература.';

    /**
     * @throws UnknownClassException
     */
    public function __construct($id, $module, $config = [])
    {
        $host = explode(":", $_SERVER['HTTP_HOST'])[0];
        $this->isLocal = \Yii::$app->params['localserver'] && $host == 'localhost';
        $this->isGuest = \Yii::$app->user->isGuest;

        $this->sessionChecker();
        Yii::$app->user->setCartBooks();
        Yii::$app->user->setFavoriteBooks();

        $this->pagination = Pagination::getInstance();
        $this->pagination->setForSite(true);
        $this->request = Yii::$app->request;
        $this->response = Yii::$app->response;
        $this->data = $this->request->post();
        $this->params = $this->request->get();

        try {
            $this->dataValidator = $this->loadValidator($id, 'Data');
            $this->paramsValidator = $this->loadValidator($id, 'Params');
        } catch (\Exception $e) {
            $this->asJson([
                'errors' => [$e->getMessage()]
            ]);
            $this->response->send();
        }

        parent::__construct($id, $module, $config);
    }

    public function getIsLocal(): bool
    {
        return $this->isLocal;
    }

    public function getIsGuest(): bool
    {
        return $this->isGuest;
    }

    protected function sessionChecker(): void
    {
        $session = Yii::$app->session;
        $identifierSesVal = $session->get(\Yii::$app->user::TOKEN_KEY);

        if (!is_null($identifierSesVal)) {
            return;
        }

        $cookies = Yii::$app->request->cookies;
        $identifierCookVal = $cookies->get(\Yii::$app->user::TOKEN_KEY);

        if (!is_null($identifierCookVal)) {
            $session->set(\Yii::$app->user::TOKEN_KEY, $identifierCookVal);

             return;
        }

        $newIdentifier = $this->generateNewIdentifier();
        $session->set(\Yii::$app->user::TOKEN_KEY, $newIdentifier);
        $cookies = Yii::$app->response->cookies;
        $cookies->add(new Cookie([
            'name' => \Yii::$app->user::TOKEN_KEY,
            'value' => $newIdentifier,
            'expire' => time() + 2592000,
        ]));
    }

    private function generateNewIdentifier(): string
    {
        $identifier = md5(time());

        Yii::$app->user->setParams([
            'BySession' => [
                'sessionKey' => $identifier,
                'userType' => 2
            ]
        ]);

        $user = Yii::$app->user->getUsers();

        if (empty($user['users'])) {
            return $identifier;
        } else {
            return $this->generateNewIdentifier();
        }
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!Yii::$app->user->isGuest && Yii::$app->user->isAdmin())  {
            Yii::$app->user->logout();
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

        //TODO: Удалить после переделки поиска
        $this->needSearch = !in_array($this->getRoute(), self::EXCEPTION_ROUTES);

        return parent::beforeAction($action);
    }

    public function addParams(array $params): void
    {
        $this->paramsValidator->load($params, '');

        if ($this->paramsValidator->validate()) {
            $this->params = \array_merge($this->params, $params);
        }
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
        $validator->load($data, '');

        if (!$validator->validate()) {
            $this->asJson([
                'errors' => $this->formatErrors($validator->errors)
            ]);
            $this->response->send();

            die();//:З
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

    /**
     * @param string $template
     * @param array $list
     * @return string
     */
    protected function getHtml(string $template, array $list): string
    {
        $output = '';

        foreach ($list as $item) {
            $output .= $this->renderPartial($template, ['data' => $item]);
        }

        return $output;
    }
}
