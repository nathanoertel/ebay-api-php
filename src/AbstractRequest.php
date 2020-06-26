<?php
/*
 * Copyright (c) 2019 Digital Cloud Designs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace eBayAPI;

abstract class AbstractRequest {
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    /**
     * The environment object
     * @var eBayAPI\model\Environment $environment
     */
    protected $environment;

    /**
     * The application credentials
     * @var eBayAPI\model\Credentials $credentials
     */
    protected $credentials;

    /**
     * The user access token
     * @var eBayAPI\model\AccessToken $accessToken
     */
    protected $accessToken;

    /**
     * The PSR-3 compatible logging interface
     * @var mixed $logger
     */
    private $logger;

    public function get($path, $params = array()) {
        return $this->request($path, AbstractRequest::GET, $params);
    }

    public function post($path, $data = array()) {
        return $this->request($path, AbstractRequest::POST, $data);
    }

    private function request($path, $method, $data) {
		$curl = curl_init();

		$options = array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => 'https://'.$this->environment->getAPIEndpoint().$path,
		    CURLOPT_HTTPHEADER => array(
		    	'Accept: application/json',
                'Authorization: Bearer '.$this->getAuthorization($path),
                'Content-Type: application/json',
                'Accept-Encoding: application/gzip'
		    ),
		    CURLOPT_HEADER => 1,
		    CURLOPT_RETURNTRANSFER => 1
        );
        
		if($method == AbstractRequest::GET) {
			if(!empty($data)) $options[CURLOPT_URL] .= '?'.http_build_query($data);
			$this->log('GET '.$options[CURLOPT_URL]);
		} else if($method == AbstractRequest::PUT) {
			$options[CURLOPT_CUSTOMREQUEST] = 'PUT';
		    $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $this->log('UPDATE '.$options[CURLOPT_URL]);
            $this->log($options[CURLOPT_POSTFIELDS]);
		} else if($method == AbstractRequest::POST) {
			$options[CURLOPT_POST] = 1;
		    $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $this->log('POST '.$options[CURLOPT_URL]);
            $this->log($options[CURLOPT_POSTFIELDS]);
		} else if($method == AbstractRequest::DELETE) {
			$options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
			$this->log('DELETE '.$options[CURLOPT_URL]);
		}
		
		curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $exception = null;

		if($response !== false) {
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

			$headers = substr($response, 0, $headerSize);
			$body = substr($response, $headerSize);
			
			$headerArray = explode("\r\n", $headers);
            $httpCode = 500;
            
            foreach($headerArray as $index => $header) {
                if(strpos($header, 'HTTP/') === 0) {
                    list($httpType, $httpCode, $status) = explode(' ', $header);
                    $httpCode = intval($httpCode);
                    break;
                }
            }
            
            $response = json_decode(gzdecode($body), true);

            $this->log($headers);
            $this->log(json_encode($response, JSON_PRETTY_PRINT));
    
            if(isset($response['errors'])) {
                $notHandled = true;

                if($httpCode == 401) {
                    foreach($response['errors'] as $error) { 
                        if($error['errorId'] == 1001) {
                            $notHandled = false;
                            if($this->refreshToken()) {
                                $response = $this->request($path, $method, $data);
                            } else $exception = new exception\AuthenticationException('Refresh token failed');
                            break;
                        }
                    }
                }

                if($notHandled) {
                    $errors = array();
                    foreach($response['errors'] as $error) { 
                        $errors[] = $error['message'];
                    }
                    $exception = new exception\RequestException($response['errors']);
                }
            } else if($httpCode != 200) {
                $exception = new exception\RequestException(array(
                    array(
                        'errorId' => $httpCode,
                        'message' => 'Unknown Error: '.$httpCode
                    )
                ));
            }
		} else {
            $exception = new exception\RequestException(array(
                array(
                    'errorId' => curl_errno($curl),
                    'message' => curl_error($curl)
                )
            ));
		}
		
        curl_close($curl);
        
        if($exception) throw $exception;
        else return $response;
    }

    private function getAuthorization($path) {
        return $this->accessToken->getAccessToken();
    }

    private function refreshToken() {
        $oath2API = new OAuth2API($this->environment, $this->credentials, $this->logger);

        return $oath2API->refreshToken($this->accessToken);
    }

    public function __construct(model\Environment $environment, model\Credentials $credentials, model\AccessToken $accessToken, $logger = null) {
        $this->environment = $environment;
        $this->credentials = $credentials;
        $this->accessToken = $accessToken;
        $this->logger = $logger;
    }

    private function log($message) {
        if($this->logger) $this->logger->info($message);
    }
}