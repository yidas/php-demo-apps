<?php

use models\ForUse;

require dirname(__FILE__).'/autoloader.php';

$use = new ForUse;
$test = new models\Test;
$test = new \vendors\test\Test;