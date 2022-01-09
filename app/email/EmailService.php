<?php

declare(strict_types=1);

namespace app\email;

use app\common\Log;

class EmailService {
    /**
     * Checks if email is valid.
     * Each call is a paid operation and takes from 1 second to 1 minute.
     *
     * @param string $email
     * @return int  Returns 1 if email is valid, 0 otherwise
     */
    public static function check_email(string $email): int {
        $result = 0;
        if (\filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // This is a stub: imitate real check
            $valid = rand(0, 999) < 800;
            [$minSleep, $maxSleep] = [1.0, 60.0];
            $microseconds = rand((int)($minSleep * 1e6), (int)($maxSleep * 1e6));
            usleep($microseconds);
            $result = $valid ? 1 : 0;
        }
        return $result;
    }

    /**
     * Sends email.
     * Each call takes from 1 second to 10 seconds.
     *
     * @param string $email
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $body
     */
    public static function send_email(string $email, string $from, string $to, string $subject, string $body): void {
        // This is a stub: imitate real sending
        [$minSleep, $maxSleep] = [1.0, 10.0];
        $microseconds = rand((int)($minSleep * 1e6), (int)($maxSleep * 1e6));
        usleep($microseconds);
        Log::debug(sprintf('sent email to %s', $email));
        return;
    }
}
