<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resident extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function telegramUser()
    {
        return $this->hasOne(TelegramUser::class, 'id', 'user_id');
    }
}
