<?php


namespace App\Http\Controllers\Api\Telegram\Dialogs;


use App\Models\Resident;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;

class ApproveDialog extends Dialog
{
    protected $steps = ['startDialog', 'approveRequest'];

    public function init() {
        $this->user->dialog = __CLASS__;
        $this->user->save();
    }

    protected function startDialog() {

        $residents = Resident::withoutTrashed()->where('invited', 0)->get();
        foreach ($residents as $resident) {
            $text = '#'.$resident->id.
                PHP_EOL.
                '😊 '.$resident->full_name.
                PHP_EOL.
                '📧 '.$resident->email.
                PHP_EOL.
                '☎ ️'.$resident->phone.
                PHP_EOL.
                '🏠 '.$resident->apartment_numbers.
                PHP_EOL.
                '🚘 '.$resident->parking_numbers;
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => $text ]);
        }

        $this->get_next();
    }

    protected function approveRequest() {

        $resident = Resident::withoutTrashed()->where('id', $this->message['message']['text'])->first();
        $user = TelegramUser::withoutTrashed()->where('id', $resident->user_id)->first();

        $this->api->sendMessage([ 'chat_id' => $user->uid , 'text' => 'https://t.me/joinchat/CgQ3Wh0qdmwPfeNU3MHopg' ]);

        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Спасибо большое пользователю отправлено приглашение' ]);
        $this->end();
        //$this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Напишите 1 для потверждения и 2 для отказа' ]);
    }


}
