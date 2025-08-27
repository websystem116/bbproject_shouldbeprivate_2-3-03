<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParentUser extends Model
{
    protected $table = 'parent_users'; // テーブル名を指定
    protected $fillable = ['email', 'password', 'name'];

    public function students()
    {
        return $this->hasMany(Student::class, 'parent_user_id', 'id');
    }
}
