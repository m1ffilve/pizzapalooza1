<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "pizza_rating".
 *
 * @property int $id
 * @property int $pizza_id
 * @property int $rating
 */
class PizzaRating extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pizza_rating';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pizza_id', 'rating'], 'required'],
            [['pizza_id', 'rating'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pizza_id' => 'Pizza ID',
            'rating' => 'Rating',
        ];
    }
    public function getPizza()
    {
        return $this->hasOne(Pizza::class, ['id' => 'pizza_id']);
    }
}
