<?php

declare(strict_types=1);

use Nextcloud\CodingStandard\Config;

$config = new Config();
$config
	->getFinder()
	->ignoreVCSIgnored(true)
	->exclude('config')
	->notPath('3rdparty')
	->notPath('vendor')
	->in(__DIR__);
return $config;
