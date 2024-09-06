<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use app\models\User;
use yii\widgets\ActiveForm;
use app\models\LoginForm;
use app\models\RegisterForm;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
$cartItemCount = Yii::$app->session->get('cartItemCount', 0);
$this->registerJsFile('@web/js/bootstrap.bundle.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$logmodel = new LoginForm();
$regmodel = new RegisterForm();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">

<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="/css/site.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300&family=Roboto:wght@300;500;700&display=swap"
        rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release-pro/v4.0.0/css/solid.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
        type="text/javascript"></script>
</head>

<body>
    <div class="form-factor">
        <div class="navbar">
            <div class="navbar-left">
                <img src="../images/logo.png" alt="Logo" class="logo">
                <a href="index">Главная</a>
                <a href="pizza">Меню</a>
                <a href="stocks">Акции</a>
                <a href="cont">Контакты</a>
                <a href="review">Отзывы</a>
            </div>
            <div class="navbar-center">
                <a class="phone" href="tel:89533362404">89533362404</a>
                <span class="address">Калуга</span>
            </div>
            <div class="navbar-right">
                <a href="cart" class="cart">
                    <img src="../images/card.png" class="card">
                    <div class="cart-icon">
                        <?php if (!Yii::$app->user->isGuest): ?>
                            <?php $cartItemCount = Yii::$app->session->get('cartItemCount', 0); ?>
                            <?php if ($cartItemCount > 0): ?>
                                <span class="cart-item-count"><?= $cartItemCount ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <script>
                        $(document).ready(function () {
                            // Функция для обновления количества товаров в корзине
                            function updateCartItemCount() {
                                $.ajax({
                                    url: '/site/get-cart-item-count',
                                    method: 'GET',
                                    success: function (response) {
                                        var cartItemCount = response.cartItemCount;
                                        if (cartItemCount > 0) {
                                            $('.cart-item-count').text(cartItemCount).show(); // Показываем элемент, если есть товары
                                        } else {
                                            $('.cart-item-count').hide(); // Скрываем элемент, если корзина пуста
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Ошибка при обновлении количества товаров в корзине:', error);
                                    }
                                });
                            }

                            // Вызываем функцию обновления количества товаров в корзине после загрузки страницы
                            updateCartItemCount();

                            // Устанавливаем интервал для обновления количества товаров в корзине каждые 5 секунд
                            setInterval(updateCartItemCount, 500); // Обновление каждые 5 секунд (5000 миллисекунд)
                        });
                    </script>
                    <?php
                    if (Yii::$app->user->isGuest) {
                        // Пользователь не аутентифицирован
                        echo Html::a('Вход', '#', ['class' => 'login', 'id' => 'loginBtn']);
                        echo Html::a('Регистрация', '#', ['class' => 'login', 'id' => 'registerBtn']);
                    } else {
                        // Пользователь аутентифицирован
                        echo Html::a(Yii::$app->user->identity->name, ['site/profile'], ['class' => 'profile-link']);
                        echo Html::beginForm(['site/logout'], 'post', ['class' => 'logout-form']);  // Изменено вот здесь
                        echo Html::submitButton('<img src="../images/logout.svg" class="logout">', ['class' => 'logout-button']);
                        echo Html::endForm();
                    }

                    $form = ActiveForm::begin(['id' => 'login-form2', 'method' => 'post']);
                    ActiveForm::end();
                    ?>
            </div>
        </div>
        <?= $content ?>
        <div class="footer">
            <div class="links">
                <a href="pizza" class="footer-link">Меню</a>
                <a href="onas" class="footer-link">О нас</a>
                <a href="cont" class="footer-link">Контакты</a>
                <a href="review" class="footer-link">Отзывы</a>
                <a href="vacancy" class="footer-link">Вакансии</a>
                <a href="stocks" class="footer-link">Акции</a>
            </div>
            <div class="footer-bottom">
                <div class="left-bottom">
                    <h1 class="bottom-text-left">© 2024 PizzaPalooza</h1>
                </div>
                <div class="right-bottom">
                    <h1 class="bottom-text-right">Наши соц. сети:</h1>
                    <div class="socs">
                        <a href="https://vk.com/hime_derechan" class="soc"><img src="../images/socials.png" alt=""></a>
                        <a href="https://t.me/PizzaPalooza_bot" class="soc"><img src="../images/socials2.png"
                                alt=""></a>
                        <a href="https://wa.me/89533362404" class="soc"><img src="../images/socials3.png" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="fileAttachedNotification" class="notification" style="display: none;">
        <span id="notificationMessage"></span>
    </div>
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'action' => ['site/login'],
                'options' => ['class' => 'modal-content'],
            ]); ?>
            <h5 class="login-title" id="loginModalLabel">Вход</h5>
            <a type="button" class="close close-login" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <div class="modal-body">
                <?= $form->field($logmodel, 'phone_number')->textInput(['placeholder' => 'Телефон', 'id' => 'login-form-phone_number'])->label(false) ?>
                <?= $form->field($logmodel, 'password')->passwordInput(['placeholder' => 'Пароль', 'id' => 'login-form-password'])->label(false) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-block', 'id' => 'loginbutn', 'name' => 'login-button']) ?>
            </div>
            <p class="nothave">Нет аккаунта?
                <?= Html::a('Зарегистрироваться', ['site/register'], ['class' => 'reglink']) ?>
            </p>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <?php $form = ActiveForm::begin([
                'id' => 'register-form',
                'action' => ['site/register'], // Укажите свой URL для обработки формы здесь
                'options' => ['class' => 'modal-content'],
            ]); ?>
            <h5 class="login-title" id="registerModalLabel">Регистрация</h5>
            <a type="button" class="close close-login" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <div class="modal-body">
                <?= $form->field($regmodel, 'phone_number')->textInput(['placeholder' => 'Телефон', 'id' => 'register-form-phone_number'])->label(false) ?>
                <?= $form->field($regmodel, 'name')->textInput(['placeholder' => 'Имя'])->label(false) ?>
                <?= $form->field($regmodel, 'password')->passwordInput(['placeholder' => 'Пароль'])->label(false) ?>
                <?= $form->field($regmodel, 'password_repeat')->passwordInput(['placeholder' => 'Подтверждение пароля'])->label(false) ?>
            </div>
            <div class="modal-footer">
                <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary btn-block', 'id' => 'loginbutn', 'name' => 'register-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function showNotification(message, isSuccess = false) {
                var notification = $('#fileAttachedNotification');
                notification.removeClass('success error'); // Удаляем оба класса
                if (isSuccess) {
                    notification.addClass('success');
                } else {
                    notification.addClass('error');
                }
                $('#notificationMessage').text(message);
                notification.show();

                // Скрыть уведомление через 3 секунды
                setTimeout(function () {
                    notification.hide();
                }, 3000);
            }
            $('#registerBtn').click(function (e) {
                e.preventDefault();
                $('#registerModal').modal('show');
            });
            $('#register-form').on('beforeSubmit', function (e) {
                e.preventDefault();
                return false; // Предотвращаем отправку формы по умолчанию
            }).on('submit', function (e) {
                e.preventDefault(); // Блокируем стандартную отправку формы

                var form = $(this);
                var formData = form.serialize();

                $.ajax({
                    url: form.attr('action'),
                    type: 'post',
                    data: formData,
                    success: function (response) {
                        console.log('Ответ сервера:', response); // Добавьте эту строку для отладки

                        if (response.success) {
                            $('#registerModal').modal('hide');
                            location.reload(); // Обновление страницы после успешной регистрации
                        } else {
                            var errors = response.errors;
                            form.find('.help-block').remove();
                            form.find('.form-group').removeClass('has-error');
                            $.each(errors, function (key, val) {
                                var input = form.find('#' + form.attr('id') + '-' + key);
                                input.closest('.form-group').addClass('has-error');
                                input.after('<div class="help-block">' + val[0] + '</div>');
                                input.addClass('shake');
                                setTimeout(function () {
                                    input.removeClass('shake');
                                }, 500); // Удаление класса после анимации
                            });
                            showNotification('Пожалуйста, исправьте ошибки в форме.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Ошибка при отправке данных формы:', xhr.responseText); // Вывод ошибки в консоль
                        showNotification('Произошла ошибка при отправке данных формы.');
                    }
                });

            });
            $('#loginBtn').click(function (e) {
                e.preventDefault();
                $('#loginModal').modal('show');
            });
            $('#login-form').on('beforeSubmit', function (e) {
                e.preventDefault();
                return false; // Предотвращаем отправку формы по умолчанию
            }).on('submit', function (e) {
                e.preventDefault(); // Блокируем стандартную отправку формы

                var form = $(this);
                var formData = form.serialize();

                $.ajax({
                    url: form.attr('action'),
                    type: 'post',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            $('#loginModal').modal('hide');
                            showNotification('Вы успешно вошли в систему.', true);
                            location.reload(); // Обновление страницы после успешного входа
                        } else {
                            var errors = response.errors;
                            form.find('.help-block').remove();
                            form.find('.form-group').removeClass('has-error');
                            $.each(errors, function (key, val) {
                                var input = form.find('#' + form.attr('id') + '-' + key);
                                input.closest('.form-group').addClass('has-error');
                                input.after('<div class="help-block">' + val[0] + '</div>');
                                input.addClass('shake');
                                setTimeout(function () {
                                    input.removeClass('shake');
                                }, 500); // Удаление класса после анимации
                            });
                            showNotification('Пожалуйста, исправьте ошибки в форме.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Ошибка при отправке данных формы:', error);
                        showNotification('Произошла ошибка при отправке данных формы.');
                    }
                });
            });

            // Маска для номера телефона
            $("#login-form-phone_number, #register-form-phone_number").mask("+7 (999) 999-99-99");
        });

    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <? $this->endPage() ?>
</body>

</html>