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
        return parent::get('/sell/inventory/v1/location', $parameters);
    }

    public function getInventoryLocation($merchantLocationKey) {
        return parent::get('/sell/inventory/v1/location/'.$merchantLocationKey);
    }

    public function createInventoryLocation($merchantLocationKey, $locationData) {
        return parent::post('/sell/inventory/v1/location/'.$merchantLocationKey, $locationData);
    }

    public function updateInventoryLocation($merchantLocationKey, $locationData) {
        return parent::post('/sell/inventory/v1/location/'.$merchantLocationKey.'/update_location_details', $locationData);
    }

    public function createOrReplaceInventoryItem($sku, $inventoryItem) {
        return parent::put('/sell/inventory/v1/inventory_item/'.$sku, $inventoryItem);
    }

    public function getInventoryItem($sku) {
        return parent::get('/sell/inventory/v1/inventory_item/'.$sku);
    }

    public function getInventoryItems($limit, $offset) {
        return parent::get('/sell/inventory/v1/inventory_item', array(
            'limit' => $limit,
            'offset' => $offset
        ));
    }

    public function deleteInventoryItem($sku) {
        return parent::delete('/sell/inventory/v1/inventory_item/'.$sku);
    }

    public function bulkUpdatePriceQuantity($requests) {
        return parent::post('/sell/inventory/v1/bulk_update_price_quantity', array(
            'requests' => $requests
        ));
    }

    public function createOrReplaceInventoryItemGroup($inventoryItemGroupKey, $inventoryItemGroup) {
        return parent::put('/sell/inventory/v1/inventory_item_group/'.$inventoryItemGroupKey, $inventoryItemGroup);
    }

    public function getInventoryItemGroup($inventoryItemGroupKey) {
        return parent::get('/sell/inventory/v1/inventory_item_group/'.$inventoryItemGroupKey);
    }

    public function deleteInventoryItemGroup($inventoryItemGroupKey) {
        return parent::delete('/sell/inventory/v1/inventory_item_group/'.$inventoryItemGroupKey);
    }

    public function createOffer($offer) {
        return parent::post('/sell/inventory/v1/offer', $offer);
    }

    public function updateOffer($offerId, $offer) {
        return parent::put('/sell/inventory/v1/offer/'.$offerId, $offer);
    }

    public function getOffers($sku, $limit, $offset) {
        return parent::get('/sell/inventory/v1/offer', array(
            'sku' => $sku,
            'limit' => $limit,
            'offset' => $offset
        ));
    }

    public function getOffer($offerId) {
        return parent::get('/sell/inventory/v1/offer/'.$offerId);
    }

    public function publishOffer($offerId) {
        return parent::post('/sell/inventory/v1/offer/'.$offerId.'/publish');
    }

    public function deleteOffer($offerId) {
        return parent::delete('/sell/inventory/v1/offer/'.$offerId);
    }

    public function withdrawOffer($offerId) {
        return parent::post('/sell/inventory/v1/offer/'.$offerId.'/withdraw');
    }

    public function bulkMigrateListing($listingIds) {
        $request = array();

        foreach($listingIds as $listingId) {
            $request[] = array(
                'listingId' => $listingId
            );
        }

        return parent::post('/sell/inventory/v1/bulk_migrate_listing', array(
            'requests' => $request
        ));
    }
}