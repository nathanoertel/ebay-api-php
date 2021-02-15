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
namespace eBayAPI\trading;

abstract class AbstractRequest {
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    const SITE_ID = array(
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
                ),
                'ssl' => array(
                  'verify_peer' => false
                )
            ))
        );

        $client = new \SoapClient('https://developer.ebay.com/webservices/latest/ebaysvc.wsdl', $mode);

        $client->__setLocation('https://'.$this->environment->getAPIEndpoint().'/wsapi?'.http_build_query(array(
            'callname' => $operation,
            'version' => $this->version,
            'siteid' => self::SITE_ID[$marketplaceId]
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
            $this->log($client->__getLastRequestHeaders());
            $this->log($client->__getLastRequest());
            $this->log($client->__getLastResponseHeaders());
            $this->log($client->__getLastResponse());
            throw $e;
        } catch(\Exception $e) {
            if($e->getMessage() == 'Expired IAF token.') {
                $this->refreshToken();
                return $this->request($operation, $marketplaceId, $data);
            } else {
                $this->log($client->__getLastRequestHeaders());
                $this->log($client->__getLastRequest());
                $this->log($client->__getLastResponseHeaders());
                $this->log($client->__getLastResponse());
                throw $e;
            }
        }
    }

    private function getAuthorization() {
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