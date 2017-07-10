<?php
/**
* @file RefusalService.php
* @brief REST API Refusal module
* @author INNOPOST (tech@innopost.com)
*/

class RefusalService
{
	private $ServiceURL = "http://api.biz080.com";
	private $Version = "1";
	private $UserID;
	private $APIKey;
	private $Timestamp;

	public function __construct($UserID, $APIKey){
		$this->UserID = $UserID;
		$this->APIKey = $APIKey;
	}

	public function __destruct(){
	}

	private function executeCURL($uri, $method = null, $header = array(), $postdata = null, $isMultiPart = false){
		$http = curl_init($this->ServiceURL."/".$this->Version."/".$uri);
		if($isMultiPart) {
			$header[] = "Content-Type:multipart/form-data";
		}else{
			$header[] = "Content-Type:application/x-www-form-urlencoded;charset=utf8;";
			if($postdata){
				foreach($postdata as $k => $v){
					$temp[] = $k."=".$v;
				}
				$postdata = implode($temp,"&");
			}
		}
		$isPost = ($method == "POST")?true:false;
		$options = array(
			CURLOPT_POST => $isPost,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_SSLVERSION => 3,
			CURLOPT_HEADER => 0,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HTTPHEADER => $header,
			CURLOPT_POSTFIELDS => $postdata,
		);
		@curl_setopt_array($http, $options);
		$responseJson = curl_exec($http);

		$http_status = curl_getinfo($http, CURLINFO_HTTP_CODE);
		curl_close($http);

		if($http_status != 200){
			throw new APIException($responseJson);
		}else{
			$returnResult = json_decode($responseJson ,true);
		}
		return $returnResult;
	}

	private function setContent($contents){
		$result = null;
		foreach($contents as $val){
			$result .= "/".urlencode($val);
		}

		return $result;
	}

	private function createToken($uri){
			$this->Timestamp = time();
			$cert_data =  "/".$this->Version."/".$uri."/".$this->Timestamp;

			return hash_hmac("sha1", $cert_data, $this->APIKey);
	}

	public function getSearch($contents){
		try{
			$uri = "search".$this->setContent($contents);
			$token = $this->createToken($uri);
			$header = array();
			$header[] = "Authorization: Basic  ".base64_encode($this->UserID.":".$token);
			$header[] = "INNO-TimeStamp: ".$this->Timestamp;

			return $this->executeCURL($uri,"GET",$header);
		}catch(APIException $e){
			echo $e;
			exit;
		}
	}

	public function getList($contents){
		try{
			$uri = "list".$this->setContent($contents);
			$token = $this->createToken($uri);
			$header = array();
			$header[] = "Authorization: Basic  ".base64_encode($this->UserID.":".$token);
			$header[] = "INNO-TimeStamp: ".$this->Timestamp;

			return $this->executeCURL($uri,"GET",$header);
		}catch(APIException $e){
			echo $e;
			exit;
		}
	}
}

class APIException extends Exception
{
	public function __construct($response,$code = 10000, Exception $previous = null) {
		$Err = json_decode($response);
		if(is_null($Err)) {
			parent::__construct($response, $code );
		}
		else {
			parent::__construct($Err->message, $Err->code);
		}
	}
	public function toArray(){
		$result = array(
			"code" => $this->code,
			"message" => mb_convert_encoding($this->message,"EUC-KR","UTF-8"),
		);
		return $result;
	}
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: ".mb_convert_encoding($this->message,"EUC-KR","UTF-8")."\n";
	}
}
?>