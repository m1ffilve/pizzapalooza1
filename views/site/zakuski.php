<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'Меню закусок';
$this->params['breadcrumbs'][] = $this->title;
use app\models\RatingForm;

?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Подключаем скрипт SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="navbar-bottom">
    <div class="bottom-links">
        <a href="pizza" class="pizza-links-a">
            <div class="pizza-links">
                <img src="../images/picon.png" alt="" class="links-icon">
                <h1 class="links-name">Пицца</h1>
            </div>
        </a>
        <a href="zakuski" class="pizza-links-a">
            <div class="pizza-links active">
                <img src="../images/dicon2.png" alt="" class="links-icon">
                <h1 class="links-name">Закуски <br>и салаты</h1>
            </div>
        </a>
        <a href="deserti" class="pizza-links-a">
            <div class="pizza-links ">
                <img src="../images/picon3.png" alt="" class="links-icon">
                <h1 class="links-name">Десерты</h1>
            </div>
        </a>
        <a href="napitki" class="pizza-links-a">
            <div class="pizza-links ">
                <img src="../images/picon4.png" alt="" class="links-icon">
                <h1 class="links-name">Напитки</h1>
            </div>
        </a>
        <a href="sousi" class="pizza-links-a">
            <div class="pizza-links">
                <img src="../images/picon5.png" alt="" class="links-icon">
                <h1 class="links-name">Соусы</h1>
            </div>
        </a>
    </div>
