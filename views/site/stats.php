<?php
use yii\helpers\Html;
use yii\web\View;
use app\models\Pizza;
use yii\grid\GridView;
$this->title = 'Статистика';
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => View::POS_HEAD]);
?>
<div class="stats-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="stats-uvu">
        <div class="stats-container">
            <div class="top-dishes">
                <h2>Топ-5 популярных блюд</h2>
                <?php foreach ($topDishes as $index => $dish): ?>
                    <div class="dish">
                        <?php $pizza = $dish->pizza; ?>
                        <img src="<?= $pizza->image_url ?>" alt="<?= $pizza->name ?>" class="dish-image">
                        <p class="dish-name"><?= $pizza->name ?></p>
                        <img src="images/star3.svg" alt="" class="dish-img">
                        <p class="dish-rating"><?= $pizza->rating ?></p>
                    </div>
                <?php endforeach; ?>
                <div class="dish-separator"></div>
            </div>
            <div class="orders-list">
    <h2>Список заказов</h2>
    <ul>
        <?php foreach ($orders as $order): ?>
            <li>
                <div class="order-details">
                    <span class="order-name"><?= $order->name ?></span>
                    <?php
                    $totalAmount = 0;
                    $orderedItems = $order->orderedItems;
                    $itemNames = [];

                    foreach ($orderedItems as $item) {
                        $pizza = Pizza::findOne($item->pizza_id);

                        if ($pizza !== null) {
                            $itemNames[] = $pizza->name;
                            $totalAmount += $pizza->price;
                        }
                    }
                    ?>
                    <?php if (!empty($itemNames)): ?>
                        <span class="order-items"><?= implode(', ', $itemNames) ?></span>
                    <?php endif; ?>
                    <span class="order-amount"><?= $totalAmount ?> руб</span>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


        </div>
        <a href="#" id="openModal" class="user-count-link"><p class="user-count">Просмотр пользователей: <?= $usersCount ?></p></a>
        <div id="userModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="custom-close">&times;</span>
        <h2 class="tittle">Все пользователи</h2>
        <div id="userList">
            <table class="custom-table">
            <thead>
                <tr>
                    <th style="text-align: center;">Имя</th>
                    <th style="text-align: center;">Дата рождения</th>
                    <th style="text-align: center; width: 80px;">Пол</th>
                    <th style="text-align: center;">Email</th>
                    <th style="text-align: center;">Номер телефона</th>
                </tr>
            </thead>
                <tbody>
                    <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td style="text-align: center;"><?= Html::encode($user->name) ?></td>
                            <td style="text-align: center;"><?= Yii::$app->formatter->asDate($user->birthdate, 'php:d-m-Y') ?></td>
                            <td style="text-align: center;"><?= Html::encode($user->getGenderLabel()) ?></td>
                            <td style="text-align: center;"><?= Html::encode($user->email) ?></td>
                            <td style="text-align: center;"><?= Html::encode($user->phone_number) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Скрипт для управления модальным окном -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('userModal');
        var btn = document.getElementById('openModal');
        var span = document.getElementsByClassName('custom-close')[0];

        btn.onclick = function() {
            modal.style.display = 'block';
        }

        span.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    });
</script>
        <p class="user-count">Общее количество блюд: <?= $productsCount ?></p>
    </div>
</div>