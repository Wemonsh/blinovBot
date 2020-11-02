<?php


namespace App\Http\Controllers\Api\Telegram\Dialogs;


use App\Models\Resident;
use Illuminate\Support\Facades\Log;

class RegisterDialog extends Dialog
{
    protected $steps = ['startDialog', 'getName', 'getEmail', 'getPhone', 'getApartment', 'getParking', 'endDialog'];
    protected $resident = null;

    public function init() {
        $this->user->dialog = __CLASS__;
        $this->user->save();
        $resident = Resident::withoutTrashed()->where('user_id', $this->user->id)->first();
        if ($resident === null) {
            $resident = new Resident();
            $resident->user_id = $this->user->id;
            $resident->save();
        }
        $this->resident = $resident;
    }

    protected function startDialog() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваше ФИО' ]);

        $this->get_next();
    }

    protected function getName() {
        $re = '/^[А-ЯЁ][а-яё]*([-][А-ЯЁ][а-яё]*)?\s[А-ЯЁ][а-яё]*\s[А-ЯЁ][а-яё]*$/mu';
        if (preg_match($re, $this->message['message']['text'])) {
            $this->resident->full_name = $this->message['message']['text'];
            $this->resident->save();
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш Email' ]);
            $this->get_next();
        } else {
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Ошибка, ФИО не соответсвует формату, попробуйте еще раз' ]);
        }
    }

    protected function getEmail() {
        $re = '/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u';
        if (preg_match($re, $this->message['message']['text'])) {
            $this->resident->email = $this->message['message']['text'];
            $this->resident->save();
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер телефона' ]);
            $this->get_next();
        } else {
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Ошибка, Email не соответсвует формату, попробуйте еще раз' ]);
        }
    }

    protected function getPhone() {
        $re = '/^(\+)?(\(\d{2,3}\) ?\d|\d)(([ \-]?\d)|( ?\(\d{2,3}\) ?)){5,12}\d$/';
        if (preg_match($re, $this->message['message']['text'])) {
            $this->resident->phone = $this->message['message']['text'];
            $this->resident->save();
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер квартиры. Если у вас несколько квартир введите пожалуйста их через запятую.' ]);
            $this->get_next();
        } else {
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Ошибка, телефонный номер не соответсвует формату, попробуйте еще раз' ]);
        }
    }

    protected function getApartment() {
        $this->resident->apartment_numbers = $this->message['message']['text'];
        $this->resident->save();
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер парковочного места. Если у вас несколько парковочных мест введите пожалуйста их через запятую.' ]);
        $this->get_next();
    }

    protected function getParking() {
        $this->resident->parking_numbers = $this->message['message']['text'];
        $this->resident->save();
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Спасибо большое, ожидайте подтверждение заявки от модератора. Как только модератор одобрит вашу заявку вам будет отправлено приглашение в приватную группу.' ]);
        $this->end();
    }



}
