<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $phone_number
 * @property string $password_hash
 * @property string $name
 * @property string $email
 * @property string $gender
 * @property string $birthdate
 */
class User extends ActiveRecord implements IdentityInterface
{
    const GENDER_MALE = 'мужской';
    const GENDER_FEMALE = 'женский';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user'; // Замените 'user' на имя вашей таблицы в базе данных
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone_number', 'password_hash'], 'required'],
            [['phone_number'], 'unique'],
            [['password_hash'], 'string', 'min' => 6],
            [['name', 'email', 'gender', 'birthdate'], 'safe'],
            [['role'], 'in', 'range' => [1, 2]],
            [['phone_number'], 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/i', 'message' => 'Телефон должен быть в формате +7 (XXX) XXX-XX-XX.'],
            [['name'], 'match', 'pattern' => '/^[А-Яа-яЁё\s]+$/u', 'message' => 'Имя должно содержать только русские буквы.'],
            [['email'], 'email', 'message' => 'Неверный формат адреса электронной почты.'],
            [['birthdate'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'phone_number' => 'Номер телефона',
            'name' => 'Имя',
            'email' => 'Email',
            'gender' => 'Пол',
            'birthdate' => 'Дата рождения',
            'password' => 'Пароль',
            'password_repeat' => 'Подтвердите пароль',
        ];
    }

    /**
     * Returns the gender label based on the constant values.
     *
     * @return string
     */
    public function getGenderLabel()
    {
        if ($this->gender === self::GENDER_MALE) {
            return 'Мужской';
        } elseif ($this->gender === self::GENDER_FEMALE) {
            return 'Женский';
        } else {
            return 'Не указан'; // Возвращаем 'Не указан' если пол не задан или не совпадает с известными значениями
        }
    }
    
    
    /**
     * Validates a password.
     *
     * @param string $password The password to be validated
     * @return bool Whether the password is valid
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Sets password hash for the user.
     *
     * @param string $password The password to be set
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param int|string $id The ID to be searched
     * @return IdentityInterface|null The identity object associated with the ID, `null` if none found
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given access token.
     *
     * @param mixed $token The token to be searched
     * @param null|string $type The type of the token
     * @return void|null The identity object associated with the token, `null` if none found
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Implement if you are using access tokens for authentication
        return null;
    }

    /**
     * Returns the ID of the current user.
     *
     * @return int|string The ID of the current user
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the authentication key.
     *
     * @return null|string The authentication key, `null` if not implemented
     */
    public function getAuthKey()
    {
        // Implement if you are using authentication key
        return null;
    }

    /**
     * Validates the given authentication key.
     *
     * @param string $authKey The authentication key to be validated
     * @return bool Whether the authentication key is valid
     */
    public function validateAuthKey($authKey)
    {
        // Implement if you are using authentication key
        return null;
    }

    /**
     * Finds a user by the given phone number.
     *
     * @param string $phone_number The phone number to search for
     * @return static|null The user object, `null` if none found
     */
    public static function findByPhoneNumber($phone_number)
    {
        return static::findOne(['phone_number' => $phone_number]);
    }

    /**
     * Returns the promo codes associated with the user.
     *
     * @return \yii\db\ActiveQuery The active query for retrieving promo codes
     */
    public function getPromoCodes()
    {
        return $this->hasMany(Promo::className(), ['id' => 'promo_id'])
            ->viaTable('user_promo', ['user_id' => 'id']);
    }

    // Add other methods as needed
}
