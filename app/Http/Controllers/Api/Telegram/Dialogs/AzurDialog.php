<?php


namespace App\Http\Controllers\Api\Telegram\Dialogs;


class AzurDialog extends Dialog
{
    protected $steps = ['greeting', 'message', 'photo', 'number'];

    public function greeting() {
        $this->api->sendMessage(
            [
                'chat_id' => $this->user->uid ,
                'text' => 'Здраствуйте, Вы открыли заявку о проблеме с терминалом оплаты Azur. Для того что бы мы могли Вам помочь нам необходима следующая информация: описание проблемы, фото терминала, государственный номер автобуса/троллейбуса. Данная информация будет запрашиваться ботом поочередно.'
            ]
        );

        $this->api->sendMessage(
            [
                'chat_id' => $this->user->uid ,
                'text' => 'Опишите пожалуйста кратко в чем проблема.'
            ]
        );

        $this->get_next();
    }

    public function message() {

        $this->api->sendMessage(
            [
                'chat_id' => $this->user->uid ,
                'text' => 'Отправьте пожалуйста фото терминала Azur.'
            ]
        );

        $this->get_next();
    }

    public function photo() {
        $this->api->sendMessage(
            [
                'chat_id' => $this->user->uid ,
                'text' => 'Напишите пожалуйста гос номер автобуса.'
            ]
        );

        $this->get_next();
    }

    public function number() {
        $this->api->sendMessage(
            [
                'chat_id' => $this->user->uid ,
                'text' => 'Спасибо большое за информацию.'
            ]
        );

        $this->end();
    }


}
