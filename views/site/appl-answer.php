<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Список вакансий';
?>
<div class="appl-form">
    <h1 class="appl-title"><?= Html::encode($this->title) ?></h1>

    <ul class="appl-ul">
        <?php foreach ($vacancies as $vacancy): ?>
            <li class="appl-li">
                <a class="link appl-link link--arrowed"
                    href="<?= Yii::$app->urlManager->createUrl(['site/view-vacancy', 'id' => $vacancy->id]) ?>">
                    <?= $vacancy->full_name ?>
                    <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                        <g fill="none" stroke="#000" stroke-width="1.5" stroke-linejoin="round" stroke-miterlimit="10">
                            <circle class="arrow-icon--circle" cx="16" cy="16" r="15.12"></circle>
                            <path class="arrow-icon--arrow" d="M16.14 9.93L22.21 16l-6.07 6.07M8.23 16h13.98"></path>
                        </g>
                    </svg>
                </a>

            </li>
        <?php endforeach; ?>
    </ul>
</div>