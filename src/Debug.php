<?php

namespace Renatoaraujo;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\MemoryPeakUsageProcessor;
use Monolog\Processor\WebProcessor;

/**
 * Class to debug applications on screen or logging outputs
 * This class create the basic interaction for debug with many options.
 *
 * @category Library
 * @package Renatoaraujo
 *
 * @author Renato Rodrigues de Araujo <renato.r.araujo@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @uses \Monolog\Logger
 * @uses \Monolog\Handler\StreamHandler
 *
 * @version 1.1.0
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
    public static $strLogName = __CLASS__;

    /**
     * Log date time format
     * Change the date format for your logging as the example below
     * @example Debug::$strLogDateTime = 'd/m/Y His';
     * @var string logDateTime
     */
    public static $strLogDateTime = 'Y-m-d H:i:s';

    /**
     * HTML template file for debug
     * Change the template file for your logging as the example below.
     * @example Debug::$strTemplateFile = 'myAwesomeTemplate';
     * The template file MUST have HTML content and the '@debugcontext' string in body to bind or will show nothing
     * @see Debug::displayHtmlBody() to check all options to display on template
     * @var string $strTemplateFile
     */
    public static $strTemplateFile = (__DIR__) . '/../template/default.html';

    /**
     * Used only to manipulate the debug context
     * @var string $strColorfulContext
     */
    protected static $strColorfulContext;

    /**
     * Use this to display pretty debug
     * @example Debug::$isPretty = true;
     * @var bool isPretty
     */
    public static $isPretty = false;

    /**
     * The pretty Debug context
     * @var string prettyDebugContext
     */
    protected static $prettyDebugContext = '';

    /**
     * The backtrace
     * @var array arrBacktrace
     */
    protected static $arrBacktrace = [];

    /**
     * Determine if will end the application after the write de debug context
     * @example Debug::$end = true or pass 'exit' as param to any debug method
     * @var bool booEndApplication
     */
    public static $end = false;

    /**
     * The function will be used to display
     * @var string strFunc
     */
    protected static $strFunc;

    /**
     * Dump method to debug using var_dump displaying as html on screen.
     * You can send unlimited parameters from any type to this method and will be listed in screen as arguments
     * To end the application after debug send the string 'exit' as the param.See example below
     * @example Debug::dump($strArgument1, $intArgument2, $arrArgument3, 'exit');
     * @return string
     */
    public static function dump()
    {
        self::$arrBacktrace = array_shift(debug_backtrace());
        self::$strFunc = 'var_dump';
        $strDisplay = self::write();

        if (self::$isPretty && self::$end) {
            exit(self::displayHtmlBody());
        } elseif (self::$isPretty) {
            exit(self::displayHtmlBody());
        } elseif (self::$end) {
            exit($strDisplay);
        } else {
            echo $strDisplay;
        }

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
        self::$arrBacktrace = array_shift(debug_backtrace());
        self::$strFunc = 'print_r';
        $strDisplay = self::write();

        if (self::$isPretty && self::$end) {
            exit(self::displayHtmlBody());
        } elseif (self::$isPretty) {
            exit(self::displayHtmlBody());
        } elseif (self::$end) {
            exit($strDisplay);
        } else {
            echo $strDisplay;
        }

        return $strDisplay;
    }

    /**
     * Method to write the debug
     * @return string
     */
    protected static function write()
    {
        $filePathContext = self::$arrBacktrace['file'];
        $lineContext = self::$arrBacktrace['line'];
        $argsContext = self::$arrBacktrace['args'];
        $function = self::$strFunc;

        $strDisplay = '<p>' . $filePathContext . ' on line ' . $lineContext . '</p>';

        if (is_array($argsContext) && !empty($argsContext)) {
            foreach ($argsContext as $intKey => $mixValue) {
                if ($mixValue === 'exit') {
                    self::$end = true;
                    continue;
                }

                ob_start();
                $function($mixValue);
                $strDisplay .= 'Argument ' . ($intKey + 1) . '<br />';
                $strDisplay .= '<pre>' . ob_get_contents() . '</pre>';

                if (self::$isPretty) {
                    self::prettify($mixValue, $intKey, ob_get_contents());
                }

                ob_end_clean();
            }
        }

        return $strDisplay;
    }

    /**
     * Dump method to debug displaying as json on screen.
     * You can send unlimited parameters from any type to this method and will be listed in screen as arguments
     * To end the application after debug send the string 'exit' or 'die' as the param. See example below
     * @example Debug::dump($strArgument1, $intArgument2, $arrArgument3, 'exit');
     * @return string
     */
    public static function json()
    {
        self::$arrBacktrace = array_shift(debug_backtrace());
        $arrJsonDisplay = self::$arrBacktrace;
        $arrJsonDisplay['debug'] = [];

        if (is_array($arrJsonDisplay['args']) && !empty($arrJsonDisplay['args'])) {
            foreach ($arrJsonDisplay['args'] as $intKey => $mixValue) {
                if ($mixValue === 'exit') {
                    self::$end = true;
                    continue;
                }

                array_push(
                    $arrJsonDisplay['debug'],
                    array(
                        "Argument " . ($intKey + 1) => $mixValue
                    )
                );
            }
        }

        unset($arrJsonDisplay['args'], $arrJsonDisplay['class'], $arrJsonDisplay['type']);
        $strFormattedJson = json_encode($arrJsonDisplay, JSON_PRETTY_PRINT);

        if (self::$end) {
            exit($strFormattedJson);
        }

        echo $strFormattedJson;
        return $strFormattedJson;
    }

    /**
     * Method to debug displaying on log.
     *
     * @return bool
     */
    public static function log()
    {
        self::$arrBacktrace = array_shift(debug_backtrace());
        $strMessage = self::$arrBacktrace['file'] . ' on line ' . self::$arrBacktrace['line'];
        $arrLogDisplay = self::$arrBacktrace;
        $arrLogDisplay['debug'] = [];

        if (is_array($arrLogDisplay['args']) && !empty($arrLogDisplay['args'])) {
            foreach ($arrLogDisplay['args'] as $intKey => $mixValue) {
                array_push(
                    $arrLogDisplay['debug'],
                    array(
                        "Argument " . ($intKey + 1) => $mixValue
                    )
                );
            }
        }

        $log = new Logger(self::$strLogName);
        $log->pushProcessor(new MemoryPeakUsageProcessor());
        $log->pushProcessor(new WebProcessor());
        $streamHandler = new StreamHandler(self::$strLogPath, Logger::DEBUG);
        $log->pushHandler($streamHandler);

        unset(
            $arrLogDisplay['args'],
            $arrLogDisplay['class'],
            $arrLogDisplay['type'],
            $arrLogDisplay['file'],
            $arrLogDisplay['line']
        );

        return $log->debug($strMessage, $arrLogDisplay);
    }

    /**
     * Method to debug displaying on log.
     *
     * @return bool
     */
    public static function console()
    {
        self::$arrBacktrace = array_shift(debug_backtrace());
        $strMessage = self::$arrBacktrace['file'] . ' on line ' . self::$arrBacktrace['line'];
        $arrLogDisplay = self::$arrBacktrace;
        $arrLogDisplay['debug'] = [];

        if (is_array($arrLogDisplay['args']) && !empty($arrLogDisplay['args'])) {
            foreach ($arrLogDisplay['args'] as $intKey => $mixValue) {
                array_push(
                    $arrLogDisplay['debug'],
                    array(
                        "Argument " . ($intKey + 1) => $mixValue
                    )
                );
            }
        }

        $log = new Logger(self::$strLogName);
        $log->pushProcessor(new MemoryPeakUsageProcessor());
        $log->pushProcessor(new WebProcessor());
        $streamHandler = new StreamHandler('php://stdout', Logger::DEBUG);
        $log->pushHandler($streamHandler);

        unset(
            $arrLogDisplay['args'],
            $arrLogDisplay['class'],
            $arrLogDisplay['type'],
            $arrLogDisplay['file'],
            $arrLogDisplay['line']
        );

        return $log->debug($strMessage, $arrLogDisplay);
    }

    /**
     * Method to pretify the debug display
     * @param $mixValue
     * @param $intKey
     * @param $strContent
     * @return void
     */
    protected static function prettify($mixValue, $intKey, $strContent)
    {
        self::$prettyDebugContext .= '<p class="keyword">Argument ' . ($intKey + 1) . '</p>';
        self::$prettyDebugContext .= self::colorfulContext($mixValue, $strContent);
    }

    /**
     * Method to create the html for debugging
     * @return string
     */
    protected static function displayHtmlBody()
    {
        $strServerUrlRequestContext = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' .
            "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";

        $strDateTimeContext = date('Y-m-d H:i:s');
        $strFilePathContext = self::$arrBacktrace['file'];
        $intLineContext = self::$arrBacktrace['line'];

        $strFileContent = file_get_contents(self::$strTemplateFile);
        $strHtmlContent = str_replace('@debugcontext', self::$prettyDebugContext, $strFileContent);
        $strHtmlContent = str_replace('@filepathcontext', $strFilePathContext, $strHtmlContent);
        $strHtmlContent = str_replace('@linecontext', $intLineContext, $strHtmlContent);
        $strHtmlContent = str_replace('@serverurlrequest', $strServerUrlRequestContext, $strHtmlContent);
        $strHtmlContent = str_replace('@datetimecontext', $strDateTimeContext, $strHtmlContent);

        $intExecutionTimeContext = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        $strHtmlContent = str_replace('@executiontimecontext', $intExecutionTimeContext, $strHtmlContent);
        $intMemoryUsageContext = memory_get_peak_usage();
        $strHtmlContent = str_replace('@memoryusagecontext', $intMemoryUsageContext, $strHtmlContent);
        return $strHtmlContent;
    }

    /**
     * @param $mixContext
     * @param $strDebugContext
     * @return mixed
     */
    protected static function colorfulContext($mixContext, $strDebugContext)
    {
        self::$strColorfulContext = $strDebugContext;
        self::applyHtmlTags($mixContext);
        return self::$strColorfulContext;
    }

    /**
     * Method to apply the styles on debug context
     * @param mixed $mixValue
     * @param bool $isArrayIndex
     * @return mixed
     */
    protected static function applyHtmlTags($mixValue, $isArrayIndex = false)
    {

        if ($isArrayIndex) {
            $strHtmlTagsValue = '["<span class="literal">' . $mixValue . '</span>"]';
            $mixValue = '["' . $mixValue . '"]';
            self::applyHtmlTagsToContext($mixValue, $strHtmlTagsValue);
        } else {
            $strVarType = gettype($mixValue);

            switch ($strVarType) {
                case 'string':
                    $strHtmlTagsValue = '"<span class="string">' . htmlentities($mixValue) . '</span>"';
                    $mixValue = '"' . $mixValue . '"';
                    self::applyHtmlTagsToContext($mixValue, $strHtmlTagsValue);
                    break;

                case 'integer':
                    $strHtmlTagsValue = 'int(<span class="number">' . $mixValue . '</span>)';
                    $mixValue = 'int(' . $mixValue . ')';
                    self::applyHtmlTagsToContext($mixValue, $strHtmlTagsValue);
                    break;

                case 'double':
                    $strHtmlTagsValue = 'float(<span class="number">' . $mixValue . '</span>)';
                    $mixValue = 'float(' . $mixValue . ')';
                    self::applyHtmlTagsToContext($mixValue, $strHtmlTagsValue);
                    break;

                case 'boolean':
                    if ($mixValue) {
                        $mixValue = 'true';
                    } else {
                        $mixValue = 'false';
                    }
                    $strHtmlTagsValue = 'bool(<span class="boolean">' . $mixValue . '</span>)';
                    $mixValue = 'bool(' . $mixValue . ')';
                    self::applyHtmlTagsToContext($mixValue, $strHtmlTagsValue);
                    break;

                case 'array':
                    # this can slow down your performance too, but still useful. Just comment it to increate your
                    # performance
                    array_walk_recursive($mixValue, function ($mixValue, $mixKey) {
                        self::applyHtmlTags($mixKey, true);
                        self::applyHtmlTags($mixValue);
                    });
                    break;

                case 'object':
                    # not implemented because of segmentation fault. Will totally down your performance.
                    break;

                default:
                    break;
            }
        }

        return self::$strColorfulContext;
    }

    /**
     * Method apply the replacement of html tags
     * @param string $mixValue
     * @param string $strHtmlTagsValue
     */
    protected static function applyHtmlTagsToContext($mixValue, $strHtmlTagsValue)
    {
        self::$strColorfulContext = str_replace($mixValue, $strHtmlTagsValue, self::$strColorfulContext);
        return;
    }
}
