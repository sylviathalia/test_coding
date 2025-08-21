<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Post extends ActiveRecord
{
    public static function tableName()
    {
        return 'post';
    }

    public function getAccount()
    {
        return $this->hasOne(Account::class, ['username' => 'username']);
    }
}
