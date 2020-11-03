<?php


namespace App\Http\Controllers\Api\Telegram\Services;

use App\Models\TelegramUser;

class TelegramUserService
{
    public function getUserById(array $value) : ?TelegramUser {
        $user = TelegramUser::withoutTrashed()->where('uid', $value['id'])->first();
        if ($user !== null) {
            return $user;
        } else {
            $user = new TelegramUser();
            if (isset($value['id']) && !empty($value['id'])) {
                $user->uid = $value['id'];
            }

            if (isset($value['first_name']) && !empty(['first_name'])) {
                $user->first_name = $value['first_name'];
            }

            if (isset($value['last_name']) && !empty(['last_name'])) {
                $user->first_name = $value['last_name'];
            }

            if (isset($value['username']) && !empty(['username'])) {
                $user->first_name = $value['username'];
            }
            $user->save();
            return $user;
        }
    }
}
