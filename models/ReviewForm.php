<?php


namespace app\models;

use Yii;
use yii\base\Model;

class ReviewForm extends Model
{
    public $rating;
    public $comment;
    public $user_id;
    public $order_id; // Используем название 'order_id'

    public function rules()
    {
        return [
            [['user_id', 'rating', 'comment', 'order_id'], 'required'],
            [['user_id', 'rating', 'order_id'], 'integer'],
            ['comment', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'rating' => 'Рейтинг',
            'comment' => 'Комментарий',
        ];
    }

    public function saveReview()
    {
        $review = new Review();
        $review->user_id = $this->user_id;
        $review->rating = $this->rating;
        $review->comment = $this->comment;
        $review->order_id = $this->order_id;
        return $review->save();
    }
}