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

/*
XML Format: https://wiki.mozilla.org/Thunderbird:Autoconfiguration:ConfigFileFormat
*/

class Autoconfig {
	var $DOC;

	var $ROOT;
	var $PROVIDER;

	function Autoconfig() {
		$this->DOC=new DOMDocument();
		$this->ROOT = $this->createElement("clientConfig", array("version"=>"1.1"));
		$this->DOC->appendChild($this->ROOT);
	}


	function addProvider($id, $domain, $name, $shortname) {
		$this->PROVIDER = $this->createElement("emailProvider", !is_null($id)?array("id"=>$id):null );

		foreach((array)$domain as $dom) {
			$this->PROVIDER->appendChild($this->createElement("domain",null,$dom));
		}

		$this->PROVIDER->appendChild($this->createElement("displayName",null,$name));
		$this->PROVIDER->appendChild($this->createElement("displayShortName",null,$shortname));
		
		$this->ROOT->appendChild($this->PROVIDER);
	}


	function addServer($type, $host, $port, $socket, $user, $auth="password-cleartext") {
		if (!$this->PROVIDER)
			throw new Exception("addProvider first");
		$element= $type=="smtp"?"outgoingServer":"incomingServer";

		$s = $this->createElement($element,array("type"=>$type));

		$s->appendChild( $this->createElement("hostname", null, $host) );
		$s->appendChild( $this->createElement("port", null, $port) );
		$s->appendChild( $this->createElement("socketType", null, $socket) );
		$s->appendChild( $this->createElement("username", null, $user) );
		$s->appendChild( $this->createElement("authentication", null, $auth) );

		$this->PROVIDER->appendChild($s);
	}


	function getXML() {
		return $this->DOC->saveXML();
	}



	//Devuelve un elemento con los atributos indicados
	private function createElement($name,$attributes=array(),$innerText=null) {
		$e = $this->DOC->createElement($name,$innerText);

		if (is_array($attributes))
		foreach($attributes as $n=>$v) {
			$a = $this->DOC->createAttribute($n);
			$a->appendChild($this->DOC->createTextNode($v));
			$e->appendChild($a);
		}

	
		return $e;
	}

}
