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

class CategoryRequest extends AbstractRequest {
    public function getDefaultCategoryTreeId($marketplaceId) {
        return parent::get('/commerce/taxonomy/v1_beta/get_default_category_tree_id', array(
            'marketplace_id' => $marketplaceId
        ));
    }

    public function getCategoryTree($categoryTreeId) {
        return parent::get('/commerce/taxonomy/v1_beta/category_tree/'.$categoryTreeId);
    }

    public function getCategorySubtree($categoryTreeId, $categoryId) {
        return parent::get('/commerce/taxonomy/v1_beta/category_tree/'.$categoryTreeId.'/get_category_subtree', array(
            'category_id' => $categoryId
        ));
    }

    public function getItemAspectsForCategory($categoryTreeId, $categoryId) {
        return parent::get('/commerce/taxonomy/v1_beta/category_tree/'.$categoryTreeId.'/get_item_aspects_for_category', array(
            'category_id' => $categoryId
        ));
    }
}