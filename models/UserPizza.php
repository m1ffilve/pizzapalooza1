<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

// Импортируем модель User
use app\models\User;

class UserPizza extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_pizza';
    }

    public function rules()
    {
        return [
            [['user_id', 'pizza_id', 'quantity'], 'required'],
            [['user_id', 'pizza_id', 'quantity'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'pizza_id' => 'Pizza ID',
            'quantity' => 'Quantity',
        ];
    }
    public function getPizza()
    {
        return $this->hasOne(Pizza::className(), ['id' => 'pizza_id']);
    }
    // Определяем связь с моделью User
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
