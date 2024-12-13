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

class FulfillmentRequest extends AbstractRequest {
    public function getOrder($orderId) {
        $result = parent::get('/sell/fulfillment/v1/order/'.$orderId);
        return $result['body'];
    }

    public function getOrders($parameters) {
        $result = parent::get('/sell/fulfillment/v1/order', $parameters);
        return $result['body'];
    }

    public function issueRefund($orderId, $refund) {
        $result = parent::post('/sell/fulfillment/v1/order/'.$orderId.'/issue_refund', $refund);
        return $result['body'];
    }

    public function getShippingFulfillments($orderId) {
        $result = parent::get('/sell/fulfillment/v1/order/'.$orderId.'/shipping_fulfillment');
        return $result['body'];
    }

    public function getShippingFulfillment($orderId, $shippingFulfillmentId) {
        $result = parent::get('/sell/fulfillment/v1/order/'.$orderId.'/shipping_fulfillment/'.$shippingFulfillmentId);
        return $result['body'];
    }

    public function createShippingFulfillment($orderId, $shippingFulfillment) {
        $result = parent::post('/sell/fulfillment/v1/order/'.$orderId.'/shipping_fulfillment', $shippingFulfillment);
        return $result['body'];
    }
}