<?php

namespace App\Http\Controllers\Api\Telegram;

use App\Http\Controllers\Api\Telegram\Dialogs\AzurDialog;
use App\Http\Controllers\Api\Telegram\Dialogs\ApproveDialog;
use App\Http\Controllers\Api\Telegram\Services\TelegramUserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;

class ApiTelegramController extends Controller
{
    private $telegram;
    private $telegramUpdates;
    private $telegramUser;
    private $telegramUserService;

    public function __construct()
    {
        $this->telegram = new Api('1366419508:AAE5tLZEuwzuVTU4CSH67_jD43bOaOVP7kE');
        $this->telegramUserService = new TelegramUserService();
        $this->telegramUpdates = Telegram::commandsHandler(true)->toArray();
        $this->telegramUser = $this->telegramUserService->getUserById($this->telegramUpdates['message']['from']);
    }

    public function webhook()
    {
        Log::info('updates',$this->telegramUpdates);

        //$dialog = new AzurDialog($this->telegram, $this->telegramUser, $this->telegramUpdates);
        $dialog = new ApproveDialog($this->telegram, $this->telegramUser, $this->telegramUpdates);

        $dialog->process();


    }
}
