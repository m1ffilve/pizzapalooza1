<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use app\models\PizzaRating;

class Pizza extends ActiveRecord
{
    // Дополнительные свойства и методы по необходимости.
    public $rating;
    // Определение связи с таблицей "pizza":
    public static function tableName()
    {
        return 'pizza';
    }

    // Определение правил валидации:
    public function rules()
    {
        return [
            [['name', 'category', 'price', 'composition'], 'required'],
            [['price', 'rating'], 'number'],
            [['name', 'category', 'image_url'], 'string', 'max' => 255],
            [['description', 'composition', 'history'], 'string'],
            [['created_at'], 'safe'],
            [['rating'], 'number'],
        ];
    }
    public static function getCategoriesWithTranslations()
    {
        return [
            'Pizza' => 'Пицца',
            'napitki' => 'Напитки',
            'sousi' => 'Соусы',
            'deserti' => 'Десерты',
            'zakuski' => 'Закуски',
            // Добавьте другие категории и их переводы по мере необходимости
        ];
    }
    
    public function actionSaveRating()
    {
        $request = Yii::$app->request;
        $pizzaId = $request->post('pizzaId');
        $rating = $request->post('rating');

        // Получаем ID текущего пользователя
        $userId = Yii::$app->user->id;

        // Проверяем, существует ли запись о рейтинге для данной пиццы и пользователя
        $pizzaRating = PizzaRating::find()->where(['pizza_id' => $pizzaId, 'user_id' => $userId])->one();

        if ($pizzaRating) {
            // Если запись существует, обновляем рейтинг
            $pizzaRating->rating = $rating;
            $pizzaRating->save();
        } else {
            // Если записи нет, создаем новую запись
            $pizzaRating = new PizzaRating();
            $pizzaRating->pizza_id = $pizzaId;
            $pizzaRating->rating = $rating;
            $pizzaRating->user_id = $userId;
            $pizzaRating->save();
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['success' => true];
    }
    public function calculateRating()
    {
        // Получаем сумму всех оценок для данной пиццы
        $totalRating = (float) PizzaRating::find()
            ->select('SUM(rating)')
            ->where(['pizza_id' => $this->id])
            ->scalar();

        // Получаем количество отзывов для данной пиццы
        $totalVotes = (int) PizzaRating::find()
            ->where(['pizza_id' => $this->id])
            ->count();

        // Вычисляем средний рейтинг, предотвращаем деление на ноль
        return $totalVotes > 0 ? $totalRating / $totalVotes : 0;
    }

    public static function getAllCategories()
    {
        return Pizza::find()->select('category')->distinct()->orderBy('category')->column();
    }
    public function afterFind()
    {
        parent::afterFind();
        $this->rating = $this->calculateRating();
    }
    public function getOrderedItems()
    {
        return $this->hasMany(OrderedItem::class, ['pizza_id' => 'id']);
    }
    public function getReviews()
    {
        return $this->hasMany(Review::class, ['pizza_id' => 'id']);
    }
}
