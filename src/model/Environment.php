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
namespace eBayAPI\model;

class Environment {
    const ENVIRONMENT_ENDPOINTS = array(
        'sandbox' => array(
            'api' => 'api.sandbox.ebay.com',
            'auth' => 'https://auth.sandbox.ebay.com/oauth2/authorize',
            'token' => 'https://api.sandbox.ebay.com/identity/v1/oauth2/token'
        ),
        'live' => array(
            'api' => 'api.ebay.com',
            'auth' => 'https://auth.ebay.com/oauth2/authorize',
            'token' => 'https://api.ebay.com/identity/v1/oauth2/token'
        )
    );

    private $env;

    public function getAuthEndpoint() {
        return self::ENVIRONMENT_ENDPOINTS[$this->env]['auth'];
    }

    public function getAPIEndpoint() {
        return self::ENVIRONMENT_ENDPOINTS[$this->env]['api'];
    }

    public function __construct($env) {
        $this->env = $env;
    }
}
?>