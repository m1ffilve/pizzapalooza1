<?php
use app\models\Pizza;
use yii\helpers\Html;
use yii\web\View;
use app\models\UserPizza;
use app\models\Order;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

$this->title = 'Оформление заказа';
?>
<div class="navbar-bottom">
    <div class="bottom-links">
        <a href="pizza" class="pizza-links-a">
            <div class="pizza-links">
                <img src="../images/picon.png" alt="" class="links-icon">
                <h1 class="links-name">Пицца</h1>
            </div>
        </a>
        <a href="zakuski" class="pizza-links-a">
            <div class="pizza-links ">
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
<script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
    type="text/javascript"></script>
<form id="orderForm" action="create-order" method="POST">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    <div class="order-inputs">
        <input class="order-input" type="hidden" name="user_id" value="<?= Yii::$app->user->id ?>">
        <input class="order-input" type="text" name="name" placeholder="Имя" required>
        <input class="order-input" type="text" name="phone" id="phone" placeholder="Телефон" required>
        <input class="order-input" type="email" name="email" placeholder="Email">
        <textarea class="order-input" name="address" placeholder="Адрес" required></textarea>
        <textarea class="order-input" name="comment" placeholder="Комментарий (необязательно)"></textarea>
        <div class="order-radios-container">
            <div class="order-radio">
                <input class="order-input-radio" type="radio" id="paymentCash" name="paymentMethod" value="cash"
                    checked>
                <label for="paymentCash">Наличными</label>
            </div>
            <div class="order-radio">
                <input class="order-input-radio" type="radio" id="paymentCard" name="paymentMethod" value="card">
                <label for="paymentCard">Картой</label>
            </div>
        </div>
        <div id="cardDetails" class="card-details" style="display: none;">
            <div class="card-image">
                <img src="/images/visa.svg" alt="" class="visa-logo">
                <input class="card-input card-number" type="text" id="cardNumberInput" name="card_number"
                    placeholder="2200 .... .... ...." required maxlength="19" inputmode="numeric">
                <input class="card-input card-expiry" type="text" id="cardExpiryInput" name="card_expiry"
                    placeholder="MM/YY" required maxlength="5">
                <input class="card-input card-cvv" type="text" id="cardCvvInput" name="card_cvv" placeholder="CVV"
                    required maxlength="3">
                <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var cardNumberInput = document.getElementById('cardNumberInput');
                        cardNumberInput.addEventListener('input', function () {
                            var value = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                            var formattedValue = '';

                            for (var i = 0; i < value.length; i++) {
                                if (i > 0 && i % 4 === 0) {
                                    formattedValue += ' ';
                                }
                                formattedValue += value.charAt(i);
                            }

                            this.value = formattedValue.trim();
                        });
                        var cardExpiryInput = new Cleave('#cardExpiryInput', {
                            date: true,
                            datePattern: ['m', 'y']

                        });
                        document.getElementById('cardExpiryInput').addEventListener('blur', function () {
                            var expiryValue = cardExpiryInput.element.value;
                            if (expiryValue) {
                                var currentDate = new Date();
                                var currentMonth = currentDate.getMonth() + 1; // Месяцы в JS начинаются с 0
                                var currentYear = currentDate.getFullYear() % 100; // Берем последние две цифры года

                                var [month, year] = expiryValue.split('/').map(Number);

                                if (year < currentYear || (year === currentYear && month < currentMonth)) {
                                    showNotification('Дата карты не может быть в прошлом');
                                    cardExpiryInput.element.value = ''; // Очистка поля даты истечения
                                }
                            }
                        });
                        var cardCvvInput = document.getElementById('cardCvvInput');

                        // Функция для маскировки ввода CVV
                        function maskCvvInput(event) {
                            var input = event.target;
                            var value = input.value;

                            // Ограничение длины ввода CVV до 3 цифр и маскировка звёздочками
                            if (value.length > 3) {
                                value = value.substring(0, 3);
                            }

                            input.value = value.replace(/\d/g, '*');
                        }

                        cardCvvInput.addEventListener('input', maskCvvInput);
                    });

                </script>
            </div>
        </div>
        <div class="order-radios-container">
            <div class="order-radio">
                <input class="order-input-radio" type="radio" id="deliveryPickup" name="deliveryMethod" value="pickup"
                    checked>
                <label for="deliveryPickup">Самовывоз</label>
            </div>
            <div class="order-radio">
                <input class="order-input-radio" type="radio" id="deliveryCourier" name="deliveryMethod"
                    value="courier">
                <label for="deliveryCourier">Курьером</label>
            </div>
        </div>
        <button type="button" id="submitOrder">Оплатить</button>
    </div>
