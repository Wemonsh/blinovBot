<?php


namespace App\Http\Controllers\Api\Telegram\Dialogs;


class ApproveDialog extends Dialog
{
    protected $steps = ['startDialog', 'getRequests', 'approveRequest', 'endDialog'];

    protected function startDialog() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Список заявок:
        1. Семенов Олег Георгиевич
        2. Семенова Валерия Витальевна' ]);

        $this->get_next();
    }

    protected function getRequests() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Введите номер пользователя' ]);

        $this->get_next();
    }

    protected function approveRequest() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Напишите 1 для потверждения и 2 для отказа' ]);

        $this->get_next();
    }

    protected function endDialog() {
        $this->api->sendMessage([ 'chat_id' => $this->user->uid , 'text' => 'Спасибо большое пользователю отправлено приглашение' ]);

        $this->end();
    }

}
