<?php
namespace Curl\WebSocket;
use DIC\Service,
Curl\Curl;
class Client{
	private $Curl;
	private $Host;
	private $Port;
	private $Path;
	private $Origin;
	private $Connected=NULL;

	public function __construct(Service $Service){
		$this->Curl=new Curl($Service);
	}

	public function connect($host,$port,$path='/',$origin=false){
		$this->Curl->init();
		$this->Curl->telnet();
		$this->Curl->set_httpheader($this->create_handshake_header($host,$port));
		$this->Curl->set_url('localhost:8080');
		$this->Curl->set_returntransfer(1);
		$this->Curl->set_binarytransfer(1);
		// $this->Curl->set_connectonly(1);
		$this->Curl->set_httpproxytunnel(1);
		$this->Curl->set_readfunction(array($this,"send_data"));
		$this->Curl->set_writefunction(array($this,"recieve_data"));
		

		$this->Curl->execute();
		// var_dump($this->Curl->get_current_output());
		// $this->Curl->set_header(0);
		// $this->Curl->set_customrequest($this->_hybi10Encode('asdf'));
		// $this->Curl->execute();
		// $this->send('asdf');
		// $this->connect1($host,$port);
	}

	public function listen(){
		// while(1){}
	}

	public function recieve_data($ch,$data){
		if(empty($data)){
			echo "empty";
			return false;
		}
		$result=$this->_hybi10Decode($data); 
		echo $result['payload'];

		// $this->send('what??');
		return strlen($data);
		// return 'CURL_WRITEFUNC_PAUSE';
		
	}

	public function send($data){
		// $this->Curl->set_header(0);

		$data=$this->_hybi10Encode($data);
		// $this->Curl->set_text(1);
		// var_dump($data);
		$this->Curl->set_customrequest($this->_hybi10Encode($data)."\r\n");
		$this->Curl->set_httpheader(array('accept:','host:'));
		$this->Curl->set_header(0);
		$this->Curl->execute();
		var_dump($this->Curl->get_current_output());
		return strlen('');
	}

	public function send_data($ch,$stream=null,$data){
		// $this->Curl->set_header(0);
		echo "sending..\n";
		$data=$this->_hybi10Encode($data);

		$this->Curl->set_customrequest($this->_hybi10Encode($data));
		$this->Curl->execute();
		// var_dump($this->Curl->get_current_output());
		return strlen('');
	}

	public function create_handshake_header($host,$port,$path='/',$origin=false){
		$header = array("GET " . $path . " HTTP/1.1",
			"Host: ".$host.":".$port,
			"Upgrade: websocket",
			"Connection: Upgrade",
			"Sec-WebSocket-Key: " . $this->generate_websocket_key(),
			"Sec-WebSocket-Version: 13");
		return $header;
	}

	public function generate_websocket_key($length = 16, $addSpaces = true, $addNumbers = true){
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';
		$useChars = array();
		// select some random chars:
		for($i = 0; $i < $length; $i++)
		{
		$useChars[] = $characters[mt_rand(0, strlen($characters)-1)];
		}
		// add spaces and numbers:
		if($addSpaces === true)
		{
		array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ');
		}
		if($addNumbers === true)
		{
		array_push($useChars, rand(0,9), rand(0,9), rand(0,9));
		}
		shuffle($useChars);
		$randomString = trim(implode('', $useChars));
		$randomString = substr($randomString, 0, $length);
		return base64_encode($randomString);
	}

	private function _hybi10Encode($payload, $type = 'text', $masked = true)
	{
		$frameHead = array();
		$frame = '';
		$payloadLength = strlen($payload);

		switch($type)
		{   
		case 'text':
		// first byte indicates FIN, Text-Frame (10000001):
		$frameHead[0] = 129;    
		break;  

		case 'close':
		// first byte indicates FIN, Close Frame(10001000):
		$frameHead[0] = 136;
		break;

		case 'ping':
		// first byte indicates FIN, Ping frame (10001001):
		$frameHead[0] = 137;
		break;

		case 'pong':
		// first byte indicates FIN, Pong frame (10001010):
		$frameHead[0] = 138;
		break;
		}

		// set mask and payload length (using 1, 3 or 9 bytes)
		if($payloadLength > 65535)
		{
		$payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
		$frameHead[1] = ($masked === true) ? 255 : 127;
		for($i = 0; $i < 8; $i++)
		{
		$frameHead[$i+2] = bindec($payloadLengthBin[$i]);
		}
		// most significant bit MUST be 0 (close connection if frame too big)
		if($frameHead[2] > 127)
		{
		$this->close(1004);
		return false;
		}
		}
		elseif($payloadLength > 125)
		{
		$payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
		$frameHead[1] = ($masked === true) ? 254 : 126;
		$frameHead[2] = bindec($payloadLengthBin[0]);
		$frameHead[3] = bindec($payloadLengthBin[1]);
		}
		else
		{
		$frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
		}

		// convert frame-head to string:
		foreach(array_keys($frameHead) as $i)
		{
		$frameHead[$i] = chr($frameHead[$i]);
		}
		if($masked === true)
		{
		// generate a random mask:
		$mask = array();
		for($i = 0; $i < 4; $i++)
		{
		$mask[$i] = chr(rand(0, 255));
		}

		$frameHead = array_merge($frameHead, $mask);    
		}   
		$frame = implode('', $frameHead);

		// append payload to frame:
		$framePayload = array();    
		for($i = 0; $i < $payloadLength; $i++)
		{   
		$frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
		}

		return $frame;
	}

	private function _hybi10Decode($data){
		$payloadLength = '';
		$mask = '';
		$unmaskedPayload = '';
		$decodedData = array();

		// estimate frame type:
		$firstByteBinary = sprintf('%08b', ord($data[0]));  
		$secondByteBinary = sprintf('%08b', ord($data[1]));
		$opcode = bindec(substr($firstByteBinary, 4, 4));
		$isMasked = ($secondByteBinary[0] == '1') ? true : false;
		$payloadLength = ord($data[1]) & 127;   

		switch($opcode)
		{
			// text frame:
			case 1:
			$decodedData['type'] = 'text';  
			break;

			case 2:
			$decodedData['type'] = 'binary';
			break;

			// connection close frame:
			case 8:
			$decodedData['type'] = 'close';
			break;

			// ping frame:
			case 9:
			$decodedData['type'] = 'ping';  
			break;

			// pong frame:
			case 10:
			$decodedData['type'] = 'pong';
			break;

			default:
			return false;
			break;
		}

		if($payloadLength === 126){
			$mask = substr($data, 4, 4);
			$payloadOffset = 8;
			$dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
		}
		elseif($payloadLength === 127){
			$mask = substr($data, 10, 4);
			$payloadOffset = 14;
			$tmp = '';
			for($i = 0; $i < 8; $i++)
			{
				$tmp .= sprintf('%08b', ord($data[$i+2]));
			}
			$dataLength = bindec($tmp) + $payloadOffset;
			unset($tmp);
		}
		else
		{
			$mask = substr($data, 2, 4);    
			$payloadOffset = 6;
			$dataLength = $payloadLength + $payloadOffset;
		}   

		if($isMasked === true)
		{
			for($i = $payloadOffset; $i < $dataLength; $i++)
		{
			$j = $i - $payloadOffset;
			if(isset($data[$i]))
		{
			$unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
		}
		}
			$decodedData['payload'] = $unmaskedPayload;
		}
		else
		{
			$payloadOffset = $payloadOffset - 4;
			$decodedData['payload'] = substr($data, $payloadOffset);
		}

		return $decodedData;
	}

}