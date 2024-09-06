<?php
use yii\helpers\Html;

/**
 * @var int $rating
 * @var int $pizzaId
 */
?>

<div class="rating" data-pizza-id="<?= $pizzaId ?>">
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <?php
        $class = $i <= $rating ? 'star-filled' : 'star-empty';
        echo Html::tag('span', '★', ['class' => "star $class", 'data-value' => $i]);
        ?>
    <?php endfor; ?>
</div>

<?php
// Добавим скрипт для обработки кликов
$js = <<<JS
$('.rating').on('click', '.star', function() {
    var pizzaId = $(this).closest('.rating').data('pizza-id');
    var rating = $(this).data('value');
    
    console.log('Sending AJAX request with pizzaId:', pizzaId, 'and rating:', rating);
    $.ajax({
        url: '/site/rate-pizza',
        type: 'post',
        data: { pizzaId: pizzaId, starClicked: rating },
        success: function(data) {
            if (data.success) {
                console.log('Success:', data);
                alert('Rating updated successfully!');

                // Обновляем средний рейтинг
                $('#averageRating' + pizzaId).text('Average Rating: ' + data.averageRating);
            } else {
                console.error('Error updating rating:', data.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating rating');
            console.log('XHR status:', status);
            console.log('XHR response text:', xhr.responseText);
            console.log('Error details:', error);
        }
    });
});
JS;

$this->registerJs($js);
?>