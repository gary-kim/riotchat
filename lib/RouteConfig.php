<?php
namespace OCA\RiotChat;

class RouteConfig extends \OC\AppFramework\Routing\RouteConfig {
	public function __construct($a, $b, $c) {
		parent::__construct($a, $b, $c);
		array_push($this->rootUrlApps, 'riotchat');
	}
}
