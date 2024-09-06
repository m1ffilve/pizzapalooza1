<?php

/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Главная страница';
?>
<div class="main">
    <h1 class="main-name">Итальянская <br>пицца</h1>
</div>
<!-- Стили Swiper -->
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

<!-- Библиотека Swiper JS -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<div class="swiper-container">
    <div class="swiper-wrapper">
        <!-- Слайд 1 -->
        <div class="swiper-slide">
            <a href="pizza" class="slider-link">
                <img src="../images/slider11.png" alt="3-я пицца в подарок" class="w-100">
                <h1 class="slider-title sltt1">Бесплатная доставка в офис</h1>
                <p class="slider-text sltxt1">Закажите на общую сумму от 1000 рублей пиццу на обед для вашего офиса, и
                <br>  мы доставим всё бесплатно прямо к вашему рабочему месту!</p>
            </a>
        </div>

        <!-- Слайд 2 -->
        <div class="swiper-slide">
            <a href="pizza" class="slider-link">
                <img src="../images/slider12.png" alt="ЗИМА В ТОМАТО" class="w-100">
                <p class="slider-text sltxt2">В честь вашего дня рождения, получите скидку 30% <br>на заказ любой пиццы
                    на сайте и бесплатно десерт -<br> наш изысканный тирамису!</p>
            </a>
        </div>
        <div class="swiper-slide">
            <a href="pizza" class="slider-link">
                <img src="../images/slider13.png" alt="ЗИМА В ТОМАТО" class="w-100">
                <p class="slider-text sltxt3">Всем студентам по предъявлению <br>студенческого билета предоставляется
                    скидка <br>20% на заказ любой пиццы на сайте Pizza <br>Palooza!</p>
            </a>

        </div>
        <!-- Добавьте дополнительные слайды по аналогии -->
    </div>

    <!-- Добавьте стрелки навигации -->
    <img src="../images/strelka2.svg" alt="" class="swiper-button-next">
    <img src="../images/strelka2.svg" alt="" class="swiper-button-prev">
</div>

<script>
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 1,
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        autoplay: {
            delay: 5000, // Интервал между слайдами в миллисекундах (10 секунд)
        },
    });
</script>


<div class="onas">
    <div class="onas-name-block">
        <h1 class="onas-name">Про нас</h1>
    </div>
    <div class="onas-blocks">
        <div class="onas-block">
            <h1 class="block-name">Уникальность</h1>
            <p class="block-opis">Готовим итальянскую <br>пиццу по старым <br>авторским рецептам</p>
        </div>
        <div class="onas-block">
            <h1 class="block-name">Технологии</h1>
            <p class="block-opis">Пицца выпекается на <br>дровяной печи</p>
        </div>
        <div class="onas-block">
            <h1 class="block-name">Качество</h1>
            <p class="block-opis">Тесто из лучших сортов <br>зерна и тщательно <br>подбираемых <br>ингредиентов</p>
        </div>
    </div>
</div>
<div class="history">
    <div class="history-name-bl">
        <h1 class="history-name">ИСТОРИЯ</h1>
    </div>
    <div class="first-history">
        <p class="first-opis">Прообразом пиццы были некоторые <br>кушания, подававшиеся на ломтях хлеба в <br>домах
            древних греков и римлян. В связи со <br>ввозом помидоров в Европу в 1522 году в <br>Неаполе впервые
            появилась итальянская <br>пицца. <br><br>В XVII веке появился особый род пекарей, <br>пиццайоло (итал.
            pizzaiolo), готовивших <br>пиццу для итальянских крестьян.</p>
        <img src="../images/bd1.png" alt="" class="history-img">
    </div>
    <div class="second-history">
        <img src="../images/bd2.png" alt="" class="history-img2">
        <p class="second-opis">Она сразу понравилась всем местным <br>жителям, а также туристам и стала <br>жемчужиной
            Неаполя. Пиццерия прошла <br>долгий путь и множество смен, но до сих пор <br>она радует своих гостей по
            всему миру <br>своими старыми рецептами.</p>
    </div>
</div>
<div class="dostavka">
    <div class="map">
        <iframe class="yamap"
            src="https://yandex.ru/map-widget/v1/?um=constructor%3A95dacd222e02819c85409a89e4dd6db386f2a235a92bff74c429192b4068b065&amp;source=constructor"
            width="650" height="600" frameborder="0"></iframe>
    </div>
    <div class="dostavka-opis">
        <h1 class="dostavka-name">ДОСТАВКА</h1>
        <div class="dostavka-dost">
            <div class="first">
                <img src="../images/icon.png" class="ico" alt="">
                <p class="dostavka-dostav">При опоздании курьера — <br>скидка 100 руб на следующий заказ</p>
            </div>
            <div class="second">
                <img src="../images/icon2.png" class="ico" alt="">
                <p class="dostavka-dostav">Круглосуточная доставка</p>
            </div>
            <div class="third">
                <img src="../images/icon3.png" class="ico" alt="">
                <p class="dostavka-dostav">Бесплатная доставка по Калуге. <br>По области доставка 40р</p>
            </div>
        </div>
    </div>
</div>