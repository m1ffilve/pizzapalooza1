<!-- views/site/profile.php -->
<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap5\ActiveForm;
use yii\jui\DatePicker;

$this->title = 'Профиль';
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="profile">
    <div class="profile-menu">
        <div class="profile-view">
            <h2 class="profile-info">Информация пользователя</h2>
            <div class="zna4enie">
                <div class="profile-row">
                    <p class="pr-name">Имя</p>
                    <div class="value-container">
                        <p class="pr-names" data-field="name"><?= Yii::$app->user->identity->name ?: 'Не указано' ?></p>
                        <button class="btn-name edit-btn" data-field="name" data-title="Смена имени"><img
                                src="/images/edit.svg" alt="" class="btn-name-ico"></button>
                    </div>
                </div>

                <div class="profile-row">
                    <p class="pr-name">Номер телефона</p>
                    <div class="value-container">
                        <p class="pr-names" data-field="phone_number">
                            <?= Yii::$app->user->identity->phone_number ?: 'Не указан' ?>
                        </p>
                        <button class="btn-name edit-btn" data-field="phone_number"
                            data-title="Смена номера телефона"><img src="/images/edit.svg" alt=""
                                class="btn-name-ico"></button>
                    </div>
                </div>

                <div class="profile-row">
                    <p class="pr-name">Почта</p>
                    <div class="value-container">
                        <p class="pr-names" data-field="email"><?= Yii::$app->user->identity->email ?: 'Не указана' ?>
                        </p>
                        <button class="btn-name edit-btn" data-field="email" data-title="Смена почты"><img
                                src="/images/edit.svg" alt="" class="btn-name-ico"></button>
                    </div>
                </div>

                <div class="profile-row">
                    <p class="pr-name">Пол</p>
                    <div class="value-container">
                        <p class="pr-names" data-field="gender">
                            <?= Yii::$app->user->identity->gender === 'Мужской' ? 'Мужской' : (Yii::$app->user->identity->gender === 'Женский' ? 'Женский' : 'Не указан') ?>
                        </p>
                        <button class="btn-name edit-btn" data-field="gender" data-title="Смена пола"><img
                                src="/images/edit.svg" alt="" class="btn-name-ico"></button>
                        <div class="gender-buttons" style="display: none;">
                            <button class="btn-name gender-option" data-gender="Мужской"><img src="/images/male.svg"
                                    alt="" class="btn-name-ico"></button>
                            <button class="btn-name gender-option" data-gender="Женский"><img src="/images/female.svg"
                                    alt="" class="btn-name-ico"></button>
                        </div>
                    </div>
                </div>

                <div class="profile-row">
                    <p class="pr-name">Дата рождения</p>
                    <div class="value-container">
                        <p class="pr-names" data-field="birthdate">
                            <?= Yii::$app->user->identity->birthdate ?: 'Не указана' ?>
                        </p>
                        <button class="btn-name edit-btn" data-field="birthdate" data-title="Смена даты рождения"><img
                                src="/images/edit.svg" alt="" class="btn-name-ico"></button>
                    </div>
                </div>

                <!-- Модальное окно для редактирования данных -->
                <div id="editModal" class="modal">
                    <div class="modal-content profile-modal">
                        <h5 id="modalTitle" class="edittitle"></h5>
                        <span class="close editclose">&times;</span>
                        <div class="profile-edits">
                            <div id="editInputContainer" class="edit-input-container"></div>
                            <button id="saveBtn" class="modal-btn">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
            <button id="changePasswordBtn" class="changepass">Сменить пароль</button>

            <!-- Модальное окно для смены пароля -->
            <div id="changePasswordModal" class="modal" style="display: none;">
                <div class="modal-content profile-modal">
                    <span class="close" id="closePasswordModal">&times;</span>
                    <h2 id="changetitle">Смена пароля</h2>
                    <form id="changePasswordForm">
                        <div class="form-group">
                            <label for="newPassword">Новый пароль</label>
                            <input type="password" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Подтвердите новый пароль</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <div id="errorContainer" class="error-container" style="display: none;"></div>
                        <button type="submit" class="changepassbtn">Сменить пароль</button>
                    </form>
                </div>
            </div>
            <div id="notification" class="notification"></div>

            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function () {
                    $('.edit-btn').click(function () {
                        var field = $(this).data('field');
                        var title = $(this).data('title');
                        var currentValue = $(this).closest('.value-container').find('.pr-names').text().trim();

                        // Set modal title based on field
                        $('#modalTitle').text(title);

                        // Prepare input based on field type
                        switch (field) {
                            case 'name':
                                $('#editInputContainer').html(`<input type="text" id="editInput" class="modal-input" value="${currentValue}">`);
                                break;
                            case 'phone_number':
                                $('#editInputContainer').html(`<input type="tel" id="editInput" class="modal-input" value="${currentValue}" placeholder="+7 (___) ___-__-__">`);
                                break;
                            case 'email':
                                $('#editInputContainer').html(`<input type="email" id="editInput" class="modal-input" value="${currentValue}">`);
                                break;
                            case 'gender':
                                $('#editInputContainer').html(`
                        <div class="gender-buttons">
                            <button class="btn-name gender-option" data-gender="Мужской"><img src="/images/male.svg" alt="" class="btn-name-ico">Мужской</button>
                            <button class="btn-name gender-option" data-gender="Женский"><img src="/images/female.svg" alt="" class="btn-name-ico">Женский</button>
                        </div>`);
                                break;
                            case 'birthdate':
                                $('#editInputContainer').html(`<input type="date" id="editInput" class="modal-input" value="${currentValue}">`);
                                break;
                            default:
                                break;
                        }

                        // Display modal
                        $('#editModal').css('display', 'block');

                        // Save changes on button click
                        $('#saveBtn').click(function () {
                            var newValue = $('#editInput').val().trim();

                            // Validate and save changes
                            if (validateField(field, newValue)) {
                                updateProfileField(field, newValue);
                            }
                        });
                    });

                    // Gender selection
                    $(document).on('click', '.gender-option', function () {
                        var newGender = $(this).data('gender');
                        updateProfileField('gender', newGender);
                    });

                    // Modal close functionality
                    $('.close').click(function () {
                        $('#editModal').css('display', 'none');
                    });

                    // Function to update profile field via AJAX
                    function updateProfileField(field, value) {
                        $.ajax({
                            url: '/site/edit-profile', // Путь к обработчику AJAX на сервере
                            method: 'POST',
                            data: { field: field, value: value },
                            success: function (response) {
                                if (response.success) {
                                    $('p[data-field="' + field + '"]').text(value);
                                    $('#editModal').css('display', 'none'); // Hide modal on success
                                    showNotification('Данные успешно обновлены!', true);
                                } else {
                                    showNotification('Произошла ошибка: ' + response.message, false);
                                }
                            },
                            error: function () {
                                showNotification('Произошла ошибка при выполнении запроса.', false);
                            }
                        });
                    }

                    // Validation function for each field
                    function validateField(field, value) {
                        switch (field) {
                            case 'name':
                                if (value === '') {
                                    showNotification('Введите имя.', false);
                                    return false;
                                }
                                break;
                            case 'phone_number':
                                // Add more complex validation for phone number if needed
                                if (value === '') {
                                    showNotification('Введите номер телефона.', false);
                                    return false;
                                }
                                break;
                            case 'email':
                                // Add email format validation if needed
                                if (value === '') {
                                    showNotification('Введите почту.', false);
                                    return false;
                                }
                                break;
                            case 'birthdate':
                                // Validate birthdate
                                if (value === '') {
                                    showNotification('Введите дату рождения.', false);
                                    return false;
                                }
                                const selectedDate = new Date(value);
                                const today = new Date();
                                const sixYearsAgo = new Date();
                                sixYearsAgo.setFullYear(today.getFullYear() - 6);

                                if (selectedDate > today) {
                                    showNotification('Дата рождения не может быть в будущем.', false);
                                    return false;
                                }

                                if (selectedDate > sixYearsAgo) {
                                    showNotification('Вы должны быть старше 6 лет.', false);
                                    return false;
                                }
                                break;
                            default:
                                return true;
                        }
                        return true;
                    }

                    // Notification function
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
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('changePasswordBtn').addEventListener('click', function () {
                        document.getElementById('changePasswordModal').style.display = 'block';
                    });

                    document.getElementById('closePasswordModal').addEventListener('click', function () {
                        document.getElementById('changePasswordModal').style.display = 'none';
                    });

                    document.getElementById('changePasswordForm').addEventListener('submit', function (e) {
                        e.preventDefault();

                        var newPassword = document.getElementById('newPassword').value;
                        var confirmPassword = document.getElementById('confirmPassword').value;

                        var formData = {
                            'ChangePasswordForm[newPassword]': newPassword,
                            'ChangePasswordForm[confirmPassword]': confirmPassword
                        };

                        $.ajax({
                            url: '/site/change-password',
                            method: 'POST',
                            data: formData,
                            success: function (response) {
                                if (response.success) {
                                    showNotification('Пароль успешно изменен.', true);
                                    document.getElementById('changePasswordModal').style.display = 'none';
                                } else {
                                    showNotification('Ошибка при изменении пароля: ' + response.message, false);
                                    if (response.errors) {
                                        var errorContainer = document.getElementById('errorContainer');
                                        errorContainer.innerHTML = '';
                                        for (var key in response.errors) {
                                            var errors = response.errors[key];
                                            errors.forEach(function (error) {
                                                var errorElement = document.createElement('div');
                                                errorElement.textContent = error;
                                                errorContainer.appendChild(errorElement);
                                            });
                                        }
                                        errorContainer.style.display = 'block';
                                    }
                                }
                            },
                            error: function () {
                                showNotification('Произошла ошибка при выполнении запроса.', false);
                            }
                        });
                    });
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
            <div class="profile-links">
                <?= Html::a('Редактировать профиль', ['site/edit-profile'], ['class' => 'profile-edit']); ?>

                <div class="div"></div>
                <?php
                $user = Yii::$app->user->identity;
                if ($user && $user->role == 1) {
                    echo '<hr>';
                    echo Html::a('Дополнить меню', ['site/add-menu'], ['class' => 'profile-edit']);
                    echo Html::a('Вакансии', ['site/appl-answer'], ['class' => 'profile-edit']);
                    echo Html::a('Статистика', ['site/stats'], ['class' => 'profile-edit']);
                    echo Html::a('Редактировать блюдо', '#', ['id' => 'editDishLink', 'class' => 'profile-edit']);
                    echo Html::a('Добавить промокод', '#openModal2', ['class' => 'btn-success', 'id' => 'openModalButton']);
                }
                ?>
            </div>
        </div>
        <div id="fileAttachedNotification" class="notification" style="display: none;">
            <span id="notificationMessage"></span>
        </div>
    </div>
</div>
<div id="editDishModal" class="modal">
    <div class="modal-contents">
        <span class="close">&times;</span>
        <h2 class="dish-title">Редактировать блюдо</h2>
        <div id="dishSelectContainer">
            <select id="dishSelect">
                <option value="">Выберите блюдо</option>
                <?php foreach ($dishes as $dish): ?>
                    <option value="<?= Html::encode($dish->id) ?>"><?= Html::encode($dish->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="dishEditForm" style="display: none;">
            <form id="editDishForm">
                <input type="text" id="dishName" name="name" placeholder="Название блюда">
                <textarea id="dishComposition" name="composition" placeholder="Описание"></textarea>
                <textarea id="dishHistory" name="history" placeholder="История"></textarea>
                <input type="number" id="dishPrice" name="price" step="0.01" placeholder="Цена">
                <!-- Add more fields as needed -->
                <button type="submit" class="btn-primary">Сохранить изменения</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('editDishModal');
        var btn = document.getElementById('editDishLink');
        var span = document.getElementsByClassName('close')[0];
        var dishSelect = document.getElementById('dishSelect');
        var dishEditForm = document.getElementById('dishEditForm');
        var form = document.getElementById('editDishForm');

        btn.onclick = function (event) {
            event.preventDefault();
            modal.style.display = 'block';
        }

        span.onclick = function () {
            modal.style.display = 'none';
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        dishSelect.onchange = function () {
            if (this.value) {
                loadDishDetails(this.value);
                dishEditForm.style.display = 'block';
            } else {
                dishEditForm.style.display = 'none';
            }
        }

        form.onsubmit = function (event) {
            event.preventDefault();
            var formData = new FormData(form);
            formData.append('id', dishSelect.value);

            fetch('/site/update-dish', {
                method: 'POST',
                body: formData,
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Успех',
                            text: 'Блюдо успешно обновлено.'
                        }).then(() => {
                            modal.style.display = 'none';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ошибка',
                            text: 'Ошибка: ' + data.message
                        });
                    }
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка',
                        text: 'Произошла ошибка при обновлении блюда.'
                    });
                });
        }

        function loadDishDetails(dishId) {
            fetch('/site/get-dish-details?id=' + dishId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'error') {
                        alert('Ошибка: ' + data.message);
                        return;
                    }
                    document.getElementById('dishName').value = data.name;
                    document.getElementById('dishComposition').value = data.composition;
                    document.getElementById('dishHistory').value = data.history;
                    document.getElementById('dishPrice').value = data.price;
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при загрузке данных блюда.');
                });
        }

    });
</script>
<div class="modal" id="openModal" tabindex="-1" role="dialog" aria-labelledby="addPromoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPromoModalLabel">Добавить промокод</h5>
                <a href="#close" title="Close" class="close">×</a>
            </div>
            <div class="modal-body">
                <!-- Сюда будет вставлен вид add-promo.php -->
                <?php echo $this->render('add-promo', ['promoModel' => $promoModel]); ?>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap CSS -->

<!-- jQuery (необходимо загрузить перед Bootstrap JavaScript) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        // Устанавливаем обработчик события клика на элемент с id="openModalButton"
        $('#openModalButton').click(function (e) {
            e.preventDefault(); // Предотвращаем переход по ссылке по умолчанию
            // Открываем модальное окно с id="openModal"
            $('#openModal').modal('show');
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Обработчик события клика на элемент с классом "close"
        $('.close').click(function () {
            // Закрыть модальное окно с id="openModal"
            $('#openModal').modal('hide');
        });
    });

</script>