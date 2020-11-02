<?php


namespace App\Http\Controllers\Api\Telegram\Dialogs;


class RegisterDialog extends Dialog
{
    protected $steps = ['startDialog', 'getName', 'getEmail', 'getPhone', 'getApartment', 'getParking', 'endDialog'];

    protected function startDialog() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваше ФИО' ]);

        $this->get_next();
    }

    protected function getName() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш Email' ]);

        $this->get_next();
    }

    protected function getEmail() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер телефона' ]);

        $this->get_next();
    }

    protected function getPhone() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер квартиры. Если у вас несколько квартир введите пожалуйста их через запятую.' ]);

        $this->get_next();
    }

    protected function getApartment() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите пожалуйста ваш номер парковочного места. Если у вас несколько парковочных мест введите пожалуйста их через запятую.' ]);

        $this->get_next();
    }

    protected function getParking() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Спасибо большое, ожидайте подтверждение заявки от модератора. Как только модератор одобрит вашу заявку вам будет отправлено приглашение в приватную группу.' ]);
        $this->end();
    }

}
