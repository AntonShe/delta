<?php
namespace console\controllers;

use common\models\AbstractMailSender;
use common\models\bitrix\BitrixRepository;
use common\models\feed\FeedBuilder;
use common\models\feed\FeedCreator;
use common\models\logger\Logger;
use common\models\obmb\OBMBRepository;
use common\models\rsb_transaction\RsbTransactionService;
use common\models\points\PointService;
use common\models\search\index\ProductIndex;
use common\models\search\index\UserIndex;
use common\models\search\ManticoreSearchService;
use console\models\ImageTransporter;
use console\models\OrderSender;
use console\models\Sitemap;
use yii\console\Controller;

class CronController extends Controller{

    protected OBMBRepository $obmbRepository;
    protected BitrixRepository $bitrixRepository;
    protected OrderSender $orderSender;
    protected RsbTransactionService $rsbTransactionService;
    protected ManticoreSearchService $searchService;
    protected ImageTransporter $imageTransporter;
    protected array $errors = [];
    protected AbstractMailSender $mailSender;

    protected string $title = '';
    protected string $errorTitle = '';

    public function __construct($id, $module, $config = [])
    {
        $this->mailSender = new AbstractMailSender();
        $this->obmbRepository = new OBMBRepository();
        $this->bitrixRepository = new BitrixRepository();
        $this->orderSender = new OrderSender();
        $this->rsbTransactionService = new RsbTransactionService();
        $this->imageTransporter = new ImageTransporter();
        $this->searchService = new ManticoreSearchService();

        parent::__construct($id, $module, $config);
    }

    public function actionUpdateProducts(): void
    {
        $this->title = 'Обновление данных: товаров, серий, издательств и авторов';
        $data = $this->obmbRepository->getData();

        $this->createOrUpdateEntity(
            'common\models\product\ProductRepository',
            $data['products']
        );

        $this->createOrUpdateEntity(
            'common\models\publishing_house\PublishingHouseRepository',
            $data['pubhouses']
        );

        $this->createOrUpdateEntity(
            'common\models\series\SeriesRepository',
            $data['series']
        );

        $this->createOrUpdateEntity(
            'common\models\person\PersonRepository',
            $data['persons']
        );
    }

    public function actionUpdatePricesAndCounts(): void
    {
        $this->title = 'Цены/остатки';

        $this->searchService->setClient('client', new ProductIndex());
        $priceAndQuantityData = $this->obmbRepository->getPricesAndQuantityFormated();
        $this->searchService->updatePricesAndQuantity($priceAndQuantityData);

        $this->createOrUpdateEntity(
            'common\models\product\ProductRepository',
            $priceAndQuantityData,
            false
        );
    }

    private function createOrUpdateEntity($repository, $entities, $needCreate = true, $needUpdate = true): void
    {
        $this->errorTitle = "Ошибки при импорте ({$this->title})";

        try {
            if (empty($entities)) {
                $this->mailSender->sendErrorsMail($this->errorTitle, 'Количество собранных данных - 0');
                print_r('Количество собранных данных - 0');
                die();
            }

            print_r('createOrUpdateEntity start' . PHP_EOL);
            print_r('count entities - ' . count($entities) . PHP_EOL);
            $chunks = array_chunk($entities, 1000);
            print_r('count chunks - ' . count($chunks) . PHP_EOL);
            print_r('start foreach' . PHP_EOL);
            foreach ($chunks as $chunk) {
                foreach ($chunk as $entity) {
                    print_r('-----------------------------------------------------------------------' . PHP_EOL);
                    print_r('entity data - ' . PHP_EOL);
                    print_r($entity);
                    print_r(PHP_EOL);

                    $paramName = isset($entity['id']) ? 'id' : 'labirintId';
                    $paramValue = $entity['id'] ?? $entity['labirintId'];

                    $repository = new $repository();
                    $repository->setParams([$paramName => $paramValue]);

                    $id = $repository->checkIsExist();
                    print_r('checkIsExist result - ' . $id . PHP_EOL);

                    $repository->setParams($entity);

                    if ($id && $needUpdate) {
                        $result = $repository->update($id);
                        print_r('update result - ' . PHP_EOL);
                        print_r($result);
                        print_r(PHP_EOL);
                    } elseif (!$id && $needCreate) {
                        $result = $repository->create();
                        print_r('create result - ' . PHP_EOL);
                        print_r($result);
                        print_r(PHP_EOL);
                    }

                    if (isset($result['errors'])) {
                        print_r('errors - ' . PHP_EOL);
                        print_r($result['errors']);
                        print_r(PHP_EOL);
                        $result['entity'] = $entity;
                        $this->addErrors($result);
                    }
                    print_r('-----------------------------------------------------------------------' . PHP_EOL);
                }
                sleep(10);
            }
            print_r('end foreach' . PHP_EOL);
        } catch (\Exception $e) {
            $this->addErrors(['exception' => $e->getMessage()]);
            print_r('exception - ' . $e->getMessage() . PHP_EOL);
        }

        print_r('createOrUpdateEntity end' . PHP_EOL);

        if (!empty($this->errors)) {
            $this->mailSender->sendErrorsMail($this->errorTitle, print_r($this->errors, true));
            print_r($this->errors);
            print_r('Завершено с ошибками');
            die();
        }

        print_r('Успешно ' . get_class($repository) . PHP_EOL);
    }

