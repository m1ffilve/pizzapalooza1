<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class Application extends ActiveRecord
{
    public static function tableName()
    {
        return 'Applications';
    }

    public function rules()
    {
        return [
            [['full_name', 'email', 'phone', 'resume_path'], 'required'],
            [['phone'], 'match', 'pattern' => '/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/i', 'message' => 'Неверный формат.'],
            [['full_name'], 'match', 'pattern' => '/^[А-Яа-яЁё\s]+$/u', 'message' => 'Неверное имя.'],
            [['email'], 'email', 'message' => 'Неверный формат почты.'],
            [['resume_path'], 'string', 'max' => 255],
            [['resume'], 'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'full_name' => 'ФИО',
            'email' => 'Email',
            'phone' => 'Телефон',
            'resume' => 'Резюме',
            'resume_path' => 'Путь к резюме',
        ];
    }

    public function uploadResume(UploadedFile $file)
    {
        if ($file !== null) {
            // Генерируем уникальное имя файла
            $fileName = Yii::$app->security->generateRandomString(12) . '.' . $file->extension;

            // Путь для сохранения файла
            $path = Yii::getAlias('@webroot/uploads/') . $fileName;
            // Сохраняем файл на сервере
            if ($file->saveAs($path)) {
                $this->resume_path = $path;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
