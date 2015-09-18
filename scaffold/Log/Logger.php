<?php

namespace Scaffold\Log;

/**
 * This is a simple Logger implementation that other Loggers can inherit from.
 *
 * It simply delegates all log-level-specific methods to the `log` method to
 * reduce boilerplate code that a simple Logger that does the same thing with
 * messages regardless of the error level has to implement.
 */
class Logger
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
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
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
