<?php

namespace Renatoaraujo;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class to debug applications on screen or logging outputs
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
     *
     * @var string logPath
     */
    protected static $logPath = __DIR__ . '/../logs/debug.log';

    /**
     * Log identifier name
     *
     * @var string logName
     */
    protected static $logName = 'Renatoaraujo\Debug';

    /**
     * Dump method to debug displaying on screen.
     *
     * @return string
     */
    public static function dump()
    {
        $path = array_shift(debug_backtrace());
        $display = "{$path['file']} on line {$path['line']}";

        if (is_array($path['args']) && !empty($path['args'])) {
            foreach ($path['args'] as $key => $value) {
                ob_start();
                var_dump($value);
                $display .= '<br/><br/><b style="color:red;">Argument ' . ($key + 1) . '</b><br />';
                $display .= '<pre>' . ob_get_contents() . '</pre>';
                ob_end_clean();
            }
        }

        return $display;
    }

    /**
     * Dump method to debug displaying on screen.
     * Same as Debug::dump() but with print_r method.
     *
     * @return string
     */
    public static function printr()
    {
        $path = array_shift(debug_backtrace());
        $display = "{$path['file']} on line {$path['line']} ";

        if (is_array($path['args']) && !empty($path['args'])) {
            foreach ($path['args'] as $key => $value) {
                ob_start();
                print_r($value);
                $display .= '<br/><br/><b style="color:red;">Argument ' . ($key + 1) . '</b><br />';
                $display .= '<pre>' . ob_get_contents() . '</pre>';
                ob_end_clean();
            }
        }

        return $display;
    }

    /**
     * Dump method to debug displaying on screen.
     * Same as Debug::dump() and Debug::printr() but return JSON string.
     *
     * @return string
     */
    public static function json()
    {
        $path = array_shift(debug_backtrace());

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
        $path = array_shift(debug_backtrace());
        $display = "{$path['file']} on line {$path['line']} - ";

        if (is_array($path['args']) && !empty($path['args'])) {
            foreach ($path['args'] as $key => $value) {
                ob_start();
                var_dump($value);
                $display .= "Argument " . ($key + 1) . ": " . ob_get_contents();
                ob_end_clean();
            }
        }

        $log = new Logger(self::$logName);
        $log->pushHandler(new StreamHandler(self::$logPath));
        return $log->debug($display);
    }

    /**
     * Method to debug displaying on console/terminal.
     *
     * @return bool
     */
    public static function console()
    {
        $path = array_shift(debug_backtrace());
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
