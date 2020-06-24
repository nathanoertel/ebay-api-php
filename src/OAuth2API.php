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

class OAuth2API {
    private $environment;
    
    private $credentials;

    private $logger;

    public function generateAuthorizationUrl($scopes, $state = null) {
        $url = $this->environment->getAuthEndpoint();

        $url .= '?client_id='.$this->credentials->getAppId();
        $url .= '&response_type=code';
        $url .= '&redirect_uri='.$this->credentials->getRedirectURL();
        $url .= '&scope='.implode(' ', $scopes);
        if($state) $url .= '&state='.$state;

        return $url;
    }

    public function exchangeCodeForAccessToken($code) {
        return $this->sendRequest(array(
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->credentials->getRedirectURL(),
            'code' => $code
        ));
    }

    public function refreshToken(model\AccessToken $accessToken) {
        $time = time();
        $response = $this->sendRequest(array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $accessToken->getRefreshToken(),
            'scope' => implode(' ', $accessToken->getScopes())
        ));

        if($response) {
            $accessToken->setToken(
                $response['access_token'],
                $time + $response['expires_in']
            );
            return true;
        } else return false;
    }

    private function sendRequest($data) {
		$curl = curl_init();

		$options = array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => 'https://'.$this->environment->getAPIEndpoint().'/identity/v1/oauth2/token',
		    CURLOPT_HTTPHEADER => array(
		    	'Accept: application/json',
                'Authorization: Basic '.base64_encode($this->credentials->getAppId().':'.$this->credentials->getCertId()),
                'Content-Type: application/x-www-form-urlencoded',
                'Accept-Encoding: application/gzip'
		    ),
		    CURLOPT_HEADER => 1,
		    CURLOPT_RETURNTRANSFER => 1
        );
        
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        $this->log('POST '.$options[CURLOPT_URL]);
        $this->log($options[CURLOPT_POSTFIELDS]);
		
		curl_setopt_array($curl, $options);

        $error = null;
        $response = curl_exec($curl);

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
    
			if($this->logger) {
                $this->log($headers);
                $this->log(json_encode($response, JSON_PRETTY_PRINT));
            }
			
            if(isset($response['errors'])) {
                $errors = array();
                foreach($response['errors'] as $error) { 
                    $errors[] = $error['message'];
                }
                $error = new exception\RequestException(implode("\n", $errors));
            } else if($httpCode != 200) {
                $error = new exception\RequestException('Unknown Error: '.$httpCode);
            }
		} else {
            if($this->logger) $this->log(curl_error($curl));
            $error = new exception\RequestException('Unknown Curl Error: '.curl_errno($curl));
		}
		
        curl_close($curl);
        if($error) throw $error;
        else return $response;
    }

    public function __construct(model\Environment $environment, model\Credentials $credentials, $logger = null) {
        $this->environment = $environment;
        $this->credentials = $credentials;
        $this->logger = $logger;
    }

    private function log($message) {
        if($this->logger) $this->logger->info($message);
    }
}