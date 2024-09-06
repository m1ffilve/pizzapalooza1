<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Review extends ActiveRecord
{
    public static function tableName()
    {
        return 'reviews'; // Название таблицы в базе данных, где хранятся отзывы
    }

    public function rules()
    {
        return [
            [['rating', 'comment'], 'required'],
            ['rating', 'integer', 'min' => 1, 'max' => 5],
            ['comment', 'string', 'max' => 255],
            [['admin_reply'], 'string'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'rating' => 'Рейтинг',
            'comment' => 'Комментарий',
            'admin_reply' => 'Ответ администратора',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPizza()
    {
        return $this->hasOne(Pizza::class, ['id' => 'pizza_id'])
            ->viaTable('ordered_items', ['order_id' => 'order_id']);
    }
    public function getOrderedItem()
    {
        return $this->hasOne(OrderedItem::class, ['id' => 'ordered_item_id']);
    }
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }


}
