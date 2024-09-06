<?php
$this->title = 'Вакансии';
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="vac-form">
    <h1 class="title vac-tit"><?= $this->title ?></h1>
    <p class="intro">Мы рады вашему интересу к нашей пиццерии. Ниже Вы найдете информацию о доступных вакансиях:</p>
    <div class="vacancy">
        <div class="right-vac">
            <h2 class="vacancy-heading">Повар</h2>
            <p class="requirements">Требования:
            <ul class="list-disc">
                <li>• опыт работы на кухне от 1 года</li>
                <li>• знание основ кулинарии</li>
                <li>• ответственность и внимательность к деталям</li>
            </ul>
            </p>
            <p class="offers">Мы предлагаем:
            <ul class="list-disc">
                <li>• конкурентную заработную плату</li>
                <li>• дружный коллектив</li>
                <li>• возможность карьерного роста</li>
            </ul>
            </p>
        </div>
        <div class="left-vac">
            <img src="../images/coocker.jpg" alt="" class="vac-img">
        </div>
    </div>
    <div class="vacancy">
        <div class="right-vac">
            <h2 class="vacancy-heading">Официант</h2>
            <p class="requirements">Требования:
            <ul class="list-disc">
                <li>• коммуникабельность</li>
                <li>• умение работать в команде</li>
                <li>• знание основ этикета обслуживания</li>
            </ul>
            </p>
            <p class="offers">Мы предлагаем:
            <ul class="list-disc">
                <li>• гибкий график работы</li>
                <li>• достойную заработную плату и чаевые</li>
                <li>• возможность профессионального развития</li>
            </ul>
            </p>
        </div>
        <div class="left-vac">
            <img src="../images/offic.jpg" alt="" class="vac-img">
        </div>
    </div>
    <!-- Кнопка для открытия модального окна -->
    <div class="apply-button">
        <button id="openApplicationModalButton" class="btn apply-but btn-primary" data-toggle="modal"
            data-target="#applicationModal">Подать заявление</button>
    </div>

    <!-- Модальное окно -->
    <div class="modal" id="applicationModal" tabindex="-1" role="dialog" aria-labelledby="applicationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- Код формы для отправки заявления -->
                <h1 class="title">Заявка</h1>
                <?php
                $form = ActiveForm::begin([
                    'id' => 'application-form',
                    'options' => ['enctype' => 'multipart/form-data'], // Для загрузки файлов
                ]);

                echo $form->field($model, 'full_name')->textInput(['maxlength' => true, 'placeholder' => 'ФИО'])->label(false)->error();
                echo $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Email'])->label(false)->error();
                echo $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => 'Телефон'])->label(false)->error();
                ?>

                <?= $form->field($model, 'resume')->fileInput(['id' => 'menuform-imagefile', 'style' => 'display:none;', 'onchange' => 'showFileAttachedNotification()'])->label(false) ?>
                <label for="menuform-imagefile" class="input__file-button">
                    <span class="input__file-icon-wrapper">
                        <img class="input__file-icon" src="/images/drop.svg" alt="Выбрать файл" width="25">
                    </span>
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

                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'formaction' => Url::to(['site/submit-application'])]) ?>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <!-- Уведомление -->
    <div id="notification" class="notification"></div>

    <!-- jQuery и Bootstrap JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
        type="text/javascript"></script>

    <script>
        $(function () {
            $("#application-phone").mask("+7 (999) 999-99-99");

            $('#application-form').on('beforeSubmit', function (e) {
                e.preventDefault(); // Предотвращаем стандартное поведение формы

                var $form = $(this);
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            // Если успешно, показываем уведомление
                            showNotification('Заявка успешно отправлена!', true);
                            // Перенаправление через 5 секунд
                            setTimeout(function () {
                                window.location.href = '<?= Url::to(['site/vacancy']) ?>';
                            }, 5000);
                        } else {
                            // Если произошла ошибка, показываем уведомление об ошибке
                            showNotification(response.message || 'Ошибка при отправке заявки.', false);
                        }
                    },
                    error: function () {
                        // Если произошла ошибка, показываем уведомление об ошибке
                        showNotification('Ошибка при отправке заявки.', false);
                    }
                });
                return false; // Предотвращаем стандартное поведение формы
            });

            // Код для отображения уведомлений
            const applicationSubmitted = <?= json_encode(Yii::$app->session->getFlash('applicationSubmitted', false)) ?>;
            if (applicationSubmitted) {
                showNotification('Заявка успешно отправлена!', true);
                // Перенаправление через 5 секунд
            }

            const applicationnonSubmitted = <?= json_encode(Yii::$app->session->getFlash('applicationnonSubmitted', false)) ?>;
            if (applicationnonSubmitted) {
                showNotification('Заявка не отправлена! Все поля должны быть заполнены!', false);
                // Перенаправление через 5 секунд
            }

            function showNotification(message, isSuccess) {
                var notification = document.getElementById("notification");
                notification.textContent = message;
                notification.className = `notification ${isSuccess ? 'success' : 'error'} show`;
                setTimeout(() => {
                    notification.className = 'notification'; // Скрываем уведомление
                }, 4000); // Уведомление исчезает через 4 секунды
            }
        });

    </script>
</div>