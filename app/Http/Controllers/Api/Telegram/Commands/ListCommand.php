<?php

namespace App\Http\Controllers\Api\Telegram\Commands;

use App\Http\Controllers\Api\Telegram\Dialogs\AzurDialog;
use App\Http\Controllers\Api\Telegram\Dialogs\ApproveDialog;
use App\Http\Controllers\Api\Telegram\Services\TelegramUserService;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\Command;

/**
 * Class HelpCommand.
 */
class ListCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'list';

    /**
     * @var array Command Aliases
     */
    protected $aliases = ['get-list-requests'];

    /**
     * @var string Command Description
     */
    protected $description = 'Получить список заявок';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $updates = $this->getUpdate()->toArray();
        $userService = new TelegramUserService();
        $user = $userService->getUserById($updates['message']['from']);

        $user->step = null;
        $user->dialog = null;
        $user->save();

        if ($user->is_admin == 1) {
            //$dialog = new AzurDialog($this->telegram, $user, $updates);
            $dialog = new ApproveDialog($this->telegram, $user, $updates);
            $dialog->start();
        } else {
            $telegram->sendMessage(
                [
                    'chat_id' => $user->uid ,
                    'text' => 'У вас нет доступа для выполнения данной команды.'
                ]
            );
        }

        exit();
    }
}
