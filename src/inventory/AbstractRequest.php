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
namespace eBayAPI\inventory;

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

    public function put($path, $data = array()) {
        return $this->request($path, AbstractRequest::PUT, $data);
    }

    public function post($path, $data = array()) {
        return $this->request($path, AbstractRequest::POST, $data);
    }

    public function delete($path, $data = array()) {
        return $this->request($path, AbstractRequest::DELETE, $data);
    }

    private function getHeaderArray($headers) {
        $headerArray = explode("\r\n", $headers);
        $result = [];
        foreach($headerArray as $index => $header) {
            if(strpos($header, 'HTTP/') === 0) {
                list($httpType, $httpCode, $status) = explode(' ', $header);
                $httpCode = intval($httpCode);
                $result['status'] = $httpCode;
            } else if (!empty($header)) {
                $parts = explode(':', $header);
                $key = array_shift($parts);
                $value = implode(':', $parts);
                $result[strtolower($key)] = trim($value);
            }
        }
        
        return $result;
    }
    
    private function getBody($body, $headers) {
        if(empty($body)) return null;

        $readyBody = $this->isGzipped($headers) ? gzdecode($body) : $body;

        if ($this->isJSON($headers)) {
            return json_decode($readyBody, true);
        }

        return $readyBody;
    }

    private function processResponse($curl, $method, $response) {
        if($response !== false) {
			$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

			$headerString = substr($response, 0, $headerSize);
			$bodyString = substr($response, $headerSize);
			
            $headers = $this->getHeaderArray($headerString);
            $body = $this->getBody($bodyString, $headers);

            $this->log($headerString);
            $this->log(json_encode($body));
    
            if(isset($body['errors'])) {
                $notHandled = true;

                if($headers['status'] == 401) {
                    foreach($body['errors'] as $error) { 
                        if($error['errorId'] == 1001) {
                            $notHandled = false;
                            if($this->refreshToken()) {
                                return $this->request($path, $method, $data);
                            }
                            throw new \eBayAPI\exception\AuthenticationException('Refresh token failed');
                        }
                    }
                }
                
                if(
                    ($method == AbstractRequest::GET || $method == AbstractRequest::DELETE)
                    && $headers['status'] == 404
                ) { // if it's a lookup and it's not found return null
                    return [
                        'headers' => $headers,
                        'body' => null,
                    ];
                }

                $errors = array_map(function ($error) {
                    return $error['message'];
                }, $body['errors']);

                throw new \eBayAPI\exception\RequestException(
                    $body['errors'],
                    implode("\n", $errors),
                    $headers['status']
                ); 
            }

            return [
                'headers' => $headers,
                'body' => $body,
            ];
		}

        throw new \eBayAPI\exception\RequestException(
            [
                [
                    'errorId' => curl_errno($curl),
                    'message' => curl_error($curl)
                ]
            ],
            curl_error($curl),
            curl_errno($curl)
        );
    }

    private function request($path, $method, $data) {
		$curl = curl_init();

		$options = array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => 'https://'.$this->environment->getAPIEndpoint().$path,
		    CURLOPT_HTTPHEADER => array(
		    	// 'Accept: application/json',
                'Authorization: Bearer '.$this->getAuthorization($path),
                'Content-Type: application/json',
                'Accept-Encoding: application/gzip',
                'Content-Language: en-US'
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

        try {
            $result = $this->processResponse($curl, $method, $response);
            curl_close($curl);
            return $result;
        } catch (Exception $e) {
            curl_close($curl);
            throw $e;
        }
    }

    private function isGzipped($headers)
    {
        if (isset($headers['content-encoding'])) {
            $encoding = $headers['content-encoding'];
            return strtolower($encoding) == 'gzip';
        }

        return false;
    }

    private function isJSON($headers)
    {
        if (isset($headers['content-type'])) {
            $type = $headers['content-type'];
            return strtolower($type) == 'application/json';
        }

        return false;
    }

    private function getAuthorization($path) {
        return $this->accessToken->getAccessToken();
    }

    private function refreshToken() {
        $oath2API = new \eBayAPI\OAuth2API($this->environment, $this->credentials, $this->logger);

        return $oath2API->refreshToken($this->accessToken);
    }

    public function __construct(\eBayAPI\model\Environment $environment, \eBayAPI\model\Credentials $credentials, \eBayAPI\model\AccessToken $accessToken, $logger = null) {
        $this->environment = $environment;
        $this->credentials = $credentials;
        $this->accessToken = $accessToken;
        $this->logger = $logger;
    }

    private function log($message) {
        if($this->logger) $this->logger->info($message);
    }
}