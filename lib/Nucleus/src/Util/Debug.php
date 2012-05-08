<?php

namespace Util;

class Debug
{
    public static function dump()
    {
        $vars = func_get_args();

        switch(true){
            case function_exists('xdebug_var_dump'):
                $function = "xdebug_var_dump";
                break;
            default:
                $function = "var_dump";
                break;
        }

        foreach($vars as $v)
            $function($v);
    }

    public static function dumpXml($xml)
    {
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        @$dom->loadXML($xml->asXml());
        $output = $dom->saveXML();
        self::dump(PHP_EOL . $output);
    }
}