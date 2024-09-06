<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактировать профиль';
$this->params['breadcrumbs'][] = $this->title;
?>


<?php

$form = ActiveForm::begin(['id' => 'edit-profile-form']); ?>
<div id="notification" class="notification"></div>
<h1 class="title">Редактирование профиля</h1>
<?= $form->field($model, 'name')->textInput(['placeholder' => 'Имя'])->label(false) ?>
<?= $form->field($model, 'email')->textInput(['placeholder' => 'Почта'])->label(false) ?>
<?= $form->field($model, 'phone_number')->textInput(['placeholder' => 'Телефон', 'id' => 'user-phone'])->label(false) ?>
<?= $form->field($modelPassword, 'newPassword')->passwordInput(['placeholder' => 'Новый пароль'])->label(false) ?>
<?= $form->field($modelPassword, 'confirmPassword')->passwordInput(['placeholder' => 'Подтвердите новый пароль'])->label(false) ?>
<div class="form-group2">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'edit-profile-button']) ?>
</div>
<?
Yii::$app->session->setFlash('success', 'Профиль успешно обновлен.');
Yii::$app->session->setFlash('error', 'Ошибка при сохранении профиля.');
?>
<?php ActiveForm::end(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const profileUpdated = <?= json_encode(Yii::$app->session->get('profileUpdated', false)) ?>;
        if (profileUpdated) {
            showNotification('Профиль успешно обновлен!', true);
            <?php Yii::$app->session->remove('profileUpdated'); ?> // Очистка флага в сессии
        }
    });

    function showNotification(message, isSuccess) {
        var notification = document.getElementById("notification");
        notification.textContent = message;
        notification.className = `notification ${isSuccess ? 'success' : 'error'} show`;
        setTimeout(() => {
            notification.className = 'notification'; // Скрываем уведомление
        }, 4000); // Уведомление исчезает через 4 секунды
    }


</script>
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