<?php

namespace app\models;

use Yii;
use yii\web\UploadedModel;

/**
 * This is the model class for table "user_screening".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email_id
 * @property string $phone_number
 * @property string $cv
 * @property integer $status
 */
class UserScreening extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_screening';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email_id', 'phone_number', 'status', 'cv'], 'required'],
            [['status'], 'integer'],
            [['first_name', 'last_name', 'email_id', 'cv'], 'string', 'max' => 256],
            [['phone_number'], 'number'],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'docx, doc, pdf'],
            [['email_id'], 'unique'],
            [['phone_number'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email_id' => 'Email ID',
            'phone_number' => 'Phone Number',
            'cv' => 'Cv',
            'file' => 'Upload CV',
            'status' => 'Status',
        ];
    }
}
