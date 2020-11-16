<?php

namespace App\Http\Controllers;

use App\Models\TelegramUser;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function redirect(Request $request) {
        $user = TelegramUser::where('uid', $request->uid)->first();
        if ($user->invite === null) {
            $user->invite = 1;
            $user->save();
            //return redirect()->to('https://t.me/joinchat/CgQ3Wh0qdmwPfeNU3MHopg');
            return redirect()->to('https://t.me/joinchat/Ga6OUUWn7gDJPg0LXx_NYA');
        } else {
            echo 'Приглашение уже было использовано!';
        }
    }
}
