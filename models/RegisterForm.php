<?php
namespace app\models;

use Yii;
use yii\base\Model;

class RegisterForm extends Model
{
    public $username;
    public $password;
    public $name;
    public $role;

    public function rules()
    {
        return [
            [['username', 'password', 'name', 'role'], 'required'],
            ['username', 'unique', 'targetClass' => Account::class, 'message' => 'Username sudah dipakai.'],
        ];
    }

    public function register()
    {
        if ($this->validate()) {
            $account = new Account();
            $account->username = $this->username;
            $account->password = $this->password; // akan di-hash di beforeSave
            $account->name = $this->name;
            $account->role = $this->role;

            if ($account->save()) {
                return $account;
            }
        }
        return null;
    }
}
