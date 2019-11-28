<?php

/**
 * @author  advename
 * @since   October 27, 2019
 * @link    https://github.com/advename/Simple-PHP-Logger
 * @version 1.0.0
 * 
 * Description:
 * The simple php logger is a single-file logwriter with the features of:
 *  - following some PSR-3 standards
 *  - single file
 *  - singleton pattern
 *  - six log levels (info, notice, debug, warning, error, fatal)
 *  - logs the line of execution too (good for troubleshooting)
 *  - can be with or without OOP
 *  - no composer needed
 * 
 */

class Logger
{

    /**
     * $log_file - path and log file name
     * @var string
     */
    protected static $log_file;

    /**
     * $file - file
     * @var string
     */
    protected static $file;

    /**
     * $options - settable options
     * @var array $dateFormat of the format used for the log.txt file; $logFormat used for the time of a single log event
     */
    protected static $options = [
        'dateFormat' => 'd-M-Y',
        'logFormat' => 'H:i:s d-M-Y'
    ];

    private static $instance;

    /**
     * Create the log file
     * @param string $log_file - path and filename of log
     * @param array $params - settable options
     */
    public static function createLogFile()
    {
        $time = date(static::$options['dateFormat']);
        static::$log_file =  __DIR__ . "/logs/log-{$time}.txt";


        //Check if directory /logs exists
        if (!file_exists(__DIR__ . '/logs')) {
            mkdir(__DIR__ . '/logs', 0777, true);
        }

        //Create log file if it doesn't exist.
        if (!file_exists(static::$log_file)) {
            fopen(static::$log_file, 'w') or exit("Can't create {static::log_file}!");
        }

        //Check permissions of file.
        if (!is_writable(static::$log_file)) {
            //throw exception if not writable
            throw new Exception("ERROR: Unable to write to file!", 1);
        }
    }

    /**
     * Set logging options (optional)
     * @param array $options Array of settable options
     * 
     * Options:
     *  [
     *      'dateFormat' => 'value of the date format the .txt file should be saved int'
     *      'logFormat' => 'value of the date format each log event should be saved int'
     *  ]
     */
    public static function setOptions($options = [])
    {
        static::$options = array_merge(static::$options, $options);
    }

    /**
     * Info method (write info message)
     * 
     * Used for e.g.: "The user example123 has created a post".
     * 
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function info($message, array $context = [])
    {
        // grab the line where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $lineNumber = $caller['line'];

        //execute the write log
        static::writeLog([
            'message' => $message,
            'line' => $lineNumber,
            'severity' => 'INFO',
            'context' => $context
        ]);
    }

    /**
     * Notice method (write notice message)
     * 
     * Used for e.g.: "The user example123 has created a post".
     * 
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function notice($message, array $context = [])
    {
        // grab the line where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $lineNumber = $caller['line'];

        //execute the write log
        static::writeLog([
            'message' => $message,
            'line' => $lineNumber,
            'severity' => 'NOTICE',
            'context' => $context
        ]);
    }

    /**
     * Debug method (write debug message)
     * 
     * Used for debugging, could be used instead of echo'ing values
     * 
     * @param string $message Descriptive text of the debug
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function debug($message, array $context = [])
    {


        // grab the line where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $lineNumber = $caller['line'];

        //execute the write log
        static::writeLog([
            'message' => $message,
            'line' => $lineNumber,
            'severity' => 'DEBUG',
            'context' => $context
        ]);
    }

    /**
     * Warning method (write warning message)
     * 
     * Used for warnings which is not fatal to the current operation
     * 
     * @param string $message Descriptive text of the warning
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function warning($message, array $context = [])
    {

        // grab the line where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $lineNumber = $caller['line'];

        //execute the write log
        static::writeLog([
            'message' => $message,
            'line' => $lineNumber,
            'severity' => 'WARNING',
            'context' => $context
        ]);
    }

    /**
     * Error method (write error message)
     * 
     * Used for e.g. file not found,...
     * 
     * @param string $message Descriptive text of the error
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function error($message, array $context = [])
    {
        // grab the line where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $lineNumber = $caller['line'];

        //execute the write log
        static::writeLog([
            'message' => $message,
            'line' => $lineNumber,
            'severity' => 'ERROR',
            'context' => $context
        ]);
    }

    /**
     * Fatal method (write fatal message)
     * 
     * Used for e.g. database unavailable, system shutdown
     * 
     * @param string $message Descriptive text of the error
     * @param string $context Array to expend the message's meaning
     * @return void
     */
    public static function fatal($message, array $context = [])
    {
        // grab the line where the log method has been executed ( for troubleshooting )
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $lineNumber = $caller['line'];

        //execute the write log
        static::writeLog([
            'message' => $message,
            'line' => $lineNumber,
            'severity' => 'FATAL',
            'context' => $context
        ]);
    }

    /**
     * Write to log file
     * @param array $args Array of message (for log file), line (of log method execution), severity (for log file) and displayMessage (to display on frontend for the used)
     * @return void
     */
    // public function writeLog($message, $line = null, $displayMessage = null, $severity)
    public static  function writeLog($args = [])
    {
        //Create the log file
        static::createLogFile();

        // open log file
        if (!is_resource(static::$file)) {
            static::openLog();
        }

        // grab the url path
        $path = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

        //Grab time - based on the time format
        $time = date(static::$options['logFormat']);

        // Convert context to json
        $context = json_encode($args['context']);

        // Create log variable = value pairs
        $timeLog = is_null($time) ? "[N/A] " : "[{$time}] ";
        $pathLog = is_null($path) ? "[N/A] " : "[{$path}] ";
        $lineLog = is_null($args['line']) ? "[N/A] " : "[{$args['line']}] ";
        $severityLog = is_null($args['severity']) ? "[N/A]" : "[{$args['severity']}]";
        $messageLog = is_null($args['message']) ? "N/A" : "{$args['message']}";
        $contextLog = empty($args['context']) ? "" : "{$context}";

        // Write time, url, & message to end of file
        fwrite(static::$file, "{$timeLog}{$pathLog}{$lineLog}: {$severityLog}- {$messageLog} {$contextLog}" . PHP_EOL);

        // Close file stream
        static::closeFile();
    }

    /**
     * Open log file
     * @return void
     */
    private static function openLog()
    {
        $openFile = static::$log_file;
        // 'a' option = place pointer at end of file
        static::$file = fopen($openFile, 'a') or exit("Can't open $openFile!");
    }

    /**
     *  Close file stream
     */
    public static function closeFile()
    {
        if (static::$file) {
            fclose(static::$file);
        }
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct()
    { }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __destruct()
    { }
}