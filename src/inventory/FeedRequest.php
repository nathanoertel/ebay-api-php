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

class FeedRequest extends AbstractRequest {
    public const ORDER_TASK = 'order_task';
    public const INVENTORY_TASK = 'inventory_task';

    public function createInventoryTask($filterCriteria = null) {
        $data = [
            'feedType' => 'LMS_ACTIVE_INVENTORY_REPORT',
            'schemaVersion' => '1.0'
        ];

        if (!empty($filterCriteria)) {
            $data['filterCriteria'] = $filterCriteria;
        }

        $result = parent::post('/sell/feed/v1/inventory_task', $data);

        $location = $result['headers']['location'];
        $parts = explode('/', $location);
        return array_pop($parts);
    }

    public function getInventoryTask($taskId) {
        $result = parent::get('/sell/feed/v1/inventory_task/' . $taskId);

        return $result['body'];
    }

    public function getFile($taskId) {
        $result = parent::get("/sell/feed/v1/task/$taskId/download_result_file");

        return $result;
    }
}