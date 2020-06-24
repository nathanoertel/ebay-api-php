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

class AccountRequest extends AbstractRequest {
    public function getFulfillmentPolicies($marketplaceId) {
        return parent::get('/sell/account/v1/fulfillment_policy', array(
            'marketplace_id' => $marketplaceId
        ));
    }

    public function getReturnPolicies($marketplaceId) {
        return parent::get('/sell/account/v1/return_policy', array(
            'marketplace_id' => $marketplaceId
        ));
    }

    public function getPaymentPolicies($marketplaceId) {
        return parent::get('/sell/account/v1/payment_policy', array(
            'marketplace_id' => $marketplaceId
        ));
    }
}