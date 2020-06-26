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

abstract class AbstractSOAPRequest {
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    protected static $siteIDs = array(
        'EBAY_US' => 0,
        'EBAY_CA' => 2,
        'EBAY_GB' => 3,
        'EBAY_AU' => 15,
        'EBAY_AT' => 16,
        'EBAY_BE' => 23,
        'EBAY_FR' => 71,
        'EBAY_DE' => 77,
        'EBAY_MOTORS_US' => 100,
        'EBAY_IT' => 101,
        'EBAY_NL' => 146,
        'EBAY_ES' => 186,
        'EBAY_CH' => 193,
        'EBAY_HK' => 201,
        'EBAY_IN' => 203,
        'EBAY_IE' => 205,
        'EBAY_MY' => 207,
        'EBAY_PH' => 211,
        'EBAY_PL' => 212,
        'EBAY_SG' => 216
    );

    protected $version = 1157;

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

    public function get($operation, $params = array()) {
        return $this->request($operation, AbstractRequest::GET, $params);
    }

    public function post($operation, $data = array()) {
        return $this->request($operation, AbstractRequest::POST, $data);
    }

    protected function request($operation, $marketplaceId, $data) {
        $mode = array (
            'soap_version' => 'SOAP_1_2',
            'trace' => 1,
            'stream_context' => stream_context_create(array(
                'http' => array(
                    'header' => 'X-EBAY-API-IAF-TOKEN: '.$this->getAuthorization()
                )
            ))
        );

        $client = new \SoapClient('https://developer.ebay.com/webservices/latest/ebaysvc.wsdl', $mode);

        $client->__setLocation('https://'.$this->environment->getAPIEndpoint().'/wsapi?'.http_build_query(array(
            'callname' => $operation,
            'version' => $this->version,
            'siteid' => self::$siteIDs[$marketplaceId]
        )));

        try {
            $data['Version'] = $this->version;
            $resp = $client->__soapCall($operation, array($data));
            $this->log($client->__getLastRequestHeaders());
            $this->log($client->__getLastRequest());
            $this->log($client->__getLastResponseHeaders());
            $this->log($client->__getLastResponse());

            return $resp;
        } catch(\SoapException $e) {
            error_log($client->__getLastRequestHeaders());
            $this->log($client->__getLastRequestHeaders());
            $this->log($client->__getLastRequest());
            $this->log($client->__getLastResponseHeaders());
            $this->log($client->__getLastResponse());
            throw $e;
        } catch(\Exception $e) {
            error_log($client->__getLastRequestHeaders());
            $this->log($client->__getLastRequestHeaders());
            $this->log($client->__getLastRequest());
            $this->log($client->__getLastResponseHeaders());
            $this->log($client->__getLastResponse());
            throw $e;
        }
        // $curl = curl_init();
        
        // $soap = SOAP('https://developer.ebay.com/webservices/latest/ebaysvc.wsdl');

		// $options = array(
		//     CURLOPT_RETURNTRANSFER => 1,
		//     CURLOPT_URL => 'https://'.$this->environment->getAPIEndpoint().$path,
		//     CURLOPT_HTTPHEADER => array(
		//     	'Accept: application/json',
        //         'Authorization: Bearer '.$this->getAuthorization($path),
        //         'Content-Type: application/json',
        //         'Accept-Encoding: application/gzip'
		//     ),
		//     CURLOPT_HEADER => 1,
		//     CURLOPT_RETURNTRANSFER => 1
        // );
        
		// if($method == AbstractRequest::GET) {
		// 	if(!empty($data)) $options[CURLOPT_URL] .= '?'.http_build_query($data);
		// 	$this->log('GET '.$options[CURLOPT_URL]);
		// } else if($method == AbstractRequest::PUT) {
		// 	$options[CURLOPT_CUSTOMREQUEST] = 'PUT';
		//     $options[CURLOPT_POSTFIELDS] = json_encode($data);
        //     $this->log('UPDATE '.$options[CURLOPT_URL]);
        //     $this->log($options[CURLOPT_POSTFIELDS]);
		// } else if($method == AbstractRequest::POST) {
		// 	$options[CURLOPT_POST] = 1;
		//     $options[CURLOPT_POSTFIELDS] = json_encode($data);
        //     $this->log('POST '.$options[CURLOPT_URL]);
        //     $this->log($options[CURLOPT_POSTFIELDS]);
		// } else if($method == AbstractRequest::DELETE) {
		// 	$options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
		// 	$this->log('DELETE '.$options[CURLOPT_URL]);
		// }
		
		// curl_setopt_array($curl, $options);

        // $response = curl_exec($curl);
        // $exception = null;

		// if($response !== false) {
		// 	$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

		// 	$headers = substr($response, 0, $headerSize);
		// 	$body = substr($response, $headerSize);
			
		// 	$headerArray = explode("\r\n", $headers);
        //     $httpCode = 500;
            
        //     foreach($headerArray as $index => $header) {
        //         if(strpos($header, 'HTTP/') === 0) {
        //             list($httpType, $httpCode, $status) = explode(' ', $header);
        //             $httpCode = intval($httpCode);
        //             break;
        //         }
        //     }
            
        //     $response = json_decode(gzdecode($body), true);

        //     $this->log($headers);
        //     $this->log(json_encode($response, JSON_PRETTY_PRINT));
    
        //     if(isset($response['errors'])) {
        //         $notHandled = true;

        //         if($httpCode == 401) {
        //             foreach($response['errors'] as $error) { 
        //                 if($error['errorId'] == 1001) {
        //                     $notHandled = false;
        //                     if($this->refreshToken()) {
        //                         $response = $this->request($path, $method, $data);
        //                     } else $exception = new exception\AuthenticationException('Refresh token failed');
        //                     break;
        //                 }
        //             }
        //         }

        //         if($notHandled) {
        //             $errors = array();
        //             foreach($response['errors'] as $error) { 
        //                 $errors[] = $error['message'];
        //             }
        //             $exception = new exception\RequestException(implode("\n", $errors));
        //         }
        //     } else if($httpCode != 200) {
        //         $exception = new exception\RequestException('Unknown Error: '.$httpCode);
        //     }
		// } else {
        //     $this->log(curl_error($curl));
        //     $exception = new exception\RequestException('Unknown Curl Error: '.curl_errno($curl));
		// }
		
        // curl_close($curl);
        
        // if($exception) throw $exception;
        // else return $response;
    }

    private function getAuthorization() {
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