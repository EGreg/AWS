 <?php
/**
*
* Copyright (c) 2011, Dan Myers.
* Parts copyright (c) 2008, Donovan Schonknecht.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
* - Redistributions of source code must retain the above copyright notice,
*   this list of conditions and the following disclaimer.
* - Redistributions in binary form must reproduce the above copyright
*   notice, this list of conditions and the following disclaimer in the
*   documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
* IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
* ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
* LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
* CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
* SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
* CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* This is a modified BSD license (the third clause has been removed).
* The BSD license may be found here:
* http://www.opensource.org/licenses/bsd-license.php
*
* Amazon Simple Email Service is a trademark of Amazon.com, Inc. or its affiliates.
*
* SimpleEmailService is based on Donovan Schonknecht's Amazon S3 PHP class, found here:
* http://undesigned.org.za/2007/10/22/amazon-s3-php-class
*
*/

/**
* Amazon SimpleEmailService PHP class
*
* @link http://sourceforge.net/projects/php-aws-ses/
* version 0.8.2
*
*/
class SimpleEmailService
{
	protected $__accessKey; // AWS Access key
	protected $__secretKey; // AWS Secret key
	protected $__host;

	public function getAccessKey() { return $this->__accessKey; }
	public function getSecretKey() { return $this->__secretKey; }
	public function getHost() { return $this->__host; }

	protected $__verifyHost = 2;
	protected $__verifyPeer = 1;

	// verifyHost and verifyPeer determine whether curl verifies ssl certificates.
	// It may be necessary to disable these checks on certain systems.
	// These only have an effect if SSL is enabled.
	public function verifyHost() { return $this->__verifyHost; }
	public function enableVerifyHost($enable = true) { $this->__verifyHost = $enable; }

	public function verifyPeer() { return $this->__verifyPeer; }
	public function enableVerifyPeer($enable = true) { $this->__verifyPeer = $enable; }

	/**
	* Constructor
	*
	* @param string $accessKey Access key
	* @param string $secretKey Secret key
	* @return void
	*/
	public function __construct($accessKey = null, $secretKey = null, $host = 'email.us-west-2.amazonaws.com') {
		if ($accessKey !== null && $secretKey !== null) {
			$this->setAuth($accessKey, $secretKey);
		}
		$this->__host = $host;
	}

	/**
	* Set AWS access key and secret key
	*
	* @param string $accessKey Access key
	* @param string $secretKey Secret key
	* @return void
	*/
	public function setAuth($accessKey, $secretKey) {
		$this->__accessKey = $accessKey;
		$this->__secretKey = $secretKey;
	}

	public function listIdentities() {
		$rest = new SimpleEmailServiceRequest($this, 'GET');
		$rest->setParameter('Action', 'ListIdentities');
		$rest->setParameter('IdentityType', 'Domain');

		$rest = $rest->getResponse();

		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if($rest->error !== false) {
			$this->__triggerError('listIdentities', $rest->error);
			return false;
		}

		$response = array();
		if(!isset($rest->body)) {
			return $response;
		}

		$addresses = array();
		foreach($rest->body->ListIdentitiesResult->Identities->member as $address) {
			$addresses[] = (string)$address;
		}

		$response['Identities'] = $addresses;
		$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;

		return $response;
	}

	public function setIdentityMailFromDomain($identities,$email) {
	
		$rest = new SimpleEmailServiceRequest($this, 'GET');
		
		$rest->setParameter('Action', 'SetIdentityMailFromDomain');
		$rest->setParameter('BehaviorOnMXFailure','UseDefaultValue');
		$rest->setParameter('Identity',$identities);
		$rest->setParameter('MailFromDomain',$email);

		$rest = $rest->getResponse();
		$response=array();
		

		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		if($rest->error !== false) {
			$response['error']=$meassge;
            return $response;
		}

		if(!isset($rest->body)) {
			return $response;
		}

		
		$response['RequestId'] = (string)$rest->body->ResponseMetadata->RequestId;

		return $response;
	}

	

	public function verifyDomainIdentity($domain) {
		$rest = new SimpleEmailServiceRequest($this, 'POST');
		$rest->setParameter('Action', 'VerifyDomainIdentity');
		$rest->setParameter('Domain', $domain);

		$rest = $rest->getResponse();
		
		if($rest->error === false && $rest->code !== 200) {
			$rest->error = array('code' => $rest->code, 'message' => 'Unexpected HTTP status');
		}
		$response=array();

		if($rest->error !== false) {
			$meassge=$this->get_error_message($rest);
             $response['error']=$meassge;
            return $response;
		}

		$response =$rest->body;
		return $response;
	}

	/**
	* Removes the specified email address from the list of verified addresses.
	*
	* @param string email The email address to remove
	* @return The request id for this request.
	*/
	

	private function get_error_message($response)
    {
        if(isset($response->error))
        {
            if(isset($response->error['message']))
            {    
                return $response->error['message'];   
            }
            if(isset($response->error['Error']))
            {
                return $response->error['Error']['Code']."   ".$response->error['Error']['Message'];
            }
        }
    }
	

