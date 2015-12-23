<?php

namespace Scaffold\Log;

use \Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * This is a simple Logger implementation that other Loggers can inherit from.
 *
 * It simply delegates all log-level-specific methods to the `log` method to
 * reduce boilerplate code that a simple Logger that does the same thing with
 * messages regardless of the error level has to implement.
 */
class Logger extends AbstractLogger
{
    /**
    *  @var string
    */
    protected $filename;

    /**
    *  @var resource
    */
    protected $handle=null;

    /**
    *  @var string
    */
    protected $level=LogLevel::DEBUG;

    /**
    *  @var int   1G
    */
    protected static $logMaxSize = 1073741824;


    public function __destruct()
    {
        if( $this->handle!=null )
        {
            fclose($this->handle);
        }
    }

    /**
    *  create file logger
     * @param string $filename
     * @return Logger
    */
    public static function createFileLogger($filename)
    {
        $logger=new Logger();
        $logger->filename=$filename;
        $logger->backupLoggerFile();
        $logger->handle=fopen($filename, 'a+');
        return $logger;
    }

    /**
    *  backup logger file.
    */
    private function backupLoggerFile()
    {
        if ( file_exists($this->filename) ) {
            $size=filesize($this->filename);
            if( $size > static::$logMaxSize ) {
                $i=1;
                while(true) {
                    $bakFileName="{$this->filename}_$i";
                    if( !file_exists($bakFileName) ) {
                        rename($this->filename, $bakFileName);
                        break;
                    }
                    $i++;
                }
            }
        }
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $log= date('Y-m-d H:i:s') . " $level " . $message . json_encode($context) . '\n';
        fwrite($this->handle, $log);
    }

}
