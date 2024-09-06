<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class MenuForm extends Model
{
    public $name;
    public $category;
    public $price;
    public $composition;
    public $imageFile; // Новое свойство для изображения
    public $history;
    public $cook_time;
    public $weight;
    public $size;

    public function rules()
    {
        return [
            [['name', 'category', 'price', 'composition'], 'required'],
            [['price'], 'number'],
            [['history'], 'string'],
            [['cook_time', 'weight', 'size'], 'safe'],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs('../uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
}
