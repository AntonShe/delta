<?php
namespace common\models\logger;

use common\models\AbstractMailSender;

class Logger
{
    const LOG_DIR = '/var/www/deltabook/current/logs/';

    private static $_instance = null;
    private string $fileName;
    private string $logFullDir;
    private $file;
    private AbstractMailSender $mailSender;

    private function __construct(){
        $this->mailSender = new AbstractMailSender();
    }
    private function __clone(){}

    public static function getInstance(): Logger
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function writeLog(string $fileName, string $message, bool $isNeedSend = false): bool
    {
        if  ($this->getFile($fileName)) {
            $message = time() . ": " . $message . "\n";
            $result = (bool)fwrite($this->file, $message);
            fclose($this->file);

            if ($isNeedSend) $this->mailSender->sendErrorsMail("Ошибка. Логи в $fileName" , $message);

            return $result;
        }

        return false;
    }

    private function getFile(string $fileName): bool
    {
        $this->fileName = $fileName;
        $this->logFullDir = self::LOG_DIR . $this->fileName;

        try {
            if (!is_dir(self::LOG_DIR) || !file_exists(self::LOG_DIR)) {
                mkdir(self::LOG_DIR);
            }

            $this->file = fopen($this->logFullDir, 'a+');

            return $this->file !== false;
        }catch (\Throwable $th) {
            return false;
        }
    }
}