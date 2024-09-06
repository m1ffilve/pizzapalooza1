<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class OrderedItem extends ActiveRecord
{
    public static function tableName()
    {
        return 'ordered_items'; // Название таблицы в базе данных, где хранятся элементы заказа
    }

    public function rules()
    {
        return [
            [['order_id', 'pizza_id'], 'required'],
            [['order_id', 'pizza_id'], 'integer'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['pizza_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pizza::className(), 'targetAttribute' => ['pizza_id' => 'id']],
        ];
    }
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getPizza()
    {
        return $this->hasOne(Pizza::class, ['id' => 'pizza_id']);
    }

    public function getReviews()
    {
        return $this->hasMany(Review::class, ['ordered_item_id' => 'id']);
    }

}
