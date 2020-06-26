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

class FulfillmentRequest extends AbstractRequest {
    public function getOrder($orderId) {
        return parent::get('/sell/fulfillment/v1/order/'.$orderId);
    }

    public function getOrders($parameters) {
        return parent::get('/sell/fulfillment/v1/order', $parameters);
    }

    public function issueRefund($orderId, $refund) {
        return parent::post('/sell/fulfillment/v1/order/'.$orderId.'/issue_refund', $refund);
    }
}