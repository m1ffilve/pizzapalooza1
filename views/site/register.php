<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\web\JsExpression;

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-register">
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'register-form']); ?>
            <h1 class="login-title"><?= Html::encode($this->title) ?></h1>
            <div class="login-info">
                <?= $form->field($model, 'phone_number', ['options' => ['class' => 'form-group']])->textInput(['placeholder' => 'Телефон', 'id' => 'user-phone'])->label(false) ?>
                <?= $form->field($model, 'name', ['options' => ['class' => 'form-group']])->textInput(['placeholder' => 'Имя'])->label(false) ?>
                <?= $form->field($model, 'password', ['options' => ['class' => 'form-group']])->passwordInput(['placeholder' => 'Пароль'])->label(false) ?>
                <?= $form->field($model, 'password_repeat', ['options' => ['class' => 'form-group']])->passwordInput(['placeholder' => 'Подтверждение пароля'])->label(false) ?>
            </div>

            <div class="form-group2">
                <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
            </div>
            <p class="nothave">Уже есть аккаунт? <?= Html::a('Войти', ['login'], ['class' => 'reglink']) ?></p>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    //Код jQuery, установливающий маску для ввода телефона элементу input
    //1. После загрузки страницы,  когда все элементы будут доступны выполнить...
    $(function () {
        //2. Получить элемент, к которому необходимо добавить маску
        $("#user-phone").mask("+7 (999) 999-99-99");
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
    type="text/javascript"></script>