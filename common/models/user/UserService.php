<?php

namespace common\models\user;

use common\models\cart\CartRepository;
use common\models\DefaultFunctions;
use common\models\favorite\FavoriteService;
use common\models\order\OrderService;
use common\models\role\RoleService;
use common\models\search\index\UserIndex;
use common\models\search\ManticoreSearchService;
use common\models\user_info\UserInfoRepository;
use yii\web\Cookie;
use common\models\cart\CartService;
use yii\base\ExitException;
use yii\web\IdentityInterface;
use yii\web\User;
use common\models\api\smsApi\SmsClient;
use common\models\AbstractMailSender;

class UserService extends User
{
    const TOKEN_KEY = 'frontend_identifier';

    const LOGIN_DURATION = 3600 * 24 * 30;//30 days

    const AUTHORIZATION_TYPES = [
        'adminEmailPass' => 0,
        'siteEmailPass' => 1,
        'sitePhoneCode' => 2,
    ];

    private array $cartBooks = [];
    private array $favoriteBooks = [];

    protected UserRepository $userRepository;
    protected CartService $cartService;
    protected OrderService $orderService;
    protected FavoriteService $favoriteService;
    protected RoleService $roleService;
    protected ManticoreSearchService $searchService;
    protected UserInfoRepository $userInfoRepository;

    public $loginUrl = ['admin/login'];

    public function __construct($config = [])
    {
        $this->userRepository = $this->identityClass = new UserRepository();
        $this->cartService = new CartService();
        $this->orderService = new OrderService();
        $this->favoriteService = new FavoriteService();
        $this->roleService = new RoleService();
        $this->userInfoRepository = new UserInfoRepository();
        $this->searchService = new ManticoreSearchService();
        parent::__construct($config);
    }

    public function init()
    {
        parent::init();
    }

    public function isAdmin():bool
    {
        return !is_null($this->identity)
            && $this->identity->getUserType() === UserRepository::ADMIN_USER;
    }

    public function setParams(array $params): void
    {
        $this->userRepository->setParams($params);
    }

    public function getUsers(): array
    {
        if ($searchParam = $this->userRepository->getParam('search')) {
            $this->searchService->setClient('client', new UserIndex());
            $this->searchService->setParams([
                'search' => $searchParam
            ]);

            $ids = $this->searchService->getIdsForSearch();

            if ($ids) {
                $this->userRepository->setParams(['ids' => $ids]);
            }
        }

        return $this->userRepository->getUsers();
    }

    public function createUser(array $data, bool $needAuth = false): bool
    {
        $session = \Yii::$app->session->get(self::TOKEN_KEY);
        $cookie = \Yii::$app->request->cookies->get(self::TOKEN_KEY);

        $data['sessionKey'] = $session ?? $cookie;

        if (!empty($data['password'])) {
            $data['password'] = $this->generatePassHash($data['password']);
        }

        $result = $this->userRepository->createUser($data);

        if ($result && !empty($data['role'])) {
            $this->roleService->setRole($data['role'], $this->userRepository->getId());
        }

        if ($result && $needAuth) {
            $result = \Yii::$app->user->login($this->userRepository, self::LOGIN_DURATION);
        }

       return $result;
    }

    public function updatePassword(array $data): bool
    {
        if (empty($data['password'])) return false;

        $this->setParams($data);

        $user = $this->getUsers()['users'][0];
        $this->setParams(['userId' => $user['id']]);
        $lastPinHash = $this->getLastPinHash();

        if ($data['key'] === $lastPinHash) {
            return $this->updateUser([
                'id' => $user['id'],
                'password' => $data['password']
            ]);
        }

        return false;
    }

    public function updateUser(array $data): bool|string
    {

        if (empty($data['id']) && $this->getId()) $data['id'] = $this->getId();

        if (empty($data['id'])) return $this->isGuest;

        if (!empty($data['password'])) $data['password'] = $this->generatePassHash($data['password']);

        if (!empty($data['profile']['payer']['legalInn'])) {
            $issetInn = $this->userRepository->checkLegalInn($data['profile']['payer']['legalInn'], $data['id']);

            if ($issetInn) return 'Такой ИНН уже существует';
        }

        if (!empty($data['profile']['getter']['legalInn'])) {
            $issetInn = $this->userRepository->checkLegalInn($data['profile']['getter']['legalInn'], $data['id']);

            if ($issetInn) return 'Такой ИНН уже существует';
        }


        return $this->userRepository->updateUser($data);
    }

