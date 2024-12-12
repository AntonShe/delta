<?php

namespace common\models\points;

use yii\db\ActiveRecord;

class PointEntity extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{lpost_delivery_points}}';
    }

    public function rules(): array
    {
        return [
            [['id_point', 'id_region', 'fitting_rooms_count'], 'integer'],
            [[
                'is_courier',
                'is_cash',
                'is_card',
                'is_content_requires',
                'is_del',
            ], 'integer', 'min' => 0, 'max' => 1],
            [['latitude', 'longitude'], 'number'],
            [[
                'city_name',
                'address',
                'location_description',
                'metro',
                'photos',
                'work_hours',
                'courier_polygons',
                'phone',
            ], 'string'],
            [[
                'city_name',
                'address',
                'location_description',
                'metro',
                'photos',
                'work_hours',
                'courier_polygons',
                'phone',
            ], 'trim', 'skipOnEmpty' => true],
            ['date_created', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'inserting'],
            ['date_update', 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'updating']
        ];
    }
}