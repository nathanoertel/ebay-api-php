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

class StoreRequest extends AbstractRequest {
    public function getAccount($marketplaceId) {
        return parent::request('GetAccount', $marketplaceId);
    }

    public function relistFixedPriceItem($marketplaceId, $itemId) {
        return parent::request('RelistFixedPriceItem', $marketplaceId, array(
            'Item' => array(
                'ItemID' => $itemId
            )
        ));
    }

    public function reviseInventoryStatus($marketplaceId, $itemId, $quantity) {
        return parent::request('ReviseInventoryStatus', $marketplaceId, array(
            'InventoryStatus' => array(
                'ItemID' => $itemId,
                'Quantity' => $quantity
            )
        ));
    }

    public function getStore($marketplaceId, $data) {
        return parent::request('GetStore', $marketplaceId, $data);
    }
}