<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Добавить элемент меню';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$form = ActiveForm::begin(['id' => 'add-menu-form', 'options' => ['enctype' => 'multipart/form-data']]); ?>
<h1 class="title">Добавить меню</h1>
<?= $form->field($model, 'name')->textInput(['autofocus' => true, 'placeholder' => 'Название'])->label(false) ?>
<?= $form->field($model, 'category')->dropDownList(
    $categories, // Массив доступных категорий
    ['prompt' => 'Выберите категорию', 'class' => 'form-control'] // Опции для списка
)->label(false) ?>
<?= $form->field($model, 'price')->textInput(['placeholder' => 'Цена'])->label(false) ?>
<?= $form->field($model, 'composition')->textarea(['placeholder' => 'Состав'])->label(false) ?>
<div class="znaks">
    <?= $form->field($model, 'cook_time')->textInput(['placeholder' => 'Время готовки', 'class' => 'znak'])->label(false) ?>
    <?= $form->field($model, 'weight')->textInput(['placeholder' => 'Граммовка', 'class' => 'znak'])->label(false) ?>
    <?= $form->field($model, 'size')->textInput(['placeholder' => 'Размер', 'class' => 'znak'])->label(false) ?>
</div>
<?= $form->field($model, 'history')->textarea(['placeholder' => 'История'])->label(false) ?>
<?= $form->field($model, 'imageFile')->fileInput(['id' => 'menuform-imagefile', 'style' => 'display:none;','onchange' => 'showFileAttachedNotification()'])->label(false) ?>
<input name="file" type="file" id="input__file" class="input input__file" multiple>
<label for="menuform-imagefile" class="input__file-button">

    <span class="input__file-icon-wrapper"><img class="input__file-icon" src="/images/drop.svg" alt="Выбрать файл"
            width="25"></span>
    <span class="input__file-button-text">Выберите файл</span>
</label>
<div id="fileAttachedNotification" class="notification" style="display:none;">
                    Файл успешно прикреплен!
                </div>

                <script>
                    function showFileAttachedNotification() {
                        var notification = document.getElementById('fileAttachedNotification');
                        notification.style.display = 'block';

                        // Скрыть уведомление через 3 секунды
                        setTimeout(function() {
                            notification.style.display = 'none';
                        }, 3000);
                    }
                </script>
<div class="form-group">
    <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary', 'name' => 'add-menu-button']) ?>
</div>
<?php ActiveForm::end(); ?>