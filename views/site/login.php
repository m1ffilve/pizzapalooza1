<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form', 'method' => 'post']); ?>
            <h1 class="login-title"><?= Html::encode($this->title) ?></h1>
            <div class="login-info">
                <?= $form->field($model, 'phone_number')->textInput(['placeholder' => 'Телефон', 'id' => 'user-phone'])->label(false) ?>
                <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Пароль'])->label(false) ?>
            </div>
            <div class="form-group2">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <p class="nothave">Нет аккаунта?
                <?= Html::a('Зарегистрироваться', ['site/register'], ['class' => 'reglink']) ?></p>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    $(function () {
        //2. Получить элемент, к которому необходимо добавить маску
        $("#user-phone").mask("+7 (999) 999-99-99");
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
    type="text/javascript"></script>
</script>