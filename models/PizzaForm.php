<?php

namespace app\models;

use yii\base\Model;

class PizzaForm extends Model
{
    public $name;
    public $description;
    public $price;
    public $composition;
    public $image_url;
    public $history;

    public function rules()
    {
        return [
            [['name', 'price', 'composition'], 'required'],
            [['description', 'image_url', 'history'], 'string'],
            [['price'], 'number'],
        ];
    }

}
