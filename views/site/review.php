<!-- views/site/reviews.php -->

<?php
use app\models\OrderedItem;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Отзывы';
$this->params['breadcrumbs'][] = $this->title;
// Проверяем, была ли отправлена форма
if (Yii::$app->request->isPost) {
    // Логируем отправленные данные формы
    error_log('Received form data: ' . print_r(Yii::$app->request->post(), true));
}
?>
<!-- layouts/main.php -->
<!-- Подключаем CSS плагина -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/raty/2.9.0/jquery.raty.min.css">
<div class="review-form">
    <h1 class="title review-title"><?= Html::encode($this->title) ?></h1>
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <?php if (empty($reviews)): ?>
                    <p class="no-review">Нет отзывов</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-container" data-review-id="<?= $review->id ?>">
                                <div class="review">
                                    <img src="../images/sms.svg" alt="" class="rev-icon">
                                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1): ?>
                                        <a href="#" title="Удалить отзыв" class="close closeup"
                                            data-review-id="<?= $review->id ?>">×</a>
                                    <?php endif; ?>
                                    <script>
                                        $(document).ready(function () {
                                            // Обработчик для удаления отзывов с подтверждением
                                            $(document).off('click', '.closeup').on('click', '.closeup', function (e) {
                                                e.preventDefault();

                                                var $this = $(this);
                                                if ($this.data('processing')) {
                                                    return;
                                                }
                                                $this.data('processing', true);

                                                var reviewId = $this.data('review-id');

                                                if (true) { // Вместо confirm(true)
                                                    $.ajax({
                                                        url: '<?= Yii::$app->urlManager->createUrl(['site/delete-review']) ?>',
                                                        type: 'POST',
                                                        data: { id: reviewId },
                                                        success: function (response) {
                                                            if (response.success) {
                                                                $('div[data-review-id="' + reviewId + '"]').remove();
                                                                updateReviewsList();
                                                            } else {
                                                                alert('Ошибка при удалении отзыва.');
                                                            }
                                                        },
                                                        error: function () {
                                                            alert('Ошибка при удалении отзыва.');
                                                        },
                                                        complete: function () {
                                                            $this.data('processing', false);
                                                        }
                                                    });
                                                } else {
                                                    $this.data('processing', false);
                                                }

                                            });
                                        });

                                        function updateReviewsList() {
                                            // Проверяем наличие отзывов
                                            if ($('.review-container').length === 0) {
                                                // Если нет отзывов, показываем текст "Нет отзывов"
                                                $('.list-group').html('<p class="no-review">Нет отзывов</p>');
                                            }
                                        }
                                    </script>
                                    <div class="rev-info">
                                        <p><strong>Пользователь:</strong> <?= Html::encode($review->user->name) ?></p>
                                        <p><strong>Рейтинг: <img src="images/star3.svg" class="rev-img" alt=""></strong>
                                            <?= Html::encode($review->rating) ?></p>
                                        <p><strong>Отзыв:</strong> <?= Html::encode($review->comment) ?></p>
                                        <p><strong>Товары в заказе:</strong>
                                            <?php if (!empty($review->order->orderedItems)): ?>
                                                <?php $items = array_map(function ($item) {
                                                    return Html::encode($item->pizza->name);
                                                }, $review->order->orderedItems); ?>
                                                <?= implode(', ', $items) ?>
                                            <?php else: ?>
                                            <p>Нет товаров в этом заказе</p>
                                        <?php endif; ?>
                                        <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1 && !$review->admin_reply): ?>
                                            <button class="btn-reply" data-review-id="<?= $review->id ?>">
                                                <img src="images/reply.svg" alt="" class="reply-img">Ответить
                                            </button>
                                        <?php endif; ?>

                                        </p>
                                    </div>
                                </div>
                                <svg class="rev-ico" role="presentation" width="33px" height="24px" viewBox="0 0 33 24">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <g transform="translate(-1244.000000, -3123.000000)" fill="#fff">
                                            <path
                                                d="M1255,3123c0,0-0.7,5.7-2,8.8c-1.9,4.3-7.7,13.4-7.7,13.4s9.3-2.5,18-8.8c2.6-1.9,10.4-11,12.6-13.5">
                                            </path>
                                        </g>
                                    </g>
                                </svg>
                                <div class="admin-response ">
                                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1): ?>
                                        <form id="replyForm<?= $review->id ?>" class="reply-form" style="display: none;">
                                            <div class="replyform">
                                                <div class="reply-group">
                                                    <div class="reply-groups">
                                                        <label class="reply-content" for="replyContent<?= $review->id ?>">Введите
                                                            ваш ответ:</label>
                                                        <textarea class="reply-control" id="replyContent<?= $review->id ?>"
                                                            rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <button type="submit" class="submit-reply"
                                                    data-review-id="<?= $review->id ?>">Отправить</button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($review->admin_reply): ?>
                                        <div class="admin-reply">
                                            <?php if (!Yii::$app->user->isGuest && Yii::$app->user->identity->role == 1): ?>
                                                <a href="#" title="Удалить ответ" class="close delete-reply"
                                                    data-review-id="<?= $review->id ?>">×</a>
                                            <?php endif; ?>
                                            <img src="../images/sms.svg" alt="" class="rev-icon">
                                            <p><strong class="spans-rep">Администратор</strong> <img src="/images/replys.svg" alt=""
                                                    class="reply-img2">
                                                    <?= Html::encode($review->user->name) ?>
                                                <br>
                                            </p>
                                            <p class="adm-rep"><?= htmlspecialchars($review->admin_reply, ENT_QUOTES, 'UTF-8') ?>
                                            </p>
                                        </div>
                                        <script>
                                            $(document).on('click', '.delete-reply', function (e) {
                                                e.preventDefault();

                                                var reviewId = $(this).data('review-id');

                                                $.ajax({
                                                    url: '<?= \yii\helpers\Url::to(['site/delete-reply']) ?>',
                                                    type: 'POST',
                                                    data: {
                                                        reviewId: reviewId,
                                                        _csrf: '<?= Yii::$app->request->csrfToken ?>'
                                                    },
                                                    success: function (response) {
                                                        if (response.success) {
                                                            location.reload(); // Перезагрузить страницу для обновления данных
                                                        } else {
                                                            alert('Ошибка при удалении ответа');
                                                        }
                                                    },
                                                    error: function () {
                                                        alert('Ошибка при удалении ответа');
                                                    }
                                                });
                                            });
                                        </script>
                                        <svg class="rev-ico2" role="presentation" width="33px" height="24px" viewBox="0 0 33 24">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g transform="translate(-1244.000000, -3123.000000)" fill="#fff">
                                                    <path
                                                        d="M1255,3123c0,0-0.7,5.7-2,8.8c-1.9,4.3-7.7,13.4-7.7,13.4s9.3-2.5,18-8.8c2.6-1.9,10.4-11,12.6-13.5">
                                                    </path>
                                                </g>
                                            </g>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.submit-reply').click(function (e) {
                e.preventDefault(); // Предотвращаем отправку формы по умолчанию

                var reviewId = $(this).data('review-id');
                var adminReply = $('#replyContent' + reviewId).val();

                console.log('Review ID:', reviewId);
                console.log('Admin Reply:', adminReply);

                $.ajax({
                    url: '/site/reply', // убедитесь, что URL правильный
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        reviewId: reviewId,
                        adminReply: adminReply,
                        _csrf: '<?= Yii::$app->request->csrfToken ?>'
                    },
                    success: function (response) {
                        if (response.success) {
                            // Создаем HTML-код нового ответа
                            var adminReplyHtml = `
                            <div class="admin-reply">
                                <a href="#" title="Удалить ответ" class="close delete-reply" data-review-id="${reviewId}">×</a>
                                <img src="../images/sms.svg" alt="" class="rev-icon">
                                <p class="adm-rep">${adminReply}</p>
                            </div>`;

                            // Добавляем новый ответ на страницу
                            $('.admin-response[data-review-id="' + reviewId + '"]').append(adminReplyHtml);

                            // Скрываем форму для ввода ответа
                            $('#replyForm' + reviewId).hide();

                            // Очищаем поле ввода ответа
                            $('#replyContent' + reviewId).val('');
                            location.reload();
                        } else {
                            console.error(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Ошибка при отправке данных:', error);
                    }
                });
            });
        });
    </script>


    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        // Находим все кнопки "Ответить"
        var replyButtons = document.querySelectorAll('.btn-reply');

        // Для каждой кнопки устанавливаем обработчик события клика
        replyButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                // Получаем ID отзыва
                var reviewId = button.getAttribute('data-review-id');

                // Находим форму ответа для данного отзыва и отображаем ее
                var replyForm = document.getElementById('replyForm' + reviewId);
                if (replyForm) {
                    replyForm.style.display = 'block';
                }
            });
        });
    </script>
    <div class="col-md-6">
        <?php if ($lastOrder): ?>
            <h2>Отзыв на последний заказ</h2>
            <p>Заказ №<?= Html::encode($lastOrder->id) ?>, сделанный <?= Html::encode($lastOrder->created_at) ?></p>

            <!-- Форма для отправки отзыва -->
            <?php $form = ActiveForm::begin(['action' => ['site/add-review']]); ?>
            <?= $form->field($reviewModel, 'order_id')->hiddenInput(['value' => $lastOrder->id])->label(false) ?>

            <div class="form-group">
                <label for="rating">Рейтинг</label>
                <div id="rating"></div> <!-- Здесь будут звездочки -->
                <?= $form->field($reviewModel, 'rating')->hiddenInput(['id' => 'rating-input'])->label(false) ?>
            </div>
            <?= $form->field($reviewModel, 'comment')->textarea(['rows' => 4]) ?>
            <div class="form-group btn-center">
                <?= Html::submitButton('Добавить отзыв', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <p>У вас пока нет заказов для отзыва.</p>
        <?php endif; ?>
    </div>

    <!-- Добавьте этот скрипт внизу вашего представления -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- Подключаем jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raty/2.9.0/jquery.raty.min.js"></script>
    <!-- Подключаем плагин Raty -->

    <script>
        $(function () {
            $('#rating').raty({
                scoreName: 'rating', // Имя поля рейтинга в форме
                path: '../images/', // Путь к изображениям звездочек
                score: 0, // Начальное значение рейтинга
                starOn: '/star3.svg', // Изображение активной звездочки
                starOff: '/star1.svg', // Изображение неактивной звездочки
                hints: ['Ужасно', 'Плохо', 'Нормально', 'Хорошо', 'Отлично'], // Подсказки при наведении
                click: function (score, event) {
                    $('#rating-input').val(score); // Обновляем значение скрытого поля rating при выборе рейтинга
                }
            });
        });
    </script>
</div>