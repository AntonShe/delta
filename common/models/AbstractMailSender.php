<?php

namespace common\models;

use Yii;

class AbstractMailSender
{
    protected string $template = 'simple-html';

    public function sendErrorsMail(string $subject, string $errors): bool
    {
        return $this->send($subject, ['text' => $errors]);
    }

    public function sendMail(string $subject, string $data, string $mailTo): bool
    {
        return $this->send($subject, ['text' => $data], $mailTo);
    }

    private function send(string $subject, array $data, ?string $mailTo = null): bool
    {
        $mailTo = $mailTo === null ? Yii::$app->params['adminEmail'] : [$mailTo];

        foreach ($mailTo as $address) {
            $mail = Yii::$app->mailer->compose('simple-html',
                $data
            )
                ->setFrom(Yii::$app->params['senderEmail'])
                ->setTo($address)
                ->setSubject($subject);

            if (!$mail->send()) {
                return false;
            }
        }

        return true;
    }
}