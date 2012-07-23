<?php

class JSONRPC {
	
	protected $data;
	
	static function server($data) {
		$server = new self('server');
		return $server
			->id($data['id'])
			->method($data['method'])
			->params($data['params']);
			
	}
	
	static function client($url) {
		$client = new self('client');
		return $client->url($url);
	}
	
	protected function __construct($type) {
		
		static $id = 0;
		
		if($type == 'server') {
			$this->data = array(
				'jsonrpc' => "2.0",
				'result' => null,
				'error' => null,
				'id' => null
			);
		}
		
		if($type == 'client') {
			$this->data = array(
				'jsonrpc' => "2.0",
				'method' => null,
				'id' => (++$id)
			);
		}
		
	}
	
	/*
		function id($id)
		
		function method($method)
		function params($params)
		
		function result($result)
		function error($code, $message, $data = null)
	*/
	
	function __call($name, $arguments) {
		
		if(count($arguments) > 0) {
			
			switch($name) {
				case "result":
					
					unset($this->data['error']);
					$this->data[$name] = $arguments[0];
					
					break;
				case "error":
					
					unset($this->data['result']);
					
					$this->data['error'] = array(
						'code' => (int) $arguments[0],
						'message' => $arguments[1]
					);
					
					if(isset($arguments[2])) {
						$this->data['error']['data'] = $arguments[2];
					}
					
					break;
				default:
					$this->data[$name] = $arguments[0];
			}
			
			return $this;
		}
		 
		return $this->data[$name];
	}
	
	function __isset($name) {
		return isset($this->data[$name]);
	}
	
	function send() {
		echo json_encode($this->data);
		return 200;
	}
	
	function fetch() {
		
		$c = curl_init($this->url());
		
		$headers = array();
		
		if(isset($_SERVER['REMOTE_ADDR'])) {
			$headers[] = 'X-Forwarded-For: ' . $_SERVER['REMOTE_ADDR'];
		}
		
		$headers[] = 'Content-Type: application/json';
		
		$post = array();
		
		$post['jsonrpc'] = "2.0";
		$post['id'] = $this->id();
		$post['method'] = $this->method();
		
		if(isset($this->params)) {
			$post['params'] = $this->params();
		}
		
		curl_setopt($c, CURLOPT_POST, true);
		curl_setopt($c, CURLOPT_HEADER, false);
		curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($post));
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
		
		$response = json_decode(curl_exec($c));
		
		curl_close($c);
		
		$this->data['result'] = $response['result'];
		$this->data['error'] = $response['error'];
		
		return $this;
	}
	
}
