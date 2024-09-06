<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the registration form.
 */
class RegisterForm extends Model
{
    public $phone_number;
    public $name;
    public $password;
    public $password_repeat;
    public function rules()
    {
        return [
            [['phone_number', 'name', 'password', 'password_repeat'], 'required'],
            ['phone_number', 'string', 'max' => 255],
            ['name', 'string', 'max' => 255],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли должны совпадать'],
            [['phone_number'], 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/i', 'message' => 'Телефон должен быть в формате +7 (XXX) XXX-XX-XX.'],
            [['name'], 'match', 'pattern' => '/^[А-Яа-яЁё\s]+$/u', 'message' => 'Имя должно содержать только русские буквы.'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'phone_number' => 'Номер телефона',
            'name' => 'Имя',
            'password' => 'Пароль',
            'password_repeat' => 'Подтвердите пароль',
        ];
    }
    public function register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->phone_number = $this->phone_number;
            $user->name = $this->name;
            $user->setPassword($this->password);
            if ($user->save()) {
                return $user;
            }
        }

        return null;
    }
}
