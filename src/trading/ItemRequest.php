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

class ItemRequest extends AbstractRequest {
    public function getMyeBaySelling($marketplaceId, $data) {
        return parent::request('GetMyeBaySelling', $marketplaceId, $data);
    }
    
    public function getSKUItemIDs($marketplaceId, $sku, $active = true) {
        $time = time();
        $page = 1;
        $itemIds = array();
        $request = array(
            'SKUArray' => array(
                'SKU' => array(
                    $sku
                )
            ),
           'Pagination' => array(
                'EntriesPerPage' => 200,
                'PageNumber' => $page
            )
        );

        if($active) {
            $request['EndTimeFrom'] = $this->getUTCDate($time);
            $request['EndTimeTo'] = $this->getUTCDate($time + (60 * 60 * 24 * 120));
        } else {
            $request['EndTimeFrom'] = $this->getUTCDate($time - (60 * 60 * 24 * 120));
            $request['EndTimeTo'] = $this->getUTCDate($time);
        }

        do {
            $request['Pagination']['PageNumber'] = $page;
            $items = parent::request('GetSellerList', $marketplaceId, $request);

            if($items->PaginationResult->TotalNumberOfEntries > 0) {
                if(isset($items->ItemArray->Item->ItemID)) $itemIds[] = $items->ItemArray->Item->ItemID;
                else {
                    foreach($items->ItemArray->Item as $item) {
                        $itemIds[] = $item->ItemID;
                    }
                }
            }
            
            $page++;
        } while($items->PaginationResult->TotalNumberOfPages >= $page);

        return $itemIds;
    }
    
    public function getItem($marketplaceId, $data) {
        return parent::request('GetItem', $marketplaceId, $data);
    }

    public function endItem($marketplaceId, $itemID, $reason = 'NotAvailable') {
        return parent::request('EndItem', $marketplaceId, array(
            'ItemID' => $itemID,
            'EndingReason' => $reason
        ));
    }

    public function addFixedPriceItem($marketplaceId, $data) {
        return parent::request('AddFixedPriceItem', $marketplaceId, array(
            'Item' => $data
        ));
    }

    public function reviseFixedPriceItem($marketplaceId, $data) {
        return parent::request('ReviseFixedPriceItem', $marketplaceId, array(
            'Item' => $data
        ));
    }

    public function relistFixedPriceItem($marketplaceId, $data) {
        return parent::request('RelistFixedPriceItem', $marketplaceId, array(
            'Item' => $data
        ));
    }

	private function getUTCDate($timestamp) {
		$utcTimezone = new \DateTimeZone("UTC");
		$timezone = new \DateTimeZone(date_default_timezone_get());
		
		$time = new \DateTime();
		$time->setTimezone($timezone);
		$time->setTimestamp($timestamp);
		$time->setTimezone($utcTimezone);

		return $time->format("Y-m-d\\TH:i:s.000\\Z");
	}
}