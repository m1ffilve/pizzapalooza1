<!-- views/site/add-promo.php -->
<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$form = ActiveForm::begin(['id' => 'add-promo-form']);

echo $form->field($promoModel, 'code')->textInput(['maxlength' => true, 'placeholder' => 'Введите промокод']);
echo $form->field($promoModel, 'discount')->textInput(['type' => 'number', 'placeholder' => 'Введите процент скидки']);

echo Html::submitButton('Создать', ['class' => 'btn btn-primary']);

ActiveForm::end();
?>