    public function authUser(array $loginData, int $type): bool
    {
        if(!in_array($type, self::AUTHORIZATION_TYPES, true)) {
            return false;
        }

        $loginData['password'] = isset($loginData['password'])
            ? $this->generatePassHash($loginData['password'])
            : null;

        switch ($type) {
            case self::AUTHORIZATION_TYPES['adminEmailPass']:
                if (!$this->userRepository->getAdminUser($loginData)) return false;
                break;
            case self::AUTHORIZATION_TYPES['sitePhoneCode']://Если пользователь не найден - создаём и авторизуем
                $user = $this->userRepository->getUsers();

                if (empty($user['users'])) {
                    if (!$this->createUser($loginData)) {
                        return false;
                    }
                } else {
                    if ($user['users'][0]['userType'] == $this->userRepository::CUSTOMER_USER) {

                        $this->userRepository->setIdentity($user['users'][0]['id']);
                    } else {
                        return false;
                    }

                }
                break;
            case self::AUTHORIZATION_TYPES['siteEmailPass']:
                $user = $this->userRepository->getUsers();

                if (empty($user['users'])) return false;

                if (
                    $this->validatePass($loginData['password'], $user['users'][0]['password'])
                    && $user['users'][0]['userType'] === $this->userRepository::CUSTOMER_USER
                    && $user['users'][0]['email'] === $loginData['email']
                ) {
                    $this->userRepository->setIdentity($user['users'][0]['id']);
                } else {
                    return false;
                }
                break;
            default:
                return false;
        }

        return $this->login($this->userRepository, self::LOGIN_DURATION);
    }

    public function createPin(): bool|string
    {
        $user = $this->userRepository->getUsers();

        if (
            $this->userRepository->getParam('isNew')
            || $this->userRepository->getParam('isUpdate')
        ) {
            if (!empty($user['users'])) return 'Учетная запись с такими данными уже существует. Авторизуйтесь.';
        }

        if ($this->userRepository->getParam('isUpdate') ?? empty($user['users'])) {
            $this->userRepository->mergeParams([
                'OrId' => $this->getId()
            ]);
            $user = $this->userRepository->getUsers();

            if (empty($user)) return 'Не удалось отправить код. Попробуйте позже.';
        }

        $data = $this->userRepository->createPin($user['users'][0]['id'] ?? null);

        if (!empty($data)) {
            try {
                $mess = " {$data['pin']} - ваш код подтверждения Deltabook.ru";

                if (!empty($data['phone'])) {
                    $this->preparePhone($data['phone']);
                    $sms = new SmsClient();
                    $response = $sms->sendMessage($data['phone'], $mess);

                    return $response['status'] ?? false;
                } elseif (!empty($data['email'])) {
                    $subj = "Ваш код подтверждения Deltabook.ru";
                    $sms = new AbstractMailSender();

                    return $sms->sendMail($subj, $mess, $data['email']);
                }
            } catch (\Exception) {
                return false;
            }
        }

        return false;
    }

    public function getLastPinHash(): string
    {
        $pin = $this->userRepository->getLastPin();

        if (empty($pin)) return '';

        return sha1($pin['id'] . $pin['user_id'] . $pin['pin'] . $pin['date_create']);
    }

    protected function preparePhone(string &$phone): void
    {
        $pattern = "/\d+/";
        $matches =  [];

        preg_match_all($pattern, $phone, $matches);

        if ($matches[0][0] == '8') $matches[0][0] = '7';

        $rawPhone = implode('', $matches[0]);

        $phone = strlen($rawPhone) !== 11 ?  null : '+' . $rawPhone;
    }

    public function verifyPin(): bool
    {
        $pin = $this->userRepository->getPin();

        if ($pin['id'] !== 0) {
            $this->userRepository->removePin($pin['id']);

            return true;
        }

        return false;
    }

    protected function generatePassHash(string $rawPass): string
    {
        return hash(
            'sha256',
            $rawPass . md5($rawPass)
        );
    }

