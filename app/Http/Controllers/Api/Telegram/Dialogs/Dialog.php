<?php


namespace App\Http\Controllers\Api\Telegram\Dialogs;


use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class Dialog
{
    protected $dialog;
    protected $steps = [];
    protected $current = null;

    protected $user;
    protected $api;
    protected $message;

    public function __construct(Api $api, TelegramUser $user, array $message)
    {
        $this->api = $api;
        $this->user = $user;
        $this->message = $message;

        $this->current = $this->user->step;
    }

    public function init() {

    }

    public function start() {
        $this->user->step = 0;
        $this->user->save();
        $step = $this->steps[0];
        $this->init();
        $this->$step();
    }

    public function process() {
        if ($this->current === null) {
            //$this->start();
        } else {
            $this->init();
            $step = $this->steps[$this->current];
            $this->$step();
        }
    }

    public function set_dialog() {
        $this->user->dialog = $this->dialog;
    }

    public function get_dialog() {

    }

    public function set_current($value) {
        Log::info($value);
        $this->user->step = $value;
        $this->user->save();
        $this->current = $value;
    }

    public function get_current() {
        Log::info($this->current);
        return $this->current;
    }

    public function next() {

    }

    public function get_next() {
        $current = $this->get_current();

        if ($current < count($this->steps)) {
            $this->set_current($current + 1);
            return true;
        }

        return false;
    }

    public function end() {
        $this->user->step = null;
        $this->user->dialog = null;
        $this->user->save();
    }
}