	/**
	* Trigger an error message
	*
	* @internal Used by member functions to output errors
	* @param array $error Array containing error information
	* @return string
	*/
	public function __triggerError($functionname, $error)
	{
		if($error == false) {
			trigger_error(sprintf("SimpleEmailService::%s(): Encountered an error, but no description given", $functionname), E_USER_WARNING);
		}
		else if(isset($error['curl']) && $error['curl'])
		{
			trigger_error(sprintf("SimpleEmailService::%s(): %s %s", $functionname, $error['code'], $error['message']), E_USER_WARNING);
		}
		else if(isset($error['Error']))
		{
			$e = $error['Error'];
			$message = sprintf("SimpleEmailService::%s(): %s - %s: %s\nRequest Id: %s\n", $functionname, $e['Type'], $e['Code'], $e['Message'], $error['RequestId']);
			trigger_error($message, E_USER_WARNING);
		}
		else {
			trigger_error(sprintf("SimpleEmailService::%s(): Encountered an error: %s", $functionname, $error), E_USER_WARNING);
		}
	}
}

final class SimpleEmailServiceRequest
{
	private $ses, $verb, $parameters = array();
	public $response;

	/**
	* Constructor
	*
	* @param string $ses The SimpleEmailService object making this request
	* @param string $action action
	* @param string $verb HTTP verb
	* @return mixed
	*/
	function __construct($ses, $verb) {
		$this->ses = $ses;
		$this->verb = $verb;
		$this->response = new STDClass;
		$this->response->error = false;
	}

	/**
	* Set request parameter
	*
	* @param string  $key Key
	* @param string  $value Value
	* @param boolean $replace Whether to replace the key if it already exists (default true)
	* @return void
	*/
	public function setParameter($key, $value, $replace = true) {
		if(!$replace && isset($this->parameters[$key]))
		{
			$temp = (array)($this->parameters[$key]);
			$temp[] = $value;
			$this->parameters[$key] = $temp;
		}
		else
		{
			$this->parameters[$key] = $value;
		}
	}

	/**
	* Get the response
	*
	* @return object | false
	*/
	public function getResponse() {

		$params = array();
		
		foreach ($this->parameters as $var => $value)
		{
			if(is_array($value))
			{
				foreach($value as $v)
				{
					$params[] = $var.'='.$this->__customUrlEncode($v);
				}
			}
			else
			{
				$params[] = $var.'='.$this->__customUrlEncode($value);
			}
		}
		
		sort($params, SORT_STRING);
		

		// must be in format 'Sun, 06 Nov 1994 08:49:37 GMT'
		$date = gmdate('D, d M Y H:i:s e');

		$query = implode('&', $params);
	
		$headers = array();
		$headers[] = 'Date: '.$date;
		$headers[] = 'Host: '.$this->ses->getHost();

		$auth = 'AWS3-HTTPS AWSAccessKeyId='.$this->ses->getAccessKey();
		$auth .= ',Algorithm=HmacSHA256,Signature='.$this->__getSignature($date);
		$headers[] = 'X-Amzn-Authorization: '.$auth;
		$arr=['krishna.com','gmail.com'] ;
		$url = 'https://'.$this->ses->getHost().'/';
		


		// Basic setup
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_USERAGENT, 'SimpleEmailService/php');

		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, ($this->ses->verifyHost() ? 1 : 0));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, ($this->ses->verifyPeer() ? 1 : 0));

		// Request types
		switch ($this->verb) {
			case 'GET':
				$url .= '?'.$query;
				
				break;
			case 'POST':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->verb);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
				$headers[] = 'Content-Type: application/x-www-form-urlencoded';
			break;
			case 'DELETE':
				$url .= '?'.$query;
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
			break;
			default: break;
		}
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_HEADER, false);

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($curl, CURLOPT_WRITEFUNCTION, array(&$this, '__responseWriteCallback'));
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		// Execute, grab errors
		if (curl_exec($curl)) {
			$this->response->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		} else {
			$this->response->error = array(
				'curl' => true,
				'code' => curl_errno($curl),
				'message' => curl_error($curl),
				'resource' => $this->resource
			);
		}

		@curl_close($curl);

		// Parse body into XML
		if ($this->response->error === false && isset($this->response->body)) {
			$this->response->body = simplexml_load_string($this->response->body);

			// Grab SES errors
			if (!in_array($this->response->code, array(200, 201, 202, 204))
				&& isset($this->response->body->Error)) {
				$error = $this->response->body->Error;
				$output = array();
				$output['curl'] = false;
				$output['Error'] = array();
				$output['Error']['Type'] = (string)$error->Type;
				$output['Error']['Code'] = (string)$error->Code;
				$output['Error']['Message'] = (string)$error->Message;
				$output['RequestId'] = (string)$this->response->body->RequestId;

				$this->response->error = $output;
				unset($this->response->body);
			}
		}

		return $this->response;
	}

	/**
	* CURL write callback
	*
	* @param resource &$curl CURL resource
	* @param string &$data Data
	* @return integer
	*/
	private function __responseWriteCallback(&$curl, &$data) {
		$this->response->body .= $data;
		return strlen($data);
	}

	/**
	* Contributed by afx114
	* URL encode the parameters as per http://docs.amazonwebservices.com/AWSECommerceService/latest/DG/index.html?Query_QueryAuth.html
	* PHP's rawurlencode() follows RFC 1738, not RFC 3986 as required by Amazon. The only difference is the tilde (~), so convert it back after rawurlencode
	* See: http://www.morganney.com/blog/API/AWS-Product-Advertising-API-Requires-a-Signed-Request.php
	*
	* @param string $var String to encode
	* @return string
	*/
	private function __customUrlEncode($var) {
		return str_replace('%7E', '~', rawurlencode($var));
	}

	/**
	* Generate the auth string using Hmac-SHA256
	*
	* @internal Used by SimpleDBRequest::getResponse()
	* @param string $string String to sign
	* @return string
	*/
	private function __getSignature($string) {
		return base64_encode(hash_hmac('sha256', $string, $this->ses->getSecretKey(), true));
	}
}