</div>
<div class="main-pizza">
    <h1 class="tittle"><?= Html::encode($this->title) ?></h1>
    <ul class="pizza-list">
        <?php foreach ($newPizzas as $pizza): ?>
            <li class="pizza-container">
                <div class="pizza">
                    <?= Html::a(
                        Html::img(Html::encode($pizza->image_url), ['class' => 'pizza-img']),
                        ['site/view-product', 'id' => $pizza->id]
                    ) ?>
                        <p class="cook_time">
                            <img src="/images/time.png" alt="Время готовки" class="timebubu">
                            <span><?= Html::encode($pizza->cook_time) ?>'</span>
                        </p>
                    <div class="pizza-text ptt">
                        
                        <h1 class="pizza-name"><?= Html::encode($pizza->name) ?></h1>
                        <div class="sizes">
                            <span class="weightp"><?= Html::encode($pizza->weight) ?> г</span>
                        </div>
                        <p class="pizza-opis"><?= Html::encode($pizza->composition) ?></p>
                    </div>
                    <div class="stars" data-id="<?= $pizza->id ?>">
                        <!-- Здесь должен быть ваш код для отображения звездного рейтинга -->
                        <?php
                        // Максимальный возможный рейтинг
                        $maxRating = 5;
                        // Текущий рейтинг пиццы
                        $currentRating = $averageRatings[$pizza->id];
                        // Цикл для отображения звезд
                        for ($i = 1; $i <= $maxRating; $i++) {
                            // Определяем путь к изображениям звезд
                            $starImage = $i <= $currentRating ? '../images/star3.svg' : '../images/star1.svg';
                            // Выводим изображение звезды
                            echo '<img src="' . $starImage . '" class="star" data-value="' . $i . '" />';
                        }
                        ?>
                        <span class="rating"><?= $averageRatings[$pizza->id] ?></span>
                    </div>
                    <script>
                        $(document).ready(function () {
                            // Обработчик клика по звезде для установки рейтинга
                            $('.star').click(function () {
                                var rating = $(this).data('value'); // Получаем значение рейтинга
                                var pizzaId = $(this).parent().data('id'); // Получаем ID пиццы

                                $.ajax({
                                    url: '/save-rating', // URL-адрес экшена для сохранения рейтинга
                                    type: 'POST',
                                    data: { pizzaId: pizzaId, rating: rating }, // Передаем ID пиццы и рейтинг на сервер
                                    success: function (response) {
                                        if (response.success) {
                                            console.log('Рейтинг сохранен.');
                                            // Обновляем отображение рейтинга на странице без перезагрузки
                                            updateStars(pizzaId, rating); // Добавлены аргументы pizzaId и rating
                                        } else {
                                            console.error('Не удалось сохранить рейтинг.');
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Произошла ошибка:', error);
                                    }
                                });
                            });

                            // Функция для обновления звезд в зависимости от рейтинга
                            function updateStars(pizzaId, rating) {
                                var starImages = $('.star[data-id="' + pizzaId + '"]');

                                // Перебираем каждое изображение звезды для этой пиццы
                                starImages.each(function (index) {
                                    var value = $(this).data('value');

                                    // Если значение звезды меньше или равно рейтингу, показываем полную звезду, иначе - пустую
                                    if (value <= rating) {
                                        $(this).attr('src', '../images/star3.svg'); // Полная звезда
                                    } else {
                                        $(this).attr('src', '../images/star1.svg'); // Пустая звезда
                                    }
                                });
                            }
                        });


                    </script>
                    <div class="bought">
                        <div class="pizza-price">
                            <h1 class="pizza-text-price"><?= Html::encode($pizza->price) ?> руб</h1>
                        </div>
                        <div class="pizza-buy-btn">
                            <button class="pizza-buy add-to-cart" data-id="<?= $pizza->id ?>"
                                data-name="<?= Html::encode($pizza->name) ?>" data-price="<?= $pizza->price ?>">В
                                корзину</button>

                            <script>
                                $(document).ready(function () {
                                    var isUserLoggedIn = <?= Yii::$app->user->isGuest ? 'false' : 'true' ?>;

                                    // Сначала удалим предыдущие обработчики, если они есть, чтобы предотвратить дублирование
                                    $('.add-to-cart').off('click').on('click', function () {
                                        var button = $(this);

                                        if (!isUserLoggedIn) {
                                            Swal.fire({
                                                title: 'Внимание',
                                                text: 'Пожалуйста, зарегистрируйтесь или войдите в аккаунт, чтобы добавить товар в корзину.',
                                                icon: 'warning',
                                                confirmButtonText: 'ОК'
                                            });
                                            return false;
                                        }

                                        var pizzaId = button.data('id');
                                        var pizzaName = button.data('name');
                                        var pizzaPrice = button.data('price');

                                        $.ajax({
                                            url: '/site/add-to-cart',
                                            type: 'POST',
                                            data: {
                                                id: pizzaId,
                                                name: pizzaName,
                                                price: pizzaPrice
                                            },
                                            success: function (response) {
                                                if (response.success) {
                                                    button.html('<img src="../images/ico1.png" class="ico-add"> Добавлено').addClass('added');

                                                    // Обновление счетчика товаров в корзине
                                                    if ($('.cart-item-count').length === 0) {
                                                        $('.cart-icon').append('<span class="cart-item-count">' + response.cartItemCount + '</span>');
                                                    } else {
                                                        $('.cart-item-count').text(response.cartItemCount);
                                                    }

                                                    setTimeout(function () {
                                                        button.html('В корзину').removeClass('added');
                                                    }, 1000);
                                                } else {
                                                    Swal.fire({
                                                        title: 'Ошибка',
                                                        text: response.message,
                                                        icon: 'error',
                                                        confirmButtonText: 'ОК'
                                                    });
                                                }
                                            },
                                            error: function (xhr) {
                                                Swal.fire({
                                                    title: 'Ошибка',
                                                    text: 'Произошла ошибка при отправке запроса: ' + xhr.responseText,
                                                    icon: 'error',
                                                    confirmButtonText: 'ОК'
                                                });
                                            }
                                        });

                                        return false;
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<script>
    $('.add-to-cart').on('click', function (e) {
        e.preventDefault();

        // Получаем значения атрибутов data
        var pizzaId = $(this).data('id');
        var pizzaName = $(this).data('name');
        var pizzaPrice = $(this).data('price');

        // Выводим данные в консоль для отладки
        console.log('pizzaId:', pizzaId);
        console.log('pizzaName:', pizzaName);
        console.log('pizzaPrice:', pizzaPrice);

        // Получаем CSRF токен
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        // Отправляем данные на сервер
        $.ajax({
            url: '/site/add-to-cart',
            type: 'post',
            data: {
                _csrf: csrfToken,
                pizzaId: pizzaId,
                pizzaName: pizzaName,
                pizzaPrice: pizzaPrice
            },
            success: function (data) {
                console.log('Success:', data);
                console.log('Добавлено в корзину!');
            },
            error: function (xhr, status, error) {
                console.error('Error adding to cart');
                console.log('XHR status:', status);
                console.log('XHR response text:', xhr.responseText);
                console.log('Error details:', error);
            }
        });
    });
</script>