<?php

namespace common\models\user;

use common\models\AbstractRepository;
use common\models\cart\CartService;
use common\models\favorite\FavoriteService;
use common\models\logger\Logger;
use common\models\Pagination;
use common\models\pin\PinRepository;
use common\models\pin\PinService;
use common\models\role\RoleService;
use common\models\user_info\UserInfoRepository;
use common\models\userProfile\UserProfileRepository;
use yii\base\NotSupportedException;
use yii\db\ActiveQuery;
use yii\db\Exception;
use yii\web\IdentityInterface;

class UserRepository extends AbstractRepository implements IdentityInterface
{
    const UNKNOWN_USER  = 1;

    const CUSTOMER_USER  = 2;

    const ADMIN_USER  = 4;

    const USER_TYPES = [
        self::UNKNOWN_USER => 'Пользователь без типа',
        self::CUSTOMER_USER => 'Покупатель',
        self::ADMIN_USER => 'Пользователь панели администратора',
    ];

    protected $id;
    protected $phone;
    protected $email;
    protected $password;
    protected $first_name;
    protected $second_name;
    protected $last_name;
    protected $session_key;
    protected $is_active;
    protected $user_type;
    protected $date_create;
    protected $date_update;

    protected UserProfileRepository $profileRepository;
    protected PinService $pinService;
    protected RoleService $roleService;
    protected Pagination $pagination;
    protected UserInfoRepository $userInfoRepository;

    protected array $fieldsMap = [
        'first_name' => 'firstName',
        'second_name' => 'secondName',
        'last_name' => 'lastName',
        'session_key' => 'sessionKey',
        'is_active' => 'isActive',
        'user_type' => 'userType',
    ];

    protected array $params = [];

    public function __construct()
    {
        $this->entity = new UserEntity();
        $this->profileRepository = new UserProfileRepository();
        $this->pinService = new PinService();
        $this->roleService = new RoleService();
        $this->pagination = Pagination::getInstance();
        $this->userInfoRepository = new UserInfoRepository();

        parent::__construct();
    }

