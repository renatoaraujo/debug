<?php

namespace Renatoaraujo;

use Monolog\Formatter\NormalizerFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\IntrospectionProcessor;

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
    public static $strLogName = 'Renatoaraujo\Debug';

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
     * The template file MUST have HTML content and the '@debugcontext' string in body to bind
     * @var string $strTemplateFile
     */
    public static $strTemplateFile = (__DIR__) . '/../template/default.html';

    /**
     * Used only to manipulate the debug context
     * @var string $strColorfulContext
     */
    protected static $strColorfulContext;

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
        $debugContext = '';
        $filePathContext = $arrPath['file'];
        $lineContext = $arrPath['line'];

        $strDisplay = '<p>' . $filePathContext . ' on line ' . $lineContext . '</p>';
        $booEndApplication = false;

        if (is_array($arrPath['args']) && !empty($arrPath['args'])) {
            foreach ($arrPath['args'] as $intKey => $mixValue) {

                if ($mixValue === 'exit' || $mixValue === 'die') {
                    $booEndApplication = true;
                    continue;
                }

                ob_start();
                var_dump($mixValue);
                $strDisplay .= 'Argument ' . ($intKey + 1) . '<br />';
                $strDisplay .= '<pre>' . ob_get_contents() . '</pre>';
                $debugContext .= '<p class="keyword">Argument ' . ($intKey + 1) . '</p>';
                $debugContext .= self::colorfulContext($mixValue, ob_get_contents());
                ob_end_clean();
            }
        }

        $strHtmlDisplay = self::displayHtmlBody($debugContext, $filePathContext, $lineContext);

        if ($booEndApplication) {
            exit($strHtmlDisplay);
        }

        echo $strHtmlDisplay;
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
        $debugContext = '';
        $filePathContext = $arrPath['file'];
        $lineContext = $arrPath['line'];

        $strDisplay = '<p>' . $filePathContext . ' on line ' . $lineContext . '</p>';
        $booEndApplication = false;

        if (is_array($arrPath['args']) && !empty($arrPath['args'])) {
            foreach ($arrPath['args'] as $intKey => $mixValue) {
                if ($mixValue === 'exit' || $mixValue === 'die') {
                    $booEndApplication = true;
                    continue;
                }
                ob_start();
                print_r($mixValue);
                $strDisplay .= 'Argument ' . ($intKey + 1) . '<br />';
                $strDisplay .= '<pre>' . ob_get_contents() . '</pre>';
                $debugContext .= '<span class="keyword">Argument ' . ($intKey + 1) . '</span><br />'
                    . ob_get_contents();
                ob_end_clean();
            }
        }

        $strHtmlDisplay = self::displayHtmlBody($debugContext, $filePathContext, $lineContext);

        if ($booEndApplication) {
            exit($strHtmlDisplay);
        }

        echo $strHtmlDisplay;
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
        $backtrace = debug_backtrace();
        $arrPath = array_shift($backtrace);
        $booEndApplication = false;

        $arrJsonDisplay = [];
        $arrJsonDisplay['file'] = "{$arrPath['file']} on line {$arrPath['line']} ";
        $arrJsonDisplay['debug'] = [];

        if (is_array($arrPath['args']) && !empty($arrPath['args'])) {
            foreach ($arrPath['args'] as $intKey => $mixValue) {
                if ($mixValue === 'exit' || $mixValue === 'die') {
                    $booEndApplication = true;
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

        $strFormattedJson = json_encode($arrJsonDisplay, JSON_PRETTY_PRINT);

        if ($booEndApplication) {
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

    /**
     * Method to create the html for debugging
     * @param string $strDebugContext
     * @param string $strFilePathContext
     * @param int $intLineContext
     * @return string
     */
    protected static function displayHtmlBody($strDebugContext, $strFilePathContext = null, $intLineContext = null)
    {
        $strServerUrlRequestContext = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' .
            "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";

        $strDateTimeContext = date('Y-m-d H:i:s');

        $strFileContent = file_get_contents(self::$strTemplateFile);
        $strHtmlContent = str_replace('@debugcontext', $strDebugContext, $strFileContent);
        $strHtmlContent = str_replace('@filepathcontext', $strFilePathContext, $strHtmlContent);
        $strHtmlContent = str_replace('@linecontext', $intLineContext, $strHtmlContent);
        $strHtmlContent = str_replace('@serverurlrequest', $strServerUrlRequestContext, $strHtmlContent);
        $strHtmlContent = str_replace('@datetimeContext', $strDateTimeContext, $strHtmlContent);

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
        $strVarType = gettype($mixValue);

        switch ($strVarType) {
            case 'string':
                $strHtmlTagsValue = '"<span class="string">' . $mixValue . '</span>"';
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

            case 'array':
                array_walk_recursive($mixValue, function ($mixValue, $mixKey) {
                    self::applyHtmlTags($mixKey, true);
                    self::applyHtmlTags($mixValue);
                });
                break;

            default:
                break;
        }

        if ($isArrayIndex) {
            $strHtmlTagsValue = '["<span class="literal">' . $mixValue . '</span>"]';
            $mixValue = '["' . $mixValue . '"]';
            self::applyHtmlTagsToContext($mixValue, $strHtmlTagsValue);
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
