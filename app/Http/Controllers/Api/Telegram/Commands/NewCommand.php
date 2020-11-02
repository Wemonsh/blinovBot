<?php

namespace App\Http\Controllers\Api\Telegram\Commands;

use App\Http\Controllers\Api\Telegram\Dialogs\AzurDialog;
use App\Http\Controllers\Api\Telegram\Dialogs\ApproveDialog;
use App\Http\Controllers\Api\Telegram\Dialogs\RegisterDialog;
use App\Http\Controllers\Api\Telegram\Services\TelegramUserService;
use App\Models\Request;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

/**
 * Class HelpCommand.
 */
class NewCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'new';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['new-tickets'];

    /**
     * @var string Command Description
     */
    protected $description = 'Новый тикет';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $updates = $this->getUpdate()->toArray();
        $userService = new TelegramUserService();
        $user = $userService->getUserById($updates['message']['from']);

        //$dialog = new AzurDialog($this->telegram, $user, $updates);
        $dialog = new RegisterDialog($this->telegram, $user, $updates);
        $dialog->start();
        exit();
    }
}
