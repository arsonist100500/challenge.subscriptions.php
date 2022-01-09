<?php

declare(strict_types=1);

namespace app\workers;

use app\common\Config;
use app\email\EmailService;

/**
 * Class ExpirationEmailWorker
 * @package app\workers
 */
class ExpirationEmailWorker extends AbstractWorker {
    /**
     * @return array
     * @throws \Exception
     */
    public function processTask(): array {
        $email = $this->task->email;
        $input = $this->task->input;
        $username = $input['username'] ?? 'user';
        $expirationDatetime = (new \DateTime($input['expiration'] ?? 'now + 3 days'))->format('Y-m-d');
        $subject = "$username, your subscription is expiring soon";
        $body = "Dear $username, your subscription is expiring on $expirationDatetime";
        $from = (string)Config::get('env.email.from');
        EmailService::send_email($email, $from, $email, $subject, $body);
        return ['result' => 'ok'];
    }
}