    private function setAttributes(array $attributes): void
    {
        $this->id = $attributes['id'];
        $this->phone = $attributes['phone'];
        $this->email = $attributes['email'];
        $this->password = $attributes['password'];
        $this->first_name = $attributes['first_name'];
        $this->second_name = $attributes['second_name'];
        $this->last_name = $attributes['last_name'];
        $this->session_key = $attributes['session_key'];
        $this->is_active = $attributes['is_active'];
        $this->user_type = $attributes['user_type'];
        $this->date_create = $attributes['date_create'];
        $this->date_update = $attributes['date_update'];
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(), [
            'UserSearch',
            'SearchForPin',
            'BySession',
            'Phone',
            'OrId'
        ]);
    }

    public function getParam(string $param): mixed
    {
        return $this->params[$param] ?? null;
    }

    public function getUsers(): array
    {
        $query = $this->setParamsInQuery($this->entity::find());

        if ($this->withPagination) {
            $this->pagination->setTotalCount($query->count());
            $query = $this->setOffsetLimit($query);
        }

        $users = $query
            ->asArray()
            ->all();

        foreach ($users as &$user) {
            $user = $this->convertField($user);
            $this->profileRepository->setParams(['user_id' => $user['id']]);
            $user['profile'] = $this->profileRepository->getProfiles();
            $user['role'] = array_key_first($this->roleService->getRoles($user['id']));
            $user['isEmployee'] = $user['userType'] === self::ADMIN_USER;
        }
        unset($user);

        return [
            'users' => $users,
            'pagination' => $this->pagination->getData()
        ];
    }

    /**
     * @param array $userData
     * @return bool
     */
    public function getAdminUser(array $userData): bool
    {
        $user = $this->entity::find()
            ->where(['email' => $userData['email']])
            ->andWhere(['password' => $userData['password']])
            ->andWhere(['user_type' => self::ADMIN_USER])
            ->asArray()
            ->one();

        if ($user) {
            $this->setAttributes($user);
            return true;
        }
        return false;
    }

    //для поиска по полям профиля в массиве $attributes должен быть ключ 'profile', со значением массив с полями профиля
    public function getUsersBy(array $attributes): array
    {
        if (empty($attributes)) return [];

        $query = $this->entity::find();
        $isFirst = true;

        foreach ($attributes as $attribute => $value) {
            if ($attributes != 'profile') {
                if ($isFirst) {
                    $isFirst  = false;
                    $query->Where([$attribute => $value]);
                } else {
                    $query->orWhere([$attribute => $value]);
                }
            }
        }

        return $query
            ->indexBy('id')
            ->asArray()
            ->all();
    }

    protected function prepareData($data, &$profilesData, &$userData, $isUpdate = false): void
    {
        //Обработка данных профиля
        $data['profile']['payer']['isPayer'] = 1;

        if (isset($data['isLegal'])) {
            $data['profile']['getter']['isLegal'] = $data['profile']['payer']['isLegal'] = $data['isLegal'];
        }

        if (isset($data['sex'])) {
            $data['profile']['payer']['sex'] =  $data['sex'];
        }

        if (isset($data['birthday'])) {
            $data['profile']['payer']['birthday'] = date('Y-m-d H:i:s', strtotime($data['birthday']));
        }

        if (empty($data['profile']['payer']['id']) && $data['id'] !== null) {
            $searchParams = ['user_id' => $data['id']];

            if ($data['isLegal']) $searchParams['is_payer'] = 1;

            $payer = $this->profileRepository->getProfileBy($searchParams, true);

            if (!empty($payer)) {
                $data['profile']['payer']['id'] = array_key_first($payer);
            }

        }
        $data['profile']['getter']['isPayer'] = 0;

        if (empty($data['profile']['getter']['id']) && $data['id'] !== null) {
            $getter = $this->profileRepository->getProfileBy([
                'user_id' => $data['id'],
                'is_payer' => 0
            ], true);

            if (empty($getter) && $isUpdate  && $data['payerSameGetter'] == 0 && $data['isLegal'] == 1) {
                $dataForCreate = $data['profile']['getter'];
                $dataForCreate['userId'] = $data['id'];
                $dataForCreate['isPayer'] = 0;

                $this->profileRepository->createUserProfile(
                    $this->profileRepository->convertField($dataForCreate, true)
                );

                $getter = $this->profileRepository->getProfileBy([
                    'user_id' => $data['id'],
                    'is_payer' => 0
                ]);
            }

            $data['profile']['getter']['id'] = array_key_first($getter) ?? 0;
        }

        $profilesData[] = $this->profileRepository->convertField($data['profile']['payer'], true, true);

        if ($data['payerSameGetter'] == 0 && $data['isLegal'] == 1) {
            $profilesData[] = $this->profileRepository->convertField($data['profile']['getter'], true, true);
        } else if ($data['payerSameGetter'] == 1 && $data['isLegal'] == 1 && $data['profile']['getter']['id']) {
            $this->profileRepository->deleteProfile($data['profile']['getter']['id']);
            unset($data['profile']['getter']);
        }

        unset($data['isLegal']);
        unset($data['sex']);
        unset($data['birthday']);
        unset($data['payerSameGetter']);
        unset($data['profile']);

        //Обработка данных пользователя
        $data['userType'] = $data['isEmployee'] === 1
            ? $this::ADMIN_USER
            : $this::CUSTOMER_USER;
        unset($data['isEmployee']);

        $userData = $this->convertField($data, true);
    }

    public function createUser(array $data = []): bool
    {
        if (empty($data)) $data = $this->params;

        $profilesData = [];
        $userData = [];

        $this->prepareData($data, $profilesData, $userData);

        $profiles = [];

        foreach ($profilesData as $profile) {
            array_merge(
                $profiles,
                $this->profileRepository->getProfileBy($profile) ?? []
            );
        }

        $search = [];

        if (!empty($userData['email'])) $search['email'] = $userData['email'];

        if (!empty($userData['phone'])) $search['phone'] = $userData['phone'];

        $isIsset = array_merge($this->getUsersBy($search), $profiles);

        if (empty($isIsset)) {
            $this->entity->load($userData, '');

            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            try {
                if ($this->entity->validate() && $this->entity->save(false)) {
                    foreach ($profilesData as $profile) {
                        $this->profileRepository->resetUserProfileEntity();
                        $profile['user_id'] = $this->entity->id;

                        if (!$this->profileRepository->createUserProfile($profile)) {
                            throw new Exception('Не удалось создать профиль.');
                        }
                    }

                    $transaction->commit();
                    $this->setAttributes($this->entity->toArray());

                    return true;
                } else {
                    throw new Exception('Не удалось создать пользователя.');
                }
            }catch (Exception  $e) {
                $transaction->rollBack();
                var_dump($e->getMessage()); die();
                return false;
            }
        }

        return false;
    }

    public function updateUser(array $data): bool
    {
        $profilesData = [];
        $userData = [];

        $this->prepareData($data, $profilesData, $userData, true);

        if (!empty($userData['id'])) {
            $entity =  $this->entity::findOne($userData['id']);
        } else {
           if (!empty($userData['email'])) {
                $entity =  $this->entity::findOne(['email' => $userData['email']]);
            } else if (!empty($userData['phone'])) {
                $entity =  $this->entity::findOne(['phone' => $userData['phone']]);
            } else {
               return false;
           }
        }

        if (!empty($entity)) {
            $connection = $this->entity::getDb();
            $transaction = $connection->beginTransaction();

            try {
                $entity->attributes = $userData;
                $entity->date_update = date('Y-m-d H:i:s', time());

                if ($entity->validate() && $entity->save()) {
                    foreach ($profilesData as $profile) {
                        if (empty($profile['id'])) continue;

                        if (!$this->profileRepository->updateUserProfile($profile)) {
                            throw new Exception('Не удалось сохранить профиль.');
                        }
                    }

                    $userRoles = $this->roleService->getRoles($data['id']);

                    if (!empty($userRoles)) {
                        $role = array_key_first($userRoles);

                        if ($role !== $data['role']) {
                            $this->roleService->removeAll($data['id']);
                            $this->roleService->setRole($data['role'], $data['id']);
                        }
                    }


                    $transaction->commit();

                    return true;
                } else {
                    throw new Exception('Не удалось сохранить пользователя.');
                }
            }catch (Exception  $e) {
                $transaction->rollBack();

                return false;
            }
        }

        return false;
    }

    public static function findIdentity($id): ?UserRepository
    {
        $identity = new self();
        $attributes = $identity->entity::find()
            ->where(['id' => $id])
            ->asArray()
            ->one();

        if (is_null($attributes)) return null;

        $identity->setAttributes($attributes);

        return $identity;
    }

    public function setIdentity($id): void
    {
        $this->setAttributes(
            $this->entity::find()
                ->where(['id' => $id])
                ->asArray()
                ->one()
        );
    }

    public function isAdmin(): bool
    {
        return $this->user_type == self::ADMIN_USER;
    }

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getSessionKey(): string
    {
        return $this->session_key ?? '';
    }

    public function getUserType(): int
    {
        return $this->user_type;
    }

    public function getAuthKey()
    {
        return $this->session_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->session_key === $authKey;
    }

    public function createPin(?int $userId = null): array
    {
        if (!is_null($userId)) $this->params['userId'] = $userId;

        $this->pinService->setParams($this->params);

        return $this->pinService->createPin();
    }

    public function getPin(): array
    {
        $this->pinService->setParams($this->params);

        Logger::getInstance()->writeLog('VerifyPin.log', 'Params from front: ' .  json_encode($this->params), true);

        $pin = $this->pinService->getPin();

        Logger::getInstance()->writeLog('VerifyPin.log', 'Params from Database: ' .  json_encode($pin), true);

        return empty($pin) ? ['id' => 0] : $pin;
    }

    public function getLastPin(): array
    {
        $this->pinService->setParams($this->params);

        return $this->pinService->getLastPin();
    }

    public function removePin(int $id): void
    {
        $this->pinService->setParams(['id' => $id]);
        $this->pinService->removePin();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Function "findIdentityByAccessToken" has blocked');
    }

    protected function setBySessionParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (!empty($this->params['BySession']['sessionKey']) && !empty($this->params['BySession']['userType'])) {
            $query->where(['session_key' => $this->params['BySession']['sessionKey']]);
            $query->andWhere(['user_type' => $this->params['BySession']['userType']]);
        }

        return $query;
    }

    protected function setPhoneParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (!empty($this->params['phone'])) {
            $query->where(['phone' => $this->params['phone']]);
        }

        return $query;
    }

    protected function setOrIdParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (!empty($this->params['OrId'])) {
            $query->orWhere(['id' => $this->params['OrId']]);
        }

        return $query;
    }

    public function getBadge(): array
    {
        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'fio' => $this->first_name . ' ' . $this->last_name
        ];
    }

    public function checkLegalInn(string $inn, int $idUser): bool
    {
        $profiles = $this->profileRepository->getProfileBy(['legal_inn' => $inn]);

        foreach ($profiles as $id => $profile) {
            if ($profile['user_id'] == $idUser) {
                unset($profiles[$id]);
            }
        }

        return !empty($profiles);
    }

    public function getUsersForManticoreSearch(int $page): array
    {
        $query = $this->entity::find();

        $this->pagination->setTotalCount($query->count());
        $this->pagination->setCurrentPage($page);
        $this->pagination->setCountOnPage(1000);
        $query = $this->setOffsetLimit($query);
        $output['pagination'] = $this->pagination->getData();

        $users = $query
            ->asArray()
            ->all();

        foreach ($users as &$user) {
            $user = $this->convertField($user);
        }

        $output['users'] = $users;

        return $output;
    }
}