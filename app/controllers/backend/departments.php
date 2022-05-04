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

use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $suffix = '';

    // Define trusted variables that shouldn't be stripped
    fn_trusted_vars(
        'department_data'
    );

    if ($mode === 'update_department') {
        $department_id = !empty($_REQUEST['department_id']) ? $_REQUEST['department_id'] : 0;
        $data = !empty($_REQUEST['department_data']) ? $_REQUEST['department_data'] : [];
        $department_id = fn_update_department($data, $department_id);
        if (!empty($department_id)) {
            $suffix = '.update?department_id=' . $department_id;
        } else {
            $suffix = '.add';
        }
    } elseif ($mode === 'update_departments') {
        if (!empty($_REQUEST['departments_data'])) {
            foreach ($_REQUEST['departments_data'] as $department_id => $data) {
                fn_update_department($data, $department_id);
            }
        }
        $suffix = '.manage';
    } elseif ($mode === 'delete_department') {
        $department_id = !empty($_REQUEST['department_id']) ? $_REQUEST['department_id'] : 0;
        fn_delete_department($department_id);
        $suffix = '.manage';
    } elseif ($mode === 'delete_departments') {
        if (!empty($_REQUEST['departments_ids'])) {
            foreach ($_REQUEST['departments_ids'] as $department_id) {
                fn_delete_department($department_id);
            }
        }
        $suffix = '.manage';
    }

    return [CONTROLLER_STATUS_OK, 'departments' . $suffix];
}
if ($mode === 'add' || $mode === 'update') {
    $department_id = !empty($_REQUEST['department_id']) ? $_REQUEST['department_id'] : 0;
    $department_data = fn_get_department_data($department_id, DESCR_SL);

    if (empty($department_data) && $mode === 'update') {
        return [CONTROLLER_STATUS_NO_PAGE];
    }
    Tygh::$app['view']->assign([
        'department_data' => $department_data,
        'head_user_info' => !empty($department_data['user_id']) ? fn_get_user_short_info($department_data['user_id']) : [],
        'member_user_info' => !empty($department_data['member_user_ids']) ? fn_get_user_short_info($department_data['member_user_ids']) : [],
    ]);
} elseif ($mode === 'manage') {

    list($departments, $search) = fn_get_departments($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'), DESCR_SL);

    Tygh::$app['view']->assign([
        'departments' => $departments,
        'search' => $search
    ]);
}
