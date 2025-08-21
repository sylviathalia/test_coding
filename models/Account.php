<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Account extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'account';
    }

    // Implement IdentityInterface
    public static function findIdentity($username)
    {
        return static::findOne(['username' => $username]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // tidak pakai token
    }

    public function getId()
    {
        return $this->username; // PK = username
    }

    public function getAuthKey()
    {
        return null; // karena tidak ada kolom auth_key
    }

    public function validateAuthKey($authKey)
    {
        return false; // disable authKey
    }

    // cari user by username
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    // validasi password
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    // sebelum simpan hash password
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->password) && substr($this->password, 0, 4) !== '$2y$') {
                // hanya hash jika belum di-hash
                $this->password = Yii::$app->security->generatePasswordHash($this->password);
            }
            return true;
        }
        return false;
    }

    // relasi ke post
    public function getPosts()
    {
        return $this->hasMany(Post::class, ['username' => 'username']);
    }
}
