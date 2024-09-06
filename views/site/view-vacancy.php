<!-- view-vacancy.php -->

<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Вакансия №' . $vacancy->id;
?>
<div class="appl-form">
    <h1 class="appl-title"><?= Html::encode($this->title) ?></h1>

    <p class="appl-info"><strong>ФИО:</strong> <?= Html::encode($vacancy->full_name) ?></p>
    <p class="appl-info"><strong>Почта:</strong> <?= Html::encode($vacancy->email) ?></p>
    <p class="appl-info"><strong>Номер телефона:</strong> <?= Html::encode($vacancy->phone) ?></p>

    <?php if ($vacancy->resume_path): ?>
        <p class="appl-info"><strong>Резюме:</strong>
            <?= Html::a('Скачать резюме', Url::to('/' . $vacancy->resume_path), ['target' => '_blank', 'class' => 'link-down']) ?>
        </p>
    <?php else: ?>
        <p class="appl-info"><strong>Резюме:</strong> Резюме отсутствует</p>
    <?php endif; ?>
</div>