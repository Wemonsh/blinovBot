<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Controller;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramApiController extends Controller
{
//
//
//        $response = Telegram::getMe();
//
//        $botId = $response->getId();
//        $firstName = $response->getFirstName();
//        $username = $response->getUsername();
    public function webhook(Request $request) {

        $telegram = new Api('1366419508:AAE5tLZEuwzuVTU4CSH67_jD43bOaOVP7kE');

        $updates = Telegram::commandsHandler(true);
        $data = $updates->toArray();
        Log::info('updates',$updates->toArray());

        $user = TelegramUser::withoutTrashed()
            ->where('uid', $data['message']['from']['id'])
            ->first();
        if ($user === null) {
            $user = new TelegramUser();
            $user->uid = $data['message']['from']['id'];
            $user->first_name = $data['message']['from']['first_name'];
            $user->last_name = $data['message']['from']['last_name'];
            $user->username = $data['message']['from']['username'];
            $user->save();
        }

        $request = \App\Models\Request::withoutTrashed()->where('user_id', $user->id)->where('step_id', '!=', 5)->first();
        if ($user != null && $request != null) {
            switch ($request->step_id) {
                case 1:

                    if (false) {
                        //$telegram->sendMessage([ 'chat_id' => $user->uid , 'text' => 'Опишите проблему' ]);
                    } else {
                        if($data['message']['text'] != null && $data['message']['text'] != '/new') {
                            $request->message = $data['message']['text'];
                            $request->step_id = 2;
                            $telegram->sendMessage([ 'chat_id' => $user->uid , 'text' => 'Отправьте фото' ]);
                        }
                    }
                    $request->save();
                    break;
                case 2:
                    if ($request->photo != null) {
                        $request->step_id =3;
                    }else {
                        if (isset($data['message']['photo'])) {
                        $request->photo = json_encode($data['message']['photo'],true);
                        $request->step_id =3;
                            $telegram->sendMessage([ 'chat_id' => $user->uid , 'text' => 'Напишите гос номер' ]);
                        }
                    }

                    $request->save();
                    break;
                case 3:

                    if ($request->number != null) {

                        $request->step_id =4;
                    }else {
                        $request->number = $data['message']['text'];
                        $request->step_id = 4;
                        //$telegram->sendMessage([ 'chat_id' => $user->uid , 'text' => 'Сохранить?' ]);

                        $keyboard = [
                            [
                                ['text' => 'test',
                                    'request_contact' => true]
                            ]
                        ];

                        $reply_markup = Keyboard::make([
                            'keyboard' => $keyboard,
                            'resize_keyboard' => true,
                            'one_time_keyboard' => true
                        ]);

                        $response = $telegram->sendMessage([
                            'chat_id' => $user->uid,
                            'text' => 'Сохранить?',
                            'reply_markup' => $reply_markup
                        ]);

                    }
                    $request->save();
                    break;
                case 4:
                    if ($data['message']['text'] == '👍') {
                        $request->step_id =5;
                        $request->save();
                        $telegram->sendMessage([ 'chat_id' => $user->uid , 'text' => 'Спасибо заявка создана' ]);
                    } else {
                        $request->step_id =5;
                        $request->save();
                        $telegram->sendMessage([ 'chat_id' => $user->uid , 'text' => 'Заявка отменена' ]);
                    }

                    break;

            }
        }






//        if ($request->isMethod('post')) {
//
//
//
//
//
//
//
//        } else {
//
//            $result = $telegram->setWebhook(['url' => 'https://e20e7cbaa972.ngrok.io/api/telegram']);
//
//            dd($result);
//        }

    }

    public function getfile() {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));



        $result = $telegram->setWebhook(['url' => env('TELEGRAM_BOT_LINK')]);
        //$result = $telegram->setWebhook(['url' => 'https://blinov.wemonsh.ru/api/telegram']);
//
  dd($result);
//        try {
//            $file = $telegram->getFile(['file_id' => 'AgACAgIAAxkBAANFX3o8814hhsyEJVzJ1Nx1r3aMFOYAAiWwMRubBdBLLjqhMnGmojM0tHeXLgADAQADAgADeAADf2QBAAEbBA']);
//            dump($file->getFilePath());
//
//        } catch (TelegramSDKException $e) {
//            dd($e->getMessage());
//        }
//        dd($file);
    }
}
