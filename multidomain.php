<?php
/*
Email autoconfig library for Mozilla Thunderbird 
Copyright (C) 2013 Julian J. Menendez <julian@maxosystem.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require("config.php");
require("Autoconfig.class.php");


//Get the requested domain
$domain = $_SERVER['HTTP_HOST'];
$domain = strtolower( str_replace("autoconfig.","",$domain) );


//Get the configuration template for the domain, or the default
$templateid=null;
if (isset($DOMAINS[$domain])) {
	$templateid=$DOMAINS[$domain];
} else if (isset($DOMAINS['DEFAULT'])) {
	$templateid=$DOMAINS['DEFAULT'];
}

if (is_null($templateid))
	die("Domain not supported");
if (!isset($TEMPLATES[$templateid]))
	die("Domain not configured");

$tpl=$TEMPLATES[$templateid];


//Setup the provider
$a=new Autoconfig();
$a->addProvider($tpl['id'], $domain, $tpl['name'], $tpl['shortname']);
foreach($tpl['servers'] as $s) {
	$a->addServer($s['type'], $s['host'], $s['port'], $s['socket'], $s['user'], $s['auth']);
}

//Output the XML
header("Content-Type: text/xml");
echo $a->getXML();
