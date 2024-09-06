<?php
namespace app\models;

use yii\base\Model;

class Payment extends Model
{
    public $name;
    public $phone;
    public $email;
    public $address;
    public $comment;
    public $paymentMethod;
    public $card_number;
    public $card_expiry;
    public $card_cvv;
    public $deliveryMethod;

    public function rules()
    {
        return [
            [['name', 'phone', 'email', 'address', 'paymentMethod', 'deliveryMethod'], 'required'],
            ['email', 'email'],
            ['card_number', 'safe'], // Добавьте необходимые правила для карточных данных
            ['card_expiry', 'safe'],
            ['card_cvv', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Имя',
            'phone' => 'Телефон',
            'email' => 'Email',
            'address' => 'Адрес',
            'comment' => 'Комментарий',
            'paymentMethod' => 'Метод оплаты',
            'card_number' => 'Номер карты',
            'card_expiry' => 'Срок действия карты',
            'card_cvv' => 'CVV код',
            'deliveryMethod' => 'Способ доставки',
        ];
    }
}
