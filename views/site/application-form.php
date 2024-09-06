<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<h1 class="title">Заявка</h1>
<?php
$form = ActiveForm::begin([
  'id' => 'application-form',
  'options' => ['enctype' => 'multipart/form-data'], // Для загрузки файлов
]);

echo $form->field($model, 'full_name')->textInput(['maxlength' => true, 'placeholder' => 'ФИО'])->label(false)->error();
echo $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'Email'])->label(false)->error();
echo $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => 'Телефон'])->label(false)->error();
echo $form->field($model, 'resume')->fileInput(['placeholder' => 'Загрузить резюме'])->label(false)->error();


echo Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'formaction' => Url::to(['site/submit-application'])]);

ActiveForm::end();
?>
<div id="notification" class="notification"></div>
<script>
  // Код jQuery, устанавливающий маску для ввода телефона элементу input
  $(function () {
    $("#application-phone").mask("+7 (999) 999-99-99");
  });
  $(document).ready(function () {
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
<script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.maskedinput@1.4.1/src/jquery.maskedinput.min.js"
  type="text/javascript"></script>