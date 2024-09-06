<?php
use yii\helpers\Url;
use app\models\Pizza;
use yii\helpers\Html;
use yii\web\View;
use app\models\UserPizza;
use app\models\Order;
use yii\bootstrap5\ActiveForm;

$this->title = 'Корзина';

// В начале файла cart.php
$this->registerJs(
    "checkOrderStatus();",
    yii\web\View::POS_READY,
    'check-order-status'
);

// Регистрируем переменную о наличии промокода в сессии
$this->registerJsVar('isPromoApplied', $isPromoApplied ? 'true' : 'false', View::POS_HEAD);
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
<div class="site-cart">
    <div id="notification" class="notification"></div>

    <h1 class="tittle"><?= Html::encode($this->title) ?></h1>
    <div class="cart-container">
        <?php if (empty($cart)): ?>
            <!-- Блок, который будет показан, если корзина пуста -->
            <div id="emptyCartMessage" class="empty-cart">
                <p>Корзина пуста</p>
            </div>
        <?php endif; ?>

        <!-- Таблица корзины -->
        <table class="cart-table" <?= empty($cart) ? 'style="display: none;"' : '' ?>>
            <thead>
                <tr class="cart-column">
                    <th class="cart-row"></th>
                    <th class="cart-row">Название</th>
                    <th class="cart-row">Состав</th>
                    <th class="cart-row">Количество</th>
                    <th class="cart-row price-column">Цена</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $item): ?>
                    <?php $pizza = Pizza::findOne($item['pizza_id']); ?>
                    <tr id="cart-item-<?= $item['pizza_id'] ?>">
                        <td class="cart-img">
                            <?= Html::img($pizza->image_url, ['class' => 'pizza-img', 'alt' => 'Pizza Image']) ?>
                        </td>
                        <td class="cart-name"><?= Html::encode($pizza->name) ?></td>
                        <td class="cart-sostav"><?= Html::encode($pizza->composition) ?></td>
                        <td class="cart-kolvo">
                            <button class="quantity-decrease" id="decrease-<?= $item['pizza_id'] ?>">-</button>
                            <input type="number" class="quantity-input" id="quantity-<?= $item['pizza_id'] ?>"
                                value="<?= Html::encode($item['quantity']) ?>" min="1" readonly>
                            <button class="quantity-increase" id="increase-<?= $item['pizza_id'] ?>">+</button>
                        </td>
                        <td class="cart-price" id="cart-price-<?= $item['pizza_id'] ?>">
                            <?= Html::encode($pizza->price * $item['quantity']) ?> руб
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <script>
            $(document).ready(function () {
                $('.quantity-decrease, .quantity-increase').off('click');

                // Назначаем обработчики событий для кнопок
                $('.quantity-decrease, .quantity-increase').on('click', function (event) {
                    event.stopPropagation();

                    var pizzaId = this.id.split('-')[1];
                    var inputField = $('#quantity-' + pizzaId);
                    var currentQuantity = parseInt(inputField.val());
                    var action = $(this).hasClass('quantity-increase') ? 'increase' : 'decrease';

                    // Проверяем, если текущее количество 1 и нажата кнопка уменьшения, то удаляем товар из корзины
                    if (currentQuantity === 1 && action === 'decrease') {
                        removeItemFromCart(pizzaId);
                        return;
                    }

                    // Блокируем кнопку
                    $(this).addClass('updating').prop('disabled', true);

                    $.ajax({
                        url: '/site/update-quantity',
                        type: 'POST',
                        data: {
                            pizzaId: pizzaId,
                            action: action
                        },
                        success: function (response) {
                            if (response.success) {
                                $('#current-total-cost').text(response.totalCost + ' руб');
                                $('#discounted-total-cost').text(response.discountedCost + ' руб');
                                $('#quantity-' + pizzaId).val(response.quantity);
                                $('#cart-price-' + pizzaId).text(response.itemTotalPrice + ' руб');

                                updateOriginalTotalCost();
                            } else {
                                alert('Ошибка при обновлении количества товара.');
                            }
                        },
                        error: function () {
                            alert('Ошибка при отправке запроса.');
                        },
                        complete: function () {
                            $('.updating').removeClass('updating').prop('disabled', false);
                        }
                    });
                });
            });

            // Функция для удаления товара из корзины
            function removeItemFromCart(pizzaId) {
                $.ajax({
                    url: '/site/remove-item-from-cart',
                    type: 'POST',
                    data: { pizzaId: pizzaId },
                    success: function (response) {
                        if (response.success) {
                            $('#current-total-cost').text(response.totalCost.toFixed(0) + ' руб');
                            $('#discounted-total-cost').text(response.discountedCost.toFixed(0) + ' руб');

                            // Удаляем строку товара из таблицы корзины
                            $('#cart-item-' + pizzaId).remove();

                            updateOriginalTotalCost();

                            // Проверяем, если корзина пуста, скрываем таблицу
                            if ($('.cart-table tbody tr').length === 0) {
                                $('.cart-table').hide();
                            }
                        } else {
                            console.error('Ошибка при удалении товара из корзины.');
                        }
                    },
                    error: function () {
                        console.error('Ошибка AJAX при удалении товара из корзины.');
                    }
                });
            }
            // Функция для сброса промокода
            function resetPromoCode() {
                $('#promoCodeInput').val(''); // Очищаем поле ввода промокода
                $('#original-total-cost').hide(); // Скрываем сумму без скидки
                $('#discounted-total-cost').text($('#original-total-cost').text()); // Сбрасываем сумму со скидкой
            }
        </script>

    </div>
    <div class="bottom-cart" id="bottom-cart" style="margin-top: 20px;">
        <div class="total-promo">
            <div class="total-cost">

                <div id="total-cost">
                    <strong>Итого: </strong>
                    <span id="original-total-cost"
                        style="<?= $isPromoApplied ? 'text-decoration: line-through;' : 'display: none;' ?>"><?= $totalCost ?>
                        руб</span>
                    <!-- Показываем сумму со скидкой, если промокод применён, иначе - изначальную сумму -->
                    <span id="discounted-total-cost"><?= $isPromoApplied ? $discountedCost : $totalCost ?> руб</span>
                </div>

                <div class="promo-btns">
                    <form id="promoCodeForm" style="width: 100%;">
                        <input type="text" id="promoCodeInput" name="promoCode" placeholder="Введите промокод"
                            style="flex-grow: 1;">
                        <button type="button" id="applyPromoBtn">Применить</button>
                    </form>

                </div>
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script>
                    $('#applyPromoBtn').click(function () {
                        var promoCode = $('#promoCodeInput').val().trim();
                        if (promoCode === '') {
                            showError('Введите промокод.');
                            return;
                        }

                        $.ajax({
                            url: '/site/apply-promo-code',
                            type: 'POST',
                            data: { promoCode: promoCode },
                            success: function (response) {
                                if (response.success) {
                                    $('#original-total-cost').text(response.totalCost.toFixed(0) + ' руб').css('text-decoration', 'line-through').show();
                                    $('#discounted-total-cost').text(response.discountedCost.toFixed(0) + ' руб').show();
                                    updateOriginalTotalCost();
                                    createConfetti();// Добавляем вызов функции обновления суммы без скидки
                                } else {
                                    showError(response.message);
                                }
                            },
                            error: function () {
                                showError('Ошибка при отправке запроса.');
                            }
                        });
                    });
                    function createConfetti() {
                        var container = document.body; // Используем весь документ в качестве контейнера

                        // Создаем несколько элементов "конфетти"
                        for (var i = 0; i < 30; i++) {
                            var confetti = document.createElement("div");
                            confetti.classList.add("confetti");
                            confetti.style.left = Math.random() * 100 + "vw"; // Случайное положение по горизонтали
                            confetti.style.top = Math.random() * 100 + "vh"; // Случайное положение по вертикали
                            confetti.style.width = Math.random() * 10 + 5 + "px"; // Случайная ширина конфетти
                            confetti.style.height = confetti.style.width; // Высота равна ширине
                            confetti.style.backgroundColor = randomColor(); // Случайный цвет конфетти
                            confetti.style.borderRadius = Math.random() < 0.5 ? '50%' : '0'; // Случайная форма конфетти
                            confetti.style.animationDuration = Math.random() * 3 + 2 + "s"; // Случайная длительность анимации
                            container.appendChild(confetti);

                            // Удаляем элемент "конфетти" после завершения анимации
                            (function (confetti) {
                                setTimeout(function () {
                                    container.removeChild(confetti);
                                }, (Math.random() * 1 + 2) * 1000); // Удаляем через случайное время от 2 до 5 секунд
                            })(confetti);
                        }
                    }

                    function randomColor() {
                        var colors = ['#f0f', '#0ff', '#ff0', '#0f0', '#f00', '#00f'];
                        return colors[Math.floor(Math.random() * colors.length)];
                    }

                    function updateOriginalTotalCost() {
                        $.ajax({
                            url: '/site/get-original-total-cost',
                            type: 'GET',
                            success: function (response) {
                                if (response.success) {
                                    $('#original-total-cost').text(response.originalTotalCost.toFixed(0) + ' руб');
                                } else {
                                    showError('Ошибка при получении суммы без скидки.');
                                }
                            },
                            error: function () {
                                showError('Ошибка при отправке запроса.');
                            }
                        });
                    }

                    function showError(message) {
                        var promoInput = $('#promoCodeInput');
                        promoInput.removeClass('shake error').addClass('shake error');
                        $('#promoError').text('Ошибка: ' + message);
                        setTimeout(function () {
                            promoInput.removeClass('shake error');
                            $('#promoError').text('');
                        }, 500);
                    }
                </script>

            </div>
            <?php foreach ($orders as $order): ?>
                <?php if ($userId === $order->user_id): ?>
                    <?php if ($order->status !== Order::STATUS_PICKED_UP): ?>
                        <div id="orderProgress" style="display: none;">
                            <div class="progress-icons">
                                <div class="progress-icon" data-stage="new">
                                    <img src="../images/status.svg" alt="" class="status-icon">
                                    <div class="progress-icon-text">Заказ оформлен</div>
                                </div>
                                <div class="progress-icon" data-stage="processing">
                                    <img src="../images/cook.svg" alt="" class="status-icon cook">
                                    <div class="progress-icon-text">Заказ готовится</div>
                                </div>
                                <div class="progress-icon" data-stage="completed">
                                    <img src="../images/bag.svg" alt="" class="status-icon cook">
                                    <div class="progress-icon-text">Заказ выполнен</div>
                                </div>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill"></div>
                            </div>
                            <?php foreach ($orders as $order): ?>
                                <?php if ($order->status === 'completed'): ?>
                                    <div class="order-item">
                                        <form id="takeOrderForm<?= $order->id ?>" action="/site/take-order" method="POST">
                                            <input type="hidden" name="orderId" value="<?= $order->id ?>">
                                            <button class="progress-icon compl" id="completeOrderBtn<?= $order->id ?>" type="submit">
                                                <img src="../images/compl.svg" alt="" class="status-icon">
                                                <div class="progress-icon-text">Забрал заказ</div>
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    <?php foreach ($orders as $order): ?>
                        const completeOrderBtn<?= $order->id ?> = document.getElementById('completeOrderBtn<?= $order->id ?>');

                        completeOrderBtn<?= $order->id ?>.addEventListener('click', function () {
                            const orderId = <?= $order->id ?>; // Получаем идентификатор заказа из скрытого поля или другого источника данных

                            // Отправляем запрос на сервер для завершения заказа
                            fetch('/site/take-order', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ orderId: orderId })
                            })
                                .then(response => {
                                    if (response.ok) {
                                        // Успешно завершаем заказ на клиентской стороне
                                        const orderProgress<?= $order->id ?> = document.getElementById('orderProgress<?= $order->id ?>');
                                        if (orderProgress<?= $order->id ?>) {
                                            orderProgress<?= $order->id ?>.style.display = 'none';
                                        }
                                    } else {
                                        // Обработка ошибок, если необходимо
                                    }
                                })
                                .catch(error => {
                                    console.error('Ошибка при завершении заказа:', error);
                                });
                        });
                    <?php endforeach; ?>
                });
            </script>
            <div class="promo">
                <div class="promo-cart">
                    <?php if ($cartIsEmpty): ?>
                        <a href="payment" class="openmidal disabled-link" style="margin-right: 10px;">Оформить заказ</a>
                    <?php else: ?>
                        <a href="payment" class="openmidal" style="margin-right: 10px;">Оформить заказ</a>
                    <?php endif; ?>



                    <form id="clearCartForm" action="/site/clear-cart" method="post">
                        <button type="submit" class="clearCartBtn">Очистить корзину</button>
                    </form>
                    <button id="showOrderHistoryBtn" class="clearCartBtn">История заказов</button>
                    <div id="orderHistoryModal" class="modal">
                        <div class="modal-content odermodal">
                            <span class="close">&times;</span>
                            <h2 class="dish-title">История заказов</h2>
                            <div id="orderHistory"></div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const showOrderHistoryBtn = document.getElementById('showOrderHistoryBtn');
                            const orderHistoryModal = document.getElementById('orderHistoryModal');
                            const closeModalBtn = orderHistoryModal.querySelector('.close');

                            // Обработчик клика по кнопке "История заказов"
                            showOrderHistoryBtn.addEventListener('click', function () {
                                orderHistoryModal.style.display = 'block';
                                fetchOrderHistory(); // Запрос на получение истории заказов
                            });

                            // Обработчик клика по кнопке закрытия модального окна
                            closeModalBtn.addEventListener('click', function () {
                                orderHistoryModal.style.display = 'none';
                            });

                            // Обработчик клика вне модального окна для его закрытия
                            window.addEventListener('click', function (event) {
                                if (event.target === orderHistoryModal) {
                                    orderHistoryModal.style.display = 'none';
                                }
                            });
                        });

                        // Функция для отправки запроса на получение истории заказов
                        function fetchOrderHistory() {
                            fetch('/site/get-order-history')
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    displayOrderHistory(data); // Отображение полученной истории заказов
                                })
                                .catch(error => {
                                    console.error('Ошибка загрузки истории заказов:', error);
                                    alert('Ошибка загрузки истории заказов. Пожалуйста, попробуйте еще раз.');
                                });
                        }
                        // Функция для отображения истории заказов в модальном окне
                        function displayOrderHistory(orderHistory) {
                            const orderHistoryContainer = document.getElementById('orderHistory');

                            // Очистка предыдущего содержимого
                            orderHistoryContainer.innerHTML = '';

                            // Создание элементов для каждого заказа
                            orderHistory.forEach(order => {
                                const orderElement = document.createElement('div');
                                orderElement.classList.add('order');
                                orderElement.dataset.orderId = order.id;
                                // Создаем элементы для отображения данных о заказе
                                const orderIdElement = document.createElement('div');
                                orderIdElement.innerHTML = `<b>Номер заказа:</b> ${order.id}`;
                                orderIdElement.classList.add('order-p');

                                const orderedItemsElement = document.createElement('div');
                                orderedItemsElement.innerHTML = `<b>Товары в заказе:</b> ${order.itemNames.join(', ')}`;
                                orderedItemsElement.classList.add('order-p');

                                const orderDateElement = document.createElement('div');

                                // Преобразуем строку даты в объект Date
                                const orderDate = new Date(order.createdAt);

                                // Форматируем дату в нужном формате (например, DD.MM.YYYY HH:mm)
                                const formattedDate = `${orderDate.getDate()}.${orderDate.getMonth() + 1}.${orderDate.getFullYear()} ${orderDate.getHours()}:${String(orderDate.getMinutes()).padStart(2, '0')}`;


                                orderDateElement.innerHTML = `<b>Дата:</b> ${formattedDate}`;
                                orderDateElement.classList.add('order-p');


                                const orderTotalElement = document.createElement('div');
                                orderTotalElement.innerHTML = `<b>Сумма:</b> ${order.total} руб`;
                                orderTotalElement.classList.add('order-p');
                                orderTotalElement.id = `orderTotal_${order.id}`; // Уникальный идентификатор для каждого заказа


                                const repeatOrderButton = document.createElement('button');
                                repeatOrderButton.classList.add('order-btn');

                                // Создаем элемент изображения (иконка)
                                const iconImg = document.createElement('img');
                                iconImg.src = 'images/repeat.svg'; // Установите путь к вашей иконке
                                iconImg.alt = 'Повторить заказ'; // Установите атрибут alt для изображения
                                const tooltipText = document.createElement('span');
                                tooltipText.classList.add('tooltip-text');
                                tooltipText.textContent = 'Повторить';
                                // Добавляем обработчик события при клике на кнопку
                                repeatOrderButton.addEventListener('click', function () {
                                    repeatOrder(order.id);
                                });
                                const deleteOrderButton = document.createElement('button');
                                deleteOrderButton.classList.add('order-btn', 'delete-order-button');
                                deleteOrderButton.dataset.orderId = order.id; // Добавляем data-order-id

                                const deleteIconImg = document.createElement('img');
                                deleteIconImg.src = 'images/delete.svg';
                                deleteIconImg.alt = 'Удалить заказ';
                                const deleteTooltipText = document.createElement('span');
                                deleteTooltipText.classList.add('tooltip-text');
                                deleteTooltipText.textContent = 'Удалить';

                                deleteOrderButton.appendChild(deleteIconImg);
                                deleteOrderButton.appendChild(deleteTooltipText);

                                // Добавляем обработчик события нажатия для кнопки удаления
                                deleteOrderButton.addEventListener('click', function () {
                                    const orderId = this.dataset.orderId;
                                    deleteOrder(orderId);
                                });
                                // Добавляем изображение в кнопку
                                repeatOrderButton.appendChild(iconImg);
                                // Передаем orderId при нажатии на кнопку
                                repeatOrderButton.appendChild(tooltipText);
                                // Добавляем созданные элементы в контейнер истории заказов
                                orderElement.appendChild(orderIdElement);
                                orderElement.appendChild(orderDateElement);
                                orderElement.appendChild(orderedItemsElement);
                                orderElement.appendChild(orderTotalElement);
                                orderElement.appendChild(repeatOrderButton);
                                orderElement.appendChild(deleteOrderButton);

                                // Добавляем элемент заказа в контейнер истории заказов
                                orderHistoryContainer.appendChild(orderElement);
                            });
                        }
                        async function deleteOrder(orderId) {
                            try {
                                const response = await fetch('/site/delete-order', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({ orderId: orderId })
                                });

                                const data = await response.json();

                                if (data.success) {
                                    // Удаляем элемент заказа из DOM
                                    const orderElement = document.querySelector(`div[data-order-id="${orderId}"]`);
                                    if (orderElement) {
                                        orderElement.remove();
                                    }
                                    showNotification('Заказ успешно удален!', true);
                                } else {
                                    console.error('Ошибка удаления заказа:', data.message);
                                    showNotification('Ошибка удаления заказа: ' + data.message, false);
                                }
                            } catch (error) {
                                console.error('Ошибка при удалении заказа:', error);
                                showNotification('Произошла ошибка при удалении заказа.', false);
                            }
                        }
                        async function repeatOrder(orderId) {
                            try {
                                const progressBar = document.getElementById('orderProgress');
                                if (progressBar) {
                                    progressBar.style.display = 'block'; // Показываем прогресс-бар
                                } else {
                                    console.error('Элемент с идентификатором orderProgress не найден.');
                                }

                                const response = await fetch('/site/repeat-order', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({ orderId: orderId })
                                });

                                const data = await response.json();

                                if (data.status === 'success') {
                                    showNotification('Заказ успешно повторен!', true);
                                    // Добавляем задержку в 2 секунды перед перенаправлением на страницу корзины
                                    setTimeout(function () {
                                        window.location.href = '/cart'; // Измените путь на соответствующий
                                    }, 2000); // Задержка в миллисекундах (в данном случае 2 секунды)
                                } else {
                                    showNotification('Ошибка повторения заказа: ' + data.message, false);
                                }
                            } catch (error) {
                                console.error('Ошибка при повторении заказа:', error);
                                showNotification('Произошла ошибка при повторении заказа.', false);
                            }
                        }
                        document.addEventListener('DOMContentLoaded', function () {
                            const profileUpdated = <?= json_encode(Yii::$app->session->get('profileUpdated', false)) ?>;
                            if (profileUpdated) {
                                showNotification('Заказ успешно повторен!', true);
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
                </div>
            </div>
            <script>
                let currentStatus = '';
                document.addEventListener('DOMContentLoaded', async function () {
                    const orderExists = await checkOrderExists();
                    if (orderExists) {
                        document.getElementById('orderProgress').style.display = 'block';
                        const status = await getOrderStatus();
                        currentStatus = status;
                        updateProgressBar(status);
                        updateProgressText(status);
                        updateOrderStatusPeriodically(); // Добавлен вызов функции для периодического обновления статуса
                    } else {
                        document.getElementById('orderProgress').style.display = 'none'; // Прячем прогресс-бар, если заказ не существует
                    }

                    // Выполнять функцию updateOrderStatus каждые 10 секунд
                    setInterval(updateOrderStatus, 3000); // Обновляем статус каждые 10 секунд
                });

                async function updateOrderStatus() {
                    try {
                        const response = await fetch('/site/update-order-status-every-ten-seconds', {
                            method: 'POST',
                        });
                        const data = await response.json();
                        console.log(data);
                        if (data.status === 'completed' && currentStatus !== 'completed') {
                            currentStatus = 'completed';
                            location.reload(); // Перезагружаем страницу только при первом появлении статуса confirmed
                        } else {
                            updateProgressBar(data.status);
                            updateProgressText(data.status);
                        }
                    } catch (error) {
                        console.error('Ошибка при обновлении статуса заказа:', error);
                    }
                }
                function updateProgressText(status) {
                    const progressTexts = document.querySelectorAll('.progress-bar-text');
                    progressTexts.forEach(text => {
                        const stage = text.dataset.stage;
                        if (stage === status) {
                            text.style.display = 'block';
                        } else {
                            text.style.display = 'none';
                        }
                    });
                }
                async function checkOrderExists() {
                    try {
                        const response = await fetch('/site/check-order-exists');
                        const data = await response.json();
                        if (data.exists) {
                            updateOrderStatusPeriodically(); // Вызываем функцию обновления статуса периодически, если заказ существует
                        }
                        return data.exists;
                    } catch (error) {
                        console.error('Ошибка проверки заказа:', error);
                        return false;
                    }
                }

                async function updateOrderStatusPeriodically() {
                    setInterval(async () => {
                        const status = await getOrderStatus();
                        updateProgressBar(status);
                    }, 20000); // Обновляем статус каждые 20 секунд
                }
                async function getOrderStatus() {
                    try {
                        const response = await fetch('/site/get-order-status');
                        const data = await response.json();
                        return data.status;
                    } catch (error) {
                        console.error('Ошибка получения статуса заказа:', error);
                        return null;
                    }
                }
                function updateProgressBar(status) {
                    const icons = document.querySelectorAll('.progress-icon');
                    const progressBarFill = document.querySelector('.progress-bar-fill');

                    let found = false; // Флаг для указания, что иконка с текущим статусом найдена
                    icons.forEach(icon => {
                        if (icon.dataset.stage === status) {
                            icon.classList.add('active');
                            found = true; // Устанавливаем флаг в true, если иконка найдена
                        } else {
                            if (!found) {
                                icon.classList.remove('active');
                            }
                        }
                    });
                    // Обновляем ширину элемента progress-bar-fill в зависимости от статуса
                    switch (status) {
                        case 'new':
                            progressBarFill.style.width = '33%'; // Задайте ширину в процентах в зависимости от вашего дизайна
                            break;
                        case 'processing':
                            progressBarFill.style.width = '66%';
                            break;
                        case 'completed':
                            progressBarFill.style.width = '100%';
                            break;
                        default:
                            progressBarFill.style.width = '0%'; // Если статус неизвестен, делаем ширину нулевой
                    }
                }
            </script>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Обработчик клика по кнопке "Очистить корзину"
        document.getElementById('clearCartBtn').addEventListener('click', function () {
            fetch('/site/clear-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Удалите строку с CSRF, если вы его отключили
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Очищаем таблицу корзины или обновляем интерфейс
                        const cartTableBody = document.querySelector('.cart-table tbody');
                        cartTableBody.innerHTML = '';
                        $('#current-total-cost').text('0 руб'); // Обновляем текущую сумму

                        // Показываем сообщение "Корзина пуста"
                        document.getElementById('emptyCartMessage').style.display = 'block';
                        // Опционально: Показать сообщение об успешной очистке корзины
                        console.log('Корзина очищена');
                    } else {
                        // Обработка случая, когда корзина не была очищена
                        console.error('Ошибка при очистке корзины');
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                });
        });

        // Проверяем состояние корзины после загрузки страницы
        if (document.querySelectorAll('.cart-table tbody tr').length === 0) {
            var emptyCartElement = document.querySelector('.empty-cart');
            if (emptyCartElement) {
                emptyCartElement.style.display = 'flex';
            }
        }
    });
</script>


</div>
</div>
</div>