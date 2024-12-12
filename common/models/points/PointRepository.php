<?php

namespace common\models\points;

use common\models\AbstractMailSender;
use common\models\AbstractRepository;
use common\models\logger\Logger;
use yii\db\ActiveQuery;

class PointRepository extends AbstractRepository
{
    protected array $fieldsMap = [
        'id_point' => 'idPoint',
        'city_name' => 'cityName',
        'id_region' => 'idRegion',
        'is_courier' => 'isCourier',
        'is_cash' => 'isCash',
        'is_card' => 'isCard',
        'location_description' => 'locationDescription',
        'fitting_rooms_count' => 'fittingRoomsCount',
        'is_content_requires' => 'isContentRequires',
        'work_hours' => 'workHours',
        'courier_polygons' => 'courierPolygons',
        'is_del' => 'isDel',
    ];

    public function __construct()
    {
        $this->entity = new PointEntity();
        parent::__construct();
    }

    protected function getSettersParamsInQuery(): array
    {
        return array_merge(parent::getSettersParamsInQuery(),
            [
                'PointId',
            ]
        );
    }

    public function deactivateAllPoints(): void
    {
        $table = $this->entity::tableName();
        $connection = $this->entity::getDb();

        $connection->createCommand("update {$table} set  is_del = 1")->execute();
    }

    public function getPoints() {
        $query = $this->setParamsInQuery($this->entity::find());
        $points = $query->asArray()->all();
        $result = [];

        foreach ($points as $point) {
            $result[] = $this->convertField($point);
        }

        return $result;
    }

    public function createItem(): bool
    {
        $query = $this->setParamsInQuery($this->entity::find());

        $point = $query
            ->asArray()
            ->one();

        $connection = $this->entity::getDb();
        $transaction = $connection->beginTransaction();

        try {
            if (empty($point)) {
                $this->entity = new PointEntity();
            } else {
                $this->entity = $this->entity::findOne(['id_point' => $this->params['idPoint']]);
            }

            $this->entity->load($this->convertField($this->params, true), '');

            if ($this->entity->validate() && $this->entity->save()) {
                $transaction->commit();

                return true;
            } else {
                throw new \Exception(implode("\n", $this->entity->getErrors()));
            }
        } catch (\Exception $e) {
            Logger::getInstance()->writeLog(
                'UpdatePoints.log',
                'Ошибка при обновлении или добавлении в БД: ' . $e->getMessage(),
                isNeedSend: true
            );

            return false;
        }
    }

    public function getAllCities(): array
    {
        $rawCityList = $this->entity::find()
            ->select('city_name')
            ->distinct()
            ->indexBy('city_name')
            ->asArray()
            ->all();

        return array_keys($rawCityList);
    }

    public function getAllPoints(): array
    {
        $points = $this->entity::find()
            ->andWhere(['is_del' => 0])
            ->andWhere(['is_courier' => 0])
            ->asArray()
            ->all();

        foreach ($points as &$point) {
            if ($point['work_hours']) {
                $workHours = json_decode($point['work_hours']);
                foreach ($workHours as $dayWeek) {
                    if (property_exists($dayWeek, 'day')) {
                        $startDay = date(" H:i", strtotime($dayWeek->from));
                        $endDay = date(" H:i", strtotime($dayWeek->to));
                        switch ($dayWeek->day) {
                            case 0:
                                $point['schedule'][0] = "Понедельник: $startDay - $endDay";
                                break;
                            case 1:
                                $point['schedule'][1] = "Вторник: $startDay - $endDay";
                                break;
                            case 2:
                                $point['schedule'][2] = "Среда: $startDay - $endDay";
                                break;
                            case 3:
                                $point['schedule'][3] = "Четверг: $startDay - $endDay";
                                break;
                            case 4:
                                $point['schedule'][4] = "Пятница: $startDay - $endDay";
                                break;
                            case 5:
                                $point['schedule'][5] = "Суббота: $startDay - $endDay";
                                break;
                            case 6:
                                $point['schedule'][6] = "Воскресенье: $startDay - $endDay";
                                break;
                        }
                    }
                }
            }
        }

        return $points;
    }

    protected function setPointIdParamInQuery(ActiveQuery $query): ActiveQuery
    {
        if (isset($this->params['idPoint'])) {
            $query->where(['id_point' => $this->params['idPoint']]);
        }

        return $query;
    }

    public function getCourierByCity(string $cityName): array
    {
        $cityName = \trim(\str_replace('г.', '', $cityName));

        return $this->entity::find()
            ->where(['city_name' => $cityName])
            ->andWhere(['is_courier' => 1])
            ->andWhere("courier_polygons is not null")
            ->asArray()
            ->one() ?? [];
    }
}