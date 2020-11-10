<?php


namespace App\Http\Controllers\Api\Telegram\Dialogs;


use App\Models\Resident;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

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

            $keyboard = [
                [
                    ['text' => '📞 отправить мой номер телефона', 'request_contact' => true]
                ]
            ];

            $reply_markup = Keyboard::make([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false
            ]);

            $this->api->sendMessage([
                'chat_id' => $this->user->uid,
                'text' => 'Введите пожалуйста ваш номер телефона',
                'reply_markup' => $reply_markup
            ]);

            //$this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер телефона' ]);
            $this->get_next();
        } else {
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Ошибка, Email не соответсвует формату, попробуйте еще раз' ]);
        }
    }

    protected function getPhone() {
        $re = '/^(\+)?(\(\d{2,3}\) ?\d|\d)(([ \-]?\d)|( ?\(\d{2,3}\) ?)){5,12}\d$/';

        $keyboard = [
            ['Нет квартиры']
        ];

        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ]);

        if (isset($this->message['message']['text'])) {
            if (preg_match($re, $this->message['message']['text'])) {
                $this->resident->phone = $this->message['message']['text'];
                $this->resident->save();

                $this->api->sendMessage([
                    'chat_id' => $this->user->uid,
                    'text' => 'Введите пожалуйста ваш номер квартиры. Если у вас несколько квартир введите пожалуйста их через запятую',
                    'reply_markup' => $reply_markup
                ]);

                $this->get_next();
            } else {
                $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Ошибка, телефонный номер не соответсвует формату, попробуйте еще раз' ]);
            }
        } else if (isset($this->message['message']['contact']['phone_number'])) {
            if (preg_match($re, $this->message['message']['contact']['phone_number'])) {
                $this->resident->phone = $this->message['message']['contact']['phone_number'];
                $this->resident->save();
                $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер квартиры. Если у вас несколько квартир введите пожалуйста их через запятую.',
                    'reply_markup' => $reply_markup ]);
                $this->get_next();
            } else {
                $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Ошибка, телефонный номер не соответсвует формату, попробуйте еще раз' ]);
            }
        } else {
            $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Ошибка, телефонный номер не соответсвует формату, попробуйте еще раз' ]);
        }
    }

    protected function getApartment() {
        $this->resident->apartment_numbers = $this->message['message']['text'];
        $this->resident->save();

        $keyboard = [
            ['Нет парковочного места']
        ];

        $reply_markup = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ]);

        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер парковочного места. Если у вас несколько парковочных мест введите пожалуйста их через запятую.','reply_markup' => $reply_markup ]);
        $this->get_next();
    }

    protected function getParking() {
        $this->resident->parking_numbers = $this->message['message']['text'];
        $this->resident->save();

        $reply_markup = Keyboard::make([
            'remove_keyboard' => true
        ]);

        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Спасибо большое, ожидайте подтверждение заявки от модератора. Как только модератор одобрит вашу заявку вам будет отправлено приглашение в приватную группу.',
            'reply_markup' => $reply_markup ]);
        $this->end();
    }



}
