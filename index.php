<?php

use HeadlessChromium\BrowserFactory;

require_once './vendor/autoload.php';

$browserFactory = new BrowserFactory('C:\Program Files\Google\Chrome\Application\chrome.exe');
$browser = $browserFactory->createBrowser();

if (!isset($argv[1])) {
    echo "Key(s) is required";
    return;
}

unset($argv[0]);

$keys = ucwords(implode(' ', $argv));
$encodedKeys = urlencode($keys);
$allowedElements = "[jsname='W297wb'], [jsname='jqKxS']";

try {
    $page = $browser->createPage();
    $page->navigate("https://translate.google.com/?sl=en&tl=pt&text={$encodedKeys}&op=translate")
        ->waitForNavigation('networkIdle', 10000);

    $content = $page->evaluate("document.querySelector(`{$allowedElements}`).innerText")->getReturnValue();

    echo colorLog($keys) . colorLog(ucwords($content), 'success');
} catch (\Exception $e) {
    echo colorLog('Error: ' . $e->getMessage(), 'error');
} finally {
    $browser->close();
}

function colorLog($string, $type = 'info')
{
    switch ($type) {
        case 'error':
            return "\033[31m$string \033[0m\n";
        case 'success':
            return "\033[32m$string \033[0m\n";
        case 'warning':
            return "\033[33m$string \033[0m\n";
        case 'info':
            return "\033[36m$string \033[0m\n";
        default:
            return "$string\n";
    }
}