    private function addErrors(array $errors): void
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    public function actionSendOrder(): void
    {
        Logger::getInstance()->writeLog('actionSendOrder.log', 'Start sending.');
        $this->orderSender->sendNewOrders();
        Logger::getInstance()->writeLog('actionSendOrder.log', 'End sending');
    }

    public function actionUpdateOrder(): void
    {
        Logger::getInstance()->writeLog('actionUpdateOrder.log', 'Start updating');
        $this->orderSender->updateOrder();
        Logger::getInstance()->writeLog('actionUpdateOrder.log', 'End updating');
    }

    public function actionSitemap(): void
    {
        try {
            (new Sitemap())->create();
        } catch (\Exception $e) {
            Logger::getInstance()->writeLog("createSitemap.log", $e->getMessage());
        }
    }

    public function actionUpdatePoints(): void
    {
        $pointService  = new PointService();

        try {
            $pointService->updatePointsFromApi();
        } catch (\Exception $e) {
            $this->addErrors(['exception' => $e->getMessage()]);
            print_r('exception - ' . $e->getMessage() . PHP_EOL);
        }
    }

    public function actionCreateFeeds()
    {
        foreach (['others', 'edu', 'read'] as $genre) {
            try {
                $builder = new FeedBuilder();
                (new FeedCreator($builder))->create($genre);
            } catch (\Exception $e) {
                print_r('exception - ' . $e->getMessage() . PHP_EOL);
            }
        }
    }

    public function actionUpdateProductsManticore()
    {
        $this->searchService->setClient('client', new ProductIndex());
        $this->searchService->addOrUpdateProductForIndex();
    }

    public function actionCloseBusinessDay()
    {
        $this->errorTitle = "Закрытие бизнес-дня";
        try {
            $this->rsbTransactionService->closeBusinessDay();
        } catch (\Exception $e) {
            $this->mailSender->sendErrorsMail($this->errorTitle, $e->getMessage());
        }
    }

    public function actionUpdateStatusPayment()
    {
        $this->errorTitle = "Обновление статусов оплаты";
        try {
            $this->rsbTransactionService->updateStatusTransactions();
        } catch (\Exception $e) {
            $this->mailSender->sendErrorsMail($this->errorTitle, $e->getMessage());
        }
    }

    public function actionUpdateBalance()
    {
        $this->errorTitle = "Обновление баланса и оформление возврата";
        try {
            $this->rsbTransactionService->updateBalanceAfterRefund();
        } catch (\Exception $e) {
            $this->mailSender->sendErrorsMail($this->errorTitle, $e->getMessage());
        }
    }

    public function actionRejectOrders()
    {
        $this->errorTitle = "Отмена заказа магазином";
        try {
            $this->rsbTransactionService->rejectOrderByShop();
        } catch (\Exception $e) {
            $this->mailSender->sendErrorsMail($this->errorTitle, $e->getMessage());
        }
    }

    public function actionCreateIndex()
    {
        $this->searchService->setClient('client', new ProductIndex());
        $this->searchService->createIndex();
    }

    public function actionMovePhoto()
    {
        $this->imageTransporter->movePhotoToCloud();
    }

    public function actionAddPhoto()
    {
        $this->imageTransporter->addAdditionalPhoto();
    }

    public function actionCreateIndexUsers()
    {
        $this->searchService->setClient('client', new UserIndex());
        $this->searchService->createIndex();
    }

    public function actionUpdateUsersManticore()
    {
        $this->searchService->setClient('client', new UserIndex());
        $this->searchService->addOrUpdateUserForIndex();
    }
}