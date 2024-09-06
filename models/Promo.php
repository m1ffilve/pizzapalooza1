<?php

namespace app\models;

use yii\db\ActiveRecord;

class Promo extends ActiveRecord
{
    public static function tableName()
    {
        return 'promo';
    }

    public function rules()
    {
        return [
            [['code', 'discount'], 'required'],
            [['code'], 'string', 'max' => 255],
            [['discount'], 'number', 'min' => 0],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'Промокод',
            'discount' => 'Скидка %'

        ];
    }

    public static function findByCode($code)
    {
        return self::findOne(['code' => $code]);
    }

    public function getDiscount()
    {
        return $this->discount;
    }
}
