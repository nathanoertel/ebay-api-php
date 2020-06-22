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

class AccessToken {
    private $accessToken;

    private $refreshToken;

    private $expires;

    private $updated = false;

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function getRefreshToken() {
        return $this->refreshToken;
    }

    public function getExpires() {
        return $this->expires;
    }

    public function isExpired() {
        return $this->expires < time();
    }

    public function setToken($accessToken, $expires) {
        $this->accessToken = $accessToken;
        $this->expires = $expires;
        $this->updated = true;
    }

    public function isUpdated() {
        return $this->updated;
    }

    public function __construct($accessToken, $refreshToken, $expires) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expires = $expires;
    }
}
?>