    protected function validatePass(string $pass, string $origPassHash): bool
    {
        return $pass === $origPassHash;
    }

    public function login(IdentityInterface $identity, $duration = 0)
    {
        if (!$identity->isAdmin()) {
            $this->checkFavourite($identity);
            $this->checkCart($identity);
        }

        return parent::login($identity, $duration);
    }

    public function afterLogout($identity)
    {
        parent::afterLogout($identity);

        $cookies = \Yii::$app->response->cookies;
        $cookies->remove(self::TOKEN_KEY);
    }

    public function loginByAccessToken($token, $type = null)
    {
        throw new ExitException(200, 'Function has blocked', 1);

        exit();
    }

    public function getBadge(): array
    {
        $userId = $this->getId();

        if ($userId === null) return [];

        $this->userRepository->setIdentity($userId);

        return $this->userRepository->getBadge() ?? [];
    }

    public function getFullUserInfo(): array
    {
        if (is_null($this->getId())) return [];

        $this->userRepository->setParams([
            'id' => $this->getId()
        ]);

        $fullUser = $this->userRepository->getUsers()['users'][0];
        //var_dump($fullUser);die();
        $fullUser = DefaultFunctions::getInstance()->nullToEmpty($fullUser);

        $user = [
            'isLegal' => $fullUser['profile'][0]['isLegal'],
            'firstName' => $fullUser['firstName'] ?? '',
            'secondName' => $fullUser['secondName'] ?? '',
            'lastName' => $fullUser['lastName'] ?? '',
            'sex' => $fullUser['profile'][0]['sex'],
            'phone' => $fullUser['phone'] ?? '',
            'email' => $fullUser['email'] ?? '',
            'birthday' => $fullUser['profile'][0]['birthday'],
            'isPasswordExist' => !empty($fullUser['password']),
        ];

        $user['isPayerSameGetter'] = !(count($fullUser['profile']) == 2);
        $user['company'] = $fullUser['profile'];

        $this->orderService->setParams([
            'idUser' => $fullUser['id']
        ]);
        $orders = $this->orderService->getOrders();
        $user['canSwitchToLegal'] = count($orders['orders']) < 1;

        return $user;
    }

    /**
     * @param IdentityInterface $identity
     * @return void
     *
     * Метод для проверки и решения конфликтов между несколькими корзинами
     * !!!ТОЛЬКО ПРИ АВТОРИЗАЦИИ!!!
     */
    protected function checkCart(IdentityInterface $identity): void
    {
        $userId = $identity->getId();
        //Получаем текущий токен сессии
        $token = self::getCurrentSessionKey();

        //Получаем новую корзину, которая автоматом создаётся для нового неавторизованого пользователя
        $this->cartService->setParams([
            'sessionKey' => $token
        ]);
        $cartNew = $this->cartService->getCart(false);

        $token = $identity->getSessionKey();
        //Ищем основную корзину, привязанную к пользователю
        $this->cartService->setParams([
            'userId' => $userId,
            'sessionKey' => $token
        ]);
        $cartMain = $this->cartService->getCart(false);

        if ($cartNew['items']) {
            foreach ($cartNew['items'] as $key => $item) {
                if (!key_exists($key, $cartMain['items'])) {

                    $cartRep = new CartRepository();
                    $cartRep->setParams([
                        'productId' => $key,
                        'quantity' => $item['quantity']['cart'],
                        'sessionKey' => $token,

                    ]);

                    if ($cartRep->addToCart($cartMain['id'])) {
                        $this->cartService->recalculateCart();
                    }

                }
            }
            $this->cartService->setParams([
                'id' => $cartNew['id']
            ]);
            $this->cartService->deleteCart();
        }

        //Обновляем токен в куки и сессии
        \Yii::$app->session->set(self::TOKEN_KEY, $token);
        $cookies = \Yii::$app->response->cookies;
        $cookies->remove(self::TOKEN_KEY);
        $cookies->add(new Cookie([
            'name' => self::TOKEN_KEY,
            'value' => $token,
            'expire' => time() + 2592000,
        ]));
    }

