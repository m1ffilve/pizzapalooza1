<?php
/* @var $this yii\web\View */
/* @var $order app\models\Order */

use yii\helpers\Html;

?>
<p>Здравствуйте, <?= Html::encode($order->name) ?>,</p>

<p>Спасибо за ваш заказ. Ваш номер заказа: <?= Html::encode($order->id) ?>.</p>

<p>Детали заказа:</p>

<ul>
    <li>Имя: <?= Html::encode($order->name) ?></li>
    <li>Телефон: <?= Html::encode($order->phone) ?></li>
    <li>Адрес: <?= Html::encode($order->address) ?></li>
    <li>Способ оплаты: <?= Html::encode($order->payment_method) ?></li>
    <li>Способ доставки: <?= Html::encode($order->delivery_method) ?></li>
</ul>
