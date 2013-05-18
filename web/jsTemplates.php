<?php

header('Content-Type:application/x-javascript; charset=utf-8');

$moduleName = $_GET['moduleName'];

if (!$moduleName) {
    die('No module name!');
}

echo JSTemplater::getTemplates(
    $moduleName,
    isset($_GET['templates']) ? $_GET['templates'] : null,
    isset($_GET['as']) ? $_GET['as'] : null
);

class JSTemplater
{
    private static function parseJsHtml($partialHtml)
    {
        //$partialHtml = preg_replace("/[ ]+/", " ", $partialHtml);
        $partialHtml = preg_replace("/[\r\t\n]/", " ", $partialHtml);
        $partialHtml = str_replace("<%", "\t", $partialHtml);
        $partialHtml = preg_replace(
            array("/((^|%>)[^\t]*)'/", "/\t=(.*?)%>/"),
            array("$1\r", "',$1,'"),
            $partialHtml
        );
        $partialHtml = str_replace(
            array("\t", "%>", "\r"),
            array("');", PHP_EOL . "p.push('", "\'"),
            $partialHtml
        );
        $partialHtml = preg_replace('/\<\!\-\-(.*?)\-\-\>/m', '', $partialHtml);
        $partialHtml = preg_replace('/[ ]{2,}/m', ' ', $partialHtml);

        return "function(obj){obj=obj||{};var p=[],print=function(){p.push.apply(p,arguments);};with(obj){p.push(" . PHP_EOL .
        "'" . $partialHtml . "'"
        . PHP_EOL . ");}return p.join('')}";
    }

    public static function getTemplates($moduleName, $templates = null, $as = null)
    {
        $templatesList = array();
        if ($templates) {
            $templatesList = explode(',', $templates);
        }

        $templateDir = "./templates/" . $moduleName . "/";

        $html = "(function (BB) { BB.templates['" . (is_null($as) ?  $moduleName : $as) . "'] = BB.templates['" . (is_null($as) ?  $moduleName : $as) . "'] || {};" . PHP_EOL;
        $htmlPieces = array();

        foreach (scandir($templateDir) as $file) {
            $templateName = preg_replace('/([^\.]+)\.jst/', '$1', $file);
            if (
                strpos($file, '.jst') !== false &&
                (count($templatesList) == 0 || in_array($templateName, $templatesList))
            ) {
                $partialHtml = file_get_contents($templateDir . $templateName . '.jst');
                $htmlPieces[] = "LEO.templates['" . (is_null($as) ?  $moduleName : $as) . "']['" . $templateName . "'] = " . self::parseJsHtml(
                        $partialHtml
                    );
            }
        }

        $html .= implode(',' . PHP_EOL, $htmlPieces);
        $html .= "})(BB);" . PHP_EOL;

        return $html;
    }
}