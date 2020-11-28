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
        $residents = Resident::with('telegramUser')->where('invited', 0)->whereNotNull('full_name')->get();
        foreach ($residents as $resident) {
            $text = '#'.$resident->id.
                PHP_EOL.
                '@'.$resident->telegramUser->first_name.
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
        if (count($residents) === 0) {
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Нет заявок в данный момент' ]);
            exit();
        }
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Укажите номер пользователя которому необходимо отправить приглашение' ]);

        $this->user->step = 1;
        $this->user->save();

        $this->get_next();
    }

    protected function approveRequest() {

        $resident = Resident::withoutTrashed()->where('id', $this->message['message']['text'])->first();
        $user = TelegramUser::withoutTrashed()->where('id', $resident->user_id)->first();

        $user->invite = null;
        $user->save();

        $resident->invited = 1;
        $resident->save();

        $keyboard = array(
            array(
                array('text'=>'Вступить в группу','url'=>'https://t-blinov.wemonsh.ru/t/'.$user->uid)
            )
        );

        $this->api->sendMessage([ 'chat_id' => $user->uid , 'text' => 'Для принятия приглашения в закрытую группу ЖК Сердце Столицы нажмите ниже на кнопку "вступить в группу".',
            'disable_web_page_preview' => false,
            'reply_markup' => json_encode(array('inline_keyboard' => $keyboard))]);

        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Спасибо большое пользователю отправлено приглашение' ]);
        $this->end();
        //$this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Напишите 1 для потверждения и 2 для отказа' ]);
    }


}
