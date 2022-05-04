<?php

/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * 'copyright.txt' FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/


use Tygh\Languages\Languages;
use Tygh\Enum\ObjectStatuses;

defined('BOOTSTRAP') or die('Access denied');

function fn_get_department_data($department_id = 0, $lang_code = CART_LANGUAGE)
{
    $department = [];
    if (!empty($department_id)) {
        list($departments) = fn_get_departments([
            'department_id' => $department_id
        ], 1, $lang_code);
        if (!empty($departments)) {
            $department = reset($departments);
            $department['member_user_ids'] = implode(',', fn_department_get_links($department['department_id']));
        }
    }
    return $department;
}

function fn_get_departments($params = [], $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
    // Set default values to input params
    $default_params = [
        'page' => 1,
        'items_per_page' => $items_per_page
    ];

    $params = array_merge($default_params, $params);

    AREA === 'C' ? [ObjectStatuses::ACTIVE] : [];

    $sortings = [
        'position' => '?:departments.position',
        'name' => '?:department_description.department',
        'status' => '?:departments.status',
    ];

    $condition = $limit = $join = '';

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    $sorting = db_sort($params, $sortings, 'name', 'asc');

    if (!empty($params['item_ids'])) {
        $condition .= db_quote(' AND ?:departments.department_id IN (?n)', explode(',', $params['item_ids']));
    }

    if (!empty($params['name'])) {
        $condition .= db_quote(' AND ?:department_description.department LIKE ?l', '%' . trim($params['name']) . '%');
    }

    if (!empty($params['department_id'])) {
        $condition .= db_quote(' AND ?:departments.department_id = ?i ', $params['department_id']);
    }

    if (!empty($params['user_id'])) {
        $condition .= db_quote(' AND ?:departments.user_id = ?i ', $params['user_id']);
    }

    if (!empty($params['status'])) {
        $condition .= db_quote(' AND ?:departments.status = ?s', $params['status']);
    }

    $fields = [
        '?:departments.*',
        '?:department_description.department',
        '?:department_description.description',
    ];

    $join .= db_quote('LEFT JOIN ?:department_description ON ?:department_description.department_id = ?:departments.department_id AND ?:department_description.lang_code = ?s', $lang_code);

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field('SELECT COUNT(*) FROM ?:departments ?p WHERE 1 ?p', $join, $condition);
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }

    $departments = db_get_hash_array(
        'SELECT ?p FROM ?:departments ' .
            $join .
            'WHERE 1 ?p ?p ?p',
        'department_id',
        implode(', ', $fields),
        $condition,
        $sorting,
        $limit
    );

    
    $department_image_ids = array_keys($departments);
    $images = fn_get_image_pairs($department_image_ids, 'department', 'M', true, false, $lang_code);
    
    foreach ($departments as $department_id => $department) {
        $departments[$department_id]['main_pair'] = !empty($images[$department_id]) ? reset($images[$department_id]) : [];
    }

    return [$departments, $params];
}

function fn_update_department($data, $department_id, $lang_code = DESCR_SL)
{
    if (isset($data['timestamp'])) {
        $data['timestamp'] = fn_parse_date($data['timestamp']);
    }

    if (!empty($department_id)) {
        db_query('UPDATE ?:departments SET ?u WHERE department_id = ?i', $data, $department_id);
        db_query('UPDATE ?:department_description SET ?u WHERE department_id = ?i AND lang_code = ?s', $data, $department_id, $lang_code);
    } else {
        $department_id = $data['department_id'] = db_replace_into('departments', $data);

        foreach (Languages::getAll() as $data['lang_code']) {
            db_query('REPLACE INTO ?:department_description ?e', $data);
        }
    }
    if (!empty($department_id)) {
        fn_attach_image_pairs('department', 'department', $department_id, $lang_code);
    }
    if (!empty($data['member_user_ids'])) {
        $member_user_ids =  $data['member_user_ids'];
        fn_department_delete_links($department_id);
        fn_department_add_links($department_id, $member_user_ids);
    }

    return $department_id;
}

function fn_delete_department($department_id)
{
    if (!empty($department_id)) {
        $res = db_query('DELETE FROM ?:departments WHERE department_id = ?i', $department_id);
        db_query('DELETE FROM ?:department_description WHERE department_id = ?i', $department_id);
        fn_department_delete_links($department_id);
    }
}

function fn_department_delete_links($department_id)
{
    db_query('DELETE FROM ?:department_links WHERE department_id = ?i', $department_id);
}

function fn_department_add_links($department_id, $member_user_ids)
{
    if (!empty($member_user_ids)) {
        $member_user_ids = explode(',', $member_user_ids);
        foreach ($member_user_ids as $member_user_id) {
            db_query('REPLACE INTO ?:department_links ?e', [
                'member_user_id' => $member_user_id,
                'department_id' => $department_id
            ]);
        }
    }
}

function fn_department_get_links($department_id)
{
    return !empty($department_id) ? db_get_fields('SELECT member_user_id FROM ?:department_links WHERE department_id = ?i', $department_id) : [];
}

function fn_compare_departments_position($department, $next_department)
{
    if ($department['position'] === $next_department['position']) {
        return 0;
    }
    return ($department['position'] < $next_department['position']) ? -1 : 1;
}
