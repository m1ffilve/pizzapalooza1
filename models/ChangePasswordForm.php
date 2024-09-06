<?php

namespace app\models;

use yii\base\Model;

class ChangePasswordForm extends Model
{
    public $newPassword;
    public $confirmPassword;

    public function rules()
    {
        return [
            [['newPassword', 'confirmPassword'], 'required', 'message' => 'Новый пароль не может быть пустым.'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'newPassword', 'message' => 'Пароли должны совпадать'],
        ];
    }

}
