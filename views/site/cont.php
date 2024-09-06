<?php

/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Контакты';
?>
<div class="cont-cont">
    <div class="cont-info">
        <h1 class="cont-name-info">Контакты</h1>
        <a class="cont-info2" href="tel:89533362404">Телефон: +79533362404
        </a>
        <br>
        <a href="mailto:arina.peredereeva@mail.ru" class="cont-info2"?subject="HTML ссылка">Почта: arina.peredereeva@mail.ru</a>
    </div>
    <div class="streets">
        <div class="street">
            <p class="street-info"> <a href="https://yandex.ru/maps/-/CDfZUJPh">Грабцевское ш., 126</a>
                <br>Пн - Вс: с 10:00 до 23:00
            </p>
        </div>
        <div class="street">
            <p class="street-info"><a href="https://yandex.ru/maps/-/CDfZUN4E">Новослабодская ул., 31</a>
                <br>Пн - Вс: с 10:00 до 23:00
            </p>
        </div>
        <div class="street">
            <p class="street-info"><a href="https://yandex.ru/maps/-/CDfZUNm-">Терепецкая ул., 10</a>
                <br>Пн - Вс: с 10:00 до 23:00
            </p>
        </div>
    </div>
    <div class="pronas-bg">
        <div class="pronas-name1">
            <h1 class="pronas-name">Про нас</h1>
        </div>
        <div class="onas-blocks">
            <div class="onas-block">
                <h1 class="block-name">Уникальность</h1>
                <p class="block-opis">Готовим итальянскую <br>пиццу по старым <br>авторским рецептам</p>
            </div>
            <div class="onas-block">
                <h1 class="block-name">Технологии</h1>
                <p class="block-opis">Пицца выпекается на <br>дровяной печи </p>
                </p>
            </div>
            <div class="onas-block">
                <h1 class="block-name">Качество</h1>
                <p class="block-opis">Тесто из лучших сортов <br>зерна и тщательно <br>подбираемых <br>ингредиентов
            </div>
        </div>
    </div>
</div>