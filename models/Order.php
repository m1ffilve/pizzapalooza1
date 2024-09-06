<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Order extends ActiveRecord
{
    public $total_amount;
    const STATUS_NEW = 'new';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PICKED_UP = 'picked_up';
    public static function tableName()
    {
        return 'orders';
    }

    public function rules()
    {
        return [
            [['name', 'phone', 'email', 'address', 'payment_method', 'delivery_method'], 'required'],
            [['name', 'phone', 'email', 'address', 'comment', 'payment_method', 'delivery_method'], 'string', 'max' => 255],
            ['email', 'email'],
            ['phone', 'match', 'pattern' => '/^\+?\d{1,4} \(\d{3}\) \d{3}-\d{2}-\d{2}$/', 'message' => 'Неверный формат телефона. Используйте +X (XXX) XXX-XX-XX.'],
            // Условная валидация для полей карты, если выбрана оплата по карте
            [
                ['card_number', 'card_expiry', 'card_cvv'],
                'required',
                'when' => function ($model) {
                    return $model->payment_method == 'card';
                },
                'whenClient' => "function (attribute, value) {
                    return $('#paymentCard').is(':checked');
                }"
            ],
            [['card_number'], 'string', 'min' => 16, 'max' => 19],
            [['card_cvv'], 'string', 'min' => 3, 'max' => 3],
            ['card_expiry', 'match', 'pattern' => '/^(0[1-9]|1[0-2])\/\d{2}$/', 'message' => 'Неверный формат даты. Используйте MM/YY.'],
            [['ordered_items'], 'string'],
            [['total_amount'], 'number'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::class, ['order_id' => 'id']);
    }
    public function getPizzas()
    {
        return $this->hasMany(Pizza::class, ['id' => 'pizza_id'])
            ->viaTable('user_pizza', ['user_id' => 'id']);
    }
    public function getOrderedItems()
    {
        return $this->hasMany(OrderedItem::class, ['order_id' => 'id']);
    }
    public function getReviews()
    {
        return $this->hasMany(Review::class, ['order_id' => 'id']);
    }

}

