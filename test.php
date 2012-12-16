<?php
/**
* Wistia PHP Class Library - Test Page
*
* Page for quick illustration of library functionality. Dumps out the Account object for the provided API password. Use GET
* parameter "api", e.g. /test.php?api=<password>.
* 
* @author Thorne N. Melcher <tmelcher@portdusk.com>
* @copyright Copyright 2012, Thorne N. Melcher
* @license LGPL v3 (see LICENSE.txt)
* @package Wistia-API-Toolkit
* @version 2.0-b1
*/

// Defines the full namespace path for Account (which will itself load Media, Project, and APIEntity automatically)
use \wistia\Account;

// Creating a function named __autoload in PHP lets you define "smart" class includes. This one is fairly basic, but this
// can always be done manually if necessary.
function __autoload($class_name) {
    $pieces = explode("\\", $class_name);
    $class_name = $pieces[count($pieces)-1];
    include_once $class_name . '.php';
}

// Creates an Account object from our API key passed as a GET parameter.
$a = new Account($_GET['api']);

// Dumps out information about the account, global stats for the account, and all projects on the account.
echo("Dump of your Account object:");
var_dump($a);
echo("<hr />");
echo("All-time Stats object for your account:");
var_dump($a->getStats());
echo("<hr />");
echo("DailyStats object for today for your account:");
var_dump($a->getDailyStats(time()));
echo("<hr />");
echo("MonthlyStats object for this month for your account:");
var_dump($a->getMonthlyStats(date("m"), date("Y")));
echo("<hr />");
echo("Array of Project objects associated with your account:");

// Optional parameter lets you force recursive loading, which hydates the children Media objects' fields.
var_dump($a->getProjects(true));