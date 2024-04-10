<?php

namespace JDCustom;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class jdLog
{
    private $logger;

    public function __construct($logName = 'jd-log')
    {
        $dateFormat = 'Y-m-d h:m:s';
        $stream = new StreamHandler(WP_CONTENT_DIR.'/'.$logName.'.log', Level::Debug);
        $output = "%datetime% | %level_name% |  %message% \n";
        $stream->setFormatter(new LineFormatter($output, $dateFormat, true, true));
        $this->logger = new Logger($logName);
        $this->logger->pushHandler($stream);
    }

    public function logInfo($message)
    {
        $this->logger->info($message);
    }

    public function logWarning($message)
    {
        $this->logger->warning($message);
    }

    public function logError($message)
    {
        $this->logger->error($message);
    }

    public function logCritical($message)
    {
        $this->logger->critical($message);
    }
}