    public function getOrders(array $params): array
    {
        $params['idUser'] = \Yii::$app->user->getId();
        $this->orderService->setParams($params);
        $ordersRaw = $this->orderService->getOrders();

        if (empty($ordersRaw)) return [];

        foreach ($ordersRaw['orders'] as $key => $order) {
            $this->orderService->setParams(['id' => $order['id']]);
            $ordersRaw['orders'][$key] = $this->orderService->getOrderFull();
        }

        return $ordersRaw;
    }

    public function getCartBooks(): array
    {
        return $this->cartBooks['items'];
    }

    public function setCartBooks(): void
    {
        $this->cartBooks = $this->cartService->getCart();
    }

    public function getFavoriteBooks(): array
    {
        return $this->favoriteBooks;
    }

    public function setFavoriteBooks(): void
    {
        $this->favoriteBooks = $this->favoriteService->getFavorite();
    }

    public static function getCurrentSessionKey(): string
    {
        $sessionKey = \Yii::$app->session->get(self::TOKEN_KEY);

        if (empty($sessionKey)) {
            $sessionKey = \Yii::$app->request->cookies->get(self::TOKEN_KEY);
        }

        return is_object($sessionKey) ? (string)$sessionKey->value : (string)$sessionKey;
    }

    /**
     * @param IdentityInterface $identity
     * @return void
     */
    protected function checkFavourite(IdentityInterface $identity): void
    {
        $userId = $identity->getId();

        $token = self::getCurrentSessionKey();

        $this->favoriteService->setParams([
            'sessionKey' => $token
        ]);

        $favouritesNew = $this->favoriteService->getFavoriteFull();

        $this->favoriteService->setParams([
            'userId' => $userId
        ]);

        $favouritesMain = $this->favoriteService->getFavorite();

        if (!empty($favouritesNew)) {
            foreach ($favouritesNew as $item) {
                if (!in_array($item['product_id'], $favouritesMain)) {
                    $this->favoriteService->setParams([
                        'id' => $item['id'],
                        'userId' => $userId,
                        'sessionKey' => $identity->getSessionKey()
                    ]);
                    $this->favoriteService->updateFavourite();
                }
            }
        }
    }

    /**
     * @return string|null
     */
    public function getUserCity(): string|null
    {
        $userId = $this->getId();

        if (!$userId) {
            return null;
        }

        $this->userInfoRepository->setParams(['userId' => $userId]);

        return $this->userInfoRepository->getUserInfo()['city'];
    }

    /**
     * @param array $params
     * @return bool
     */
    public function updateUserCity(array $params): bool
    {
        if (!$userId = $this->getId()) {
            return false;
        }

        $params['userId'] = $userId;

        $this->userInfoRepository->setParams(['userId' => $this->getId()]);

        $userInfo = $this->userInfoRepository->getUserInfo();

        if (!$userInfo) {

            $this->userInfoRepository->setParams($params);
            return $this->userInfoRepository->create();
        }

        $params['id'] = $userInfo['id'];
        $this->userInfoRepository->setParams($params);

        return $this->userInfoRepository->update();
    }

    /**
     * @param int $page
     * @return array
     */
    public function getUsersForManticoreSearch(int $page): array
    {
        $users = $this->userRepository->getUsersForManticoreSearch($page);

        foreach ($users['users'] as &$user) {

            $user['fio'] .= $user['firstName'] ? $user['firstName'] . ' ' : '';
            $user['fio'] .= $user['secondName'] ? $user['secondName'] . ' '  : '';
            $user['fio'] .= $user['lastName'] ? $user['lastName'] . ' '  : '';

            if ($user['phone']) {
                $phoneReplace1 = str_replace('+7', '8', $user['phone']);
                $phoneReplace2 = preg_replace('/[\(\)]/','', $user['phone']);

                $phoneReplace3 = preg_replace('/[^a-zа-я0-9]/ui','', $phoneReplace1);
                $phoneReplace4 = preg_replace('/[^a-zа-я0-9\+]/ui','', str_replace(' ', '', $phoneReplace2));

                $phoneWithoutSymbols = preg_replace('/[^a-zа-я0-9]/ui','', $user['phone']);
                $user['phone'] = implode(', ', [
                    $user['phone'], $phoneReplace1, $phoneWithoutSymbols, $phoneReplace2, $phoneReplace3, $phoneReplace4
                ]);
            }
        }

        return $users;
    }
}