<?php

use ofarukbicer\Regexify\Regexify;

require "../vendor/autoload.php";

$regexify = new Regexify;

$regexify
->create("asd")
->begin_with(['hello', 'hola'], null, [2,3])
->then(',')
->then('world', 2)
->then('!', null, [1,])
->end_with();

$regexify
->create("dsa")
->begin_with(":uppercase", 3)
->then([":number", '-'], null, [2,10])
->not(":alphanumeric", 1)
->end_with();

echo $regexify->asd . "\n";
echo $regexify->dsa . "\n";