<?php

namespace Renatoaraujo;

use Monolog\Formatter\NormalizerFormatter;
use Monolog\Formatter\ScalarFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;

/**
 * Class to debug applications on screen or logging outputs
 * This class create the basic interaction for debug with many options.
 *
 * @category Library
 *
 * @package Renatoaraujo
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @uses \Monolog\Logger
 * @uses \Monolog\Handler\StreamHandler
 *
 * @version Release: 1.0
 *
 * @link https://github.com/renatoaraujo/debug
 */
class Debug
{

    /**
     * Log path for saving logs
     * Change the log path to another path inside your application as the example below
     * @example Debug::$strLogPath = 'path/to/my/logs';
     * @var string logPath
     */
    public static $strLogPath = (__DIR__) . '/../logs/debug.log';

    /**
     * Log identifier name
     * Change the name of your log as the example below
     * @example Debug::$strLogName = 'NameOfMyLog';
     * @var string logName
     */
    public static $strLogName = 'Renatoaraujo\Debug';

    /**
     * Log date time format
     * Change the date format for your logging as the example below
     * @example Debug::$strLogDateTime = 'd/m/Y His';
     * @var string logDateTime
     */
    public static $strLogDateTime = 'Y-m-d H:i:s';

    /**
     * Dump method to debug using var_dump displaying as html on screen.
     * You can send unlimited parameters from any type to this method and will be listed in screen as arguments
     * To end the application after debug send the string 'exit' or 'die' as the param. See example below
     * @example Debug::dump($strArgument1, $intArgument2, $arrArgument3, 'exit');
     * @return string
     */
    public static function dump()
    {
        $backtrace = debug_backtrace();
        $arrPath = array_shift($backtrace);
        $strDisplay = "{$arrPath['file']} on line {$arrPath['line']}";
        $booEndApplication = false;

        if (is_array($arrPath['args']) && !empty($arrPath['args'])) {
            foreach ($arrPath['args'] as $intKey => $mixValue) {
                if ($mixValue === 'exit') {
                    $booEndApplication = true;
                }
                ob_start();
                var_dump($mixValue);
                $strDisplay .= '<br/><br/><b style="color:red;">Argument ' . ($intKey + 1) . '</b><br />';
                $strDisplay .= '<pre>' . ob_get_contents() . '</pre>';
                ob_end_clean();
            }
        }

        if ($booEndApplication) {
            exit($strDisplay);
        }

        echo $strDisplay;
        return $strDisplay;
    }

    /**
     * Dump method to debug using print_r displaying as html on screen.
     * You can send unlimited parameters from any type to this method and will be listed in screen as arguments
     * To end the application after debug send the string 'exit' or 'die' as the param. See example below
     * @example Debug::dump($strArgument1, $intArgument2, $arrArgument3, 'exit');
     * @return string
     */
    public static function printr()
    {
        $backtrace = debug_backtrace();
        $arrPath = array_shift($backtrace);
        $strDisplay = "{$arrPath['file']} on line {$arrPath['line']}";
        $booEndApplication = false;

        if (is_array($arrPath['args']) && !empty($arrPath['args'])) {
            foreach ($arrPath['args'] as $intKey => $mixValue) {
                if ($mixValue === 'exit') {
                    $booEndApplication = true;
                }
                ob_start();
                print_r($mixValue);
                $strDisplay .= '<br/><br/><b style="color:red;">Argument ' . ($intKey + 1) . '</b><br />';
                $strDisplay .= '<pre>' . ob_get_contents() . '</pre>';
                ob_end_clean();
            }
        }

        if ($booEndApplication) {
            exit($strDisplay);
        }

        echo $strDisplay;
        return $strDisplay;
    }

    /**
     * Dump method to debug displaying on screen.
     * Same as Debug::dump() and Debug::printr() but return JSON string.
     *
     * @return string
     */
    public static function json()
    {
        $backtrace = debug_backtrace();
        $path = array_shift($backtrace);

        $arr_json = array();
        $arr_json['file'] = "{$path['file']} on line {$path['line']} ";
        $arr_json['arguments'] = array();

        if (is_array($path['args']) && !empty($path['args'])) {
            foreach ($path['args'] as $key => $value) {
                array_push(
                    $arr_json['arguments'],
                    array(
                        "Argument " . ($key + 1) => $value
                    )
                );
            }
        }

        return json_encode($arr_json, JSON_PRETTY_PRINT);
    }

    /**
     * Method to debug displaying on log.
     *
     * @return bool
     */
    public static function log()
    {
        $backtrace = debug_backtrace();
        $path = array_shift($backtrace);
        $display = "{$path['file']} on line {$path['line']}";
        $arrContext = [];

        if (is_array($path['args']) && !empty($path['args'])) {
            foreach ($path['args'] as $key => $value) {
                $arrContext[$key + 1] = $value;
            }
        }

        $log = new Logger(self::$strLogName);
        # add some content to log the content
        $introspectionProcessor = new IntrospectionProcessor();
        $log->pushProcessor($introspectionProcessor);

        $streamHandler = new StreamHandler(self::$strLogPath, Logger::DEBUG);
        $streamHandler->setFormatter(
            new NormalizerFormatter(self::$strLogDateTime)
        );

        $log->pushHandler($streamHandler);

        return $log->debug($display, $arrContext);
    }

    /**
     * Method to debug displaying on console/terminal.
     *
     * @return bool
     */
    public static function console()
    {
        $backtrace = debug_backtrace();
        $path = array_shift($backtrace);
        $display = "{$path['file']} on line {$path['line']} \n";

        if (is_array($path['args']) && !empty($path['args'])) {
            foreach ($path['args'] as $key => $value) {
                ob_start();
                var_dump($value);
                $display .= "Argument " . ($key + 1) . "\n" . ob_get_contents();
                ob_end_clean();
            }
        }

        $stdout = fopen("php://stdout", "w");
        fwrite($stdout, $display);
        return fclose($stdout);
    }
}
