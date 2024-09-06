<?php

use yii\helpers\Html;

$this->title = $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Товары', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="product-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= Html::img(Yii::getAlias('@web') . $product->image_url, ['alt' => 'Изображение товара']) ?>
    <p><strong>Описание товара:</strong> <?= Html::encode($product->composition) ?></p>
    <p><strong>Цена:</strong> <?= Html::encode($product->price) ?> руб</p>
    <p><strong>История:</strong> <?= Html::encode($product->history) ?></p>
    <!-- Добавьте другие свойства товара, которые вы хотите отобразить -->
</div>