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

class InventoryRequest extends AbstractRequest {
    const CONDITION_ENUM = array(
        1000 => 'NEW',
        2750 => 'LIKE_NEW',
        1500 => 'NEW_OTHER',
        1750 => 'NEW_WITH_DEFECTS',
        2000 => 'MANUFACTURER_REFURBISHED',
        2500 => 'SELLER_REFURBISHED',
        3000 => 'USED_EXCELLENT',
        4000 => 'USED_VERY_GOOD',
        5000 => 'USED_GOOD',
        6000 => 'USED_ACCEPTABLE',
        7000 => 'FOR_PARTS_OR_NOT_WORKING'
    );

    public function getInventoryLocations($parameters = array()) {
        $result = parent::get('/sell/inventory/v1/location', $parameters);
        return $result['body'];
    }

    public function getInventoryLocation($merchantLocationKey) {
        $result = parent::get('/sell/inventory/v1/location/'.$merchantLocationKey);
        return $result['body'];
    }

    public function createInventoryLocation($merchantLocationKey, $locationData) {
        $result = parent::post('/sell/inventory/v1/location/'.$merchantLocationKey, $locationData);
        return $result['body'];
    }

    public function updateInventoryLocation($merchantLocationKey, $locationData) {
        $result = parent::post('/sell/inventory/v1/location/'.$merchantLocationKey.'/update_location_details', $locationData);
        return $result['body'];
    }

    public function createOrReplaceInventoryItem($sku, $inventoryItem) {
        $result = parent::put('/sell/inventory/v1/inventory_item/'.$sku, $inventoryItem);
        return $result['body'];
    }

    public function getInventoryItem($sku) {
        $result = parent::get('/sell/inventory/v1/inventory_item/'.$sku);
        return $result['body'];
    }

    public function getInventoryItems($limit, $offset) {
        $result = parent::get('/sell/inventory/v1/inventory_item', array(
            'limit' => $limit,
            'offset' => $offset
        ));
        return $result['body'];
    }

    public function deleteInventoryItem($sku) {
        $result = parent::delete('/sell/inventory/v1/inventory_item/'.$sku);
        return $result['body'];
    }

    public function bulkUpdatePriceQuantity($requests) {
        $result = parent::post('/sell/inventory/v1/bulk_update_price_quantity', array(
            'requests' => $requests
        ));
        return $result['body'];
    }

    public function createOrReplaceInventoryItemGroup($inventoryItemGroupKey, $inventoryItemGroup) {
        $result = parent::put('/sell/inventory/v1/inventory_item_group/'.$inventoryItemGroupKey, $inventoryItemGroup);
        return $result['body'];
    }

    public function getInventoryItemGroup($inventoryItemGroupKey) {
        $result = parent::get('/sell/inventory/v1/inventory_item_group/'.$inventoryItemGroupKey);
        return $result['body'];
    }

    public function deleteInventoryItemGroup($inventoryItemGroupKey) {
        $result = parent::delete('/sell/inventory/v1/inventory_item_group/'.$inventoryItemGroupKey);
        return $result['body'];
    }

    public function createOffer($offer) {
        $result = parent::post('/sell/inventory/v1/offer', $offer);
        return $result['body'];
    }

    public function updateOffer($offerId, $offer) {
        $result = parent::put('/sell/inventory/v1/offer/'.$offerId, $offer);
        return $result['body'];
    }

    public function getOffers($sku, $limit, $offset) {
        $result = parent::get('/sell/inventory/v1/offer', array(
            'sku' => $sku,
            'limit' => $limit,
            'offset' => $offset
        ));
        return $result['body'];
    }

    public function getOffer($offerId) {
        $result = parent::get('/sell/inventory/v1/offer/'.$offerId);
        return $result['body'];
    }

    public function publishOffer($offerId) {
        $result = parent::post('/sell/inventory/v1/offer/'.$offerId.'/publish');
        return $result['body'];
    }

    public function deleteOffer($offerId) {
        $result = parent::delete('/sell/inventory/v1/offer/'.$offerId);
        return $result['body'];
    }

    public function withdrawOffer($offerId) {
        $result = parent::post('/sell/inventory/v1/offer/'.$offerId.'/withdraw');
        return $result['body'];
    }

    public function bulkMigrateListing($listingIds) {
        $request = array();

        foreach($listingIds as $listingId) {
            $request[] = array(
                'listingId' => $listingId
            );
        }

        $result = parent::post('/sell/inventory/v1/bulk_migrate_listing', array(
            'requests' => $request
        ));
        return $result['body'];
    }
}