</form>
<div id="orderErrors" style="color: red; display: none;"></div>
<script>
    document.getElementById('submitOrder').addEventListener('click', function (e) {
        e.preventDefault();

        // Получаем все поля формы
        var formData = new FormData(document.getElementById('orderForm'));
        removeShakeEffect();
        // Проверяем поля в заданном порядке
        var isValid = true;
        isValid = isValid && validateField(formData, 'name', 'Пожалуйста, введите Имя');
        isValid = isValid && validateField(formData, 'phone', 'Пожалуйста, введите Телефон');
        isValid = isValid && validateField(formData, 'email', 'Пожалуйста, введите корректный Email', isValidEmail);
        isValid = isValid && validateField(formData, 'address', 'Пожалуйста, введите Адрес');
        isValid = isValid && validatePaymentMethod(formData);

        // Если данные прошли клиентскую валидацию, отправляем на сервер
        if (isValid) {
            fetch('<?= Url::to(['site/create-order']) ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const orderId = data.orderId;
                        saveOrderStatus(orderId);
                        window.location.href = '<?= Url::to(['site/cart']) ?>';
                    } else {
                        console.error('Ошибка:', data.message);
                    }
                })
                .catch(error => console.error('Ошибка AJAX запроса:', error));
        }
    });

    // Функция для валидации поля с заданным сообщением об ошибке
    function validateField(formData, fieldName, errorMessage, validator = null) {
        var field = document.querySelector(`[name="${fieldName}"]`);
        var fieldValue = formData.get(fieldName).trim();
        if (fieldValue === '' || (validator && !validator(fieldValue))) {
            field.classList.add('shake'); // Добавляем класс тряски
            showNotification(errorMessage, false);
            return false;
        }
        return true;
    }

    // Функция для валидации метода оплаты и данных карты (если выбрана карта)
    function validatePaymentMethod(formData) {
        var paymentMethod = formData.get('paymentMethod');
        if (paymentMethod === 'card') {
            var isValid = true;
            isValid = isValid && validateField(formData, 'card_number', 'Пожалуйста, введите номер карты');
            isValid = isValid && validateField(formData, 'card_expiry', 'Пожалуйста, введите срок действия карты');
            isValid = isValid && validateField(formData, 'card_cvv', 'Пожалуйста, введите CVV-код карты');
            return isValid;
        }
        return true; // Если выбрана оплата наличными, то пропускаем валидацию карты
    }


    // Функция для проверки корректности Email
    function isValidEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    function validateCardNumber(cardNumber) {
        var re = /^\d{4} \d{4} \d{4} \d{4}$/;
        if (!re.test(cardNumber)) {
            showNotification('Пожалуйста, введите корректный номер карты', false);
            shakeField(document.getElementsByName('card_number')[0]);
            return false;
        }
        return true;
    }

    function validateCardExpiry(cardExpiry) {
        var re = /^(0[1-9]|1[0-2])\/\d{2}$/;
        if (!re.test(cardExpiry)) {
            showNotification('Пожалуйста, введите срок действия карты в формате MM/YY', false);
            shakeField(document.getElementsByName('card_expiry')[0]);
            return false;
        }
        var parts = cardExpiry.split('/');
        var month = parseInt(parts[0], 10);
        var year = parseInt('20' + parts[1], 10);
        var currentDate = new Date();
        var currentMonth = currentDate.getMonth() + 1; // Months are 0-based
        var currentYear = currentDate.getFullYear();
        if (year < currentYear || (year === currentYear && month < currentMonth)) {
            showNotification('Срок действия карты истек', false);
            shakeField(document.getElementsByName('card_expiry')[0]);
            return false;
        }
        return true;
    }

    function isValidCVV(cvv) {
        var re = /^\d{3}$/;
        return re.test(cvv);
    }

    function removeShakeEffect() {
        var shakeElements = document.querySelectorAll('.shake');
        shakeElements.forEach(function (element) {
            element.classList.remove('shake');
        });
    }
    // Эта функция вызывается при успешном создании заказа
    function onOrderCreated(orderId) {
        localStorage.setItem('orderId', orderId); // Сохраняем ID заказа
        localStorage.setItem('animationStartTime', Date.now().toString()); // Обновляем время начала анимации
        document.getElementById('orderProgress').style.display = 'block'; // Показываем прогресс-бар
        initOrderProgressAnimation(); // Инициализируем анимацию
    }

    function saveOrderStatus(orderId) {
        localStorage.setItem('orderStatus', 'success');
        localStorage.setItem('orderTimer', Date.now());
        // Убедитесь, что сервер действительно возвращает orderId, иначе здесь будет ошибка
        localStorage.setItem('orderId', orderId.toString());
    }
</script>
<script>
    $(function () {
        //2. Получить элемент, к которому необходимо добавить маску
        $("#phone").mask("+7 (999) 999-99-99");
    });

    function showNotification(message, isSuccess) {
        var notification = document.getElementById("notification");
        notification.textContent = message;
        notification.className = `notification ${isSuccess ? 'success' : 'error'} show`;
        setTimeout(() => {
            notification.className = 'notification'; // Скрываем уведомление
        }, 4000); // Уведомление исчезает через 4 секунды
    }

    document.addEventListener('DOMContentLoaded', function () {
        const paymentCashRadio = document.getElementById('paymentCash');
        const paymentCardRadio = document.getElementById('paymentCard');
        const cardDetails = document.getElementById('cardDetails');

        // Check the initial status of the payment radio buttons
        if (paymentCardRadio.checked) {
            cardDetails.style.display = 'block';
        } else {
            cardDetails.style.display = 'none';
        }

        // Add event listeners to toggle card details visibility
        paymentCashRadio.addEventListener('change', toggleCardDetailsVisibility);
        paymentCardRadio.addEventListener('change', toggleCardDetailsVisibility);

        // Function to toggle card details visibility
        function toggleCardDetailsVisibility() {
            if (paymentCardRadio.checked) {
                cardDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
            }
        }
    });
</script>
<div id="notification" class="notification"></div>