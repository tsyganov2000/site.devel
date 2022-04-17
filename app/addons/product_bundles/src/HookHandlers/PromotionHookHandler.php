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
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Addons\ProductBundles\HookHandlers;

use Tygh\Addons\ProductBundles\ServiceProvider;

class PromotionHookHandler
{
    /**
     * The `get_promotions` hook handler.
     *
     * Action performed:
     *     - Filtering promotions created for bundles from all promotions list.
     *
     * @param array<string> $params    Search params.
     * @param array<string> $fields    Selected fields.
     * @param array<string> $sortings  Sorting condition.
     * @param string        $condition Condition for selecting.
     * @param string        $join      Join tables.
     * @param string        $group     Grouping condition.
     * @param string        $lang_code Language code.
     *
     * @return void
     */
    public function onGetPromotions(array $params, array $fields, array $sortings, &$condition, $join, $group, $lang_code)
    {
        if (!empty($params['zone']) || !empty($params['promotion_id'])) {
            return;
        }
        $bundle_promotions = db_get_fields('SELECT linked_promotion_id FROM ?:product_bundles');
        if (empty($bundle_promotions)) {
            return;
        }
        $condition .= db_quote(' AND ?:promotions.promotion_id NOT IN (?n)', $bundle_promotions);
    }

    /**
     * The `promotions_apply_pre` hook handler.
     *
     * Action performed:
     *     - Multiply promotion bonuses for multiple bundles at the cart.
     *
     * @param array<string>      $promotions    List of promotions
     * @param string             $zone          Promotion zone (catalog, cart)
     * @param array<string>      $data          Data array (product - for catalog rules, cart - for cart rules)
     * @param array<string>|null $auth          Auth array (for car rules)
     * @param array<string, array<string|int>>|null $cart_products Cart products array (for car rules)
     *
     * @return void
     */
    public function onPrePromotionApply(array &$promotions, $zone, array $data, $auth, $cart_products)
    {
        if (empty($cart_products)) {
            return;
        }

        $bundle_promotions = db_get_hash_array('SELECT bundle_id, linked_promotion_id FROM ?:product_bundles', 'bundle_id');
        $cart_promotions = array_filter($promotions['cart'], static function ($promotion) use ($bundle_promotions) {
            return in_array($promotion['promotion_id'], array_column($bundle_promotions, 'linked_promotion_id'));
        });
        if (empty($cart_promotions)) {
            return;
        }
        uasort($cart_promotions, static function ($first, $second) {
            $first_bonus = reset($first['bonuses']);
            $second_bonus = reset($second['bonuses']);
            return $first_bonus['discount_value'] < $second_bonus['discount_value'];
        });

        $product_amounts = [];
        foreach ($cart_products as $product) {
            $product_amounts[$product['product_id']] = isset($product_amounts[$product['product_id']])
                ? $product_amounts[$product['product_id']] + $product['amount']
                : $product['amount'];
        }
        $bundle_service = ServiceProvider::getService();
        $bundle_ids = $bundle_service->checkForPotentialCompleteBundles($product_amounts, array_keys($cart_promotions));
        foreach ($bundle_ids as $bundle_id => $bundle_amount) {
            $promotion_id = $bundle_promotions[$bundle_id]['linked_promotion_id'];
            if ($bundle_amount) {
                list($bundle_data,) = $bundle_service->getBundles(['bundle_id' => $bundle_id, 'get_child_bundles' => true, 'full_info' => true]);
                if (empty($bundle_data)) {
                    continue;
                }
                $bundle_data = reset($bundle_data);
                $promotions['cart'][$promotion_id]['bonuses'][0]['discount_value'] = $bundle_amount *
                    ($bundle_data['total_price'] - $bundle_data['discounted_price']);
            }
        }
    }

    /**
     * The `pre_promotion_validate` hook handler.
     *
     * Action performed:
     *     - Checks promotion conditions if promotion linked to bundle and updates promotion bonus accordingly.
     *
     * @param int                               $promotion_id    Promotion ID
     * @param array<string>                     $promotion       Rule data
     * @param array<string>                     $data            Data array
     * @param bool                              $stop_validating Whether rule validity check should be interrupted
     * @param bool                              $result          Forced validity check result
     * @param array<string>|null                $auth            Auth array (for cart rules)
     * @param array<string, array<string>>|null $cart_products   Cart products array (for cart rules)
     *
     * @see fn_promotion_validate()
     *
     * @return void
     */
    public function onPrePromotionValidate($promotion_id, array $promotion, array $data, &$stop_validating, &$result, $auth, $cart_products)
    {
        if (empty($cart_products)) {
            return;
        }
        $bundle_service = ServiceProvider::getService();
        $linked_promotions = $bundle_service->getLinkedPromotions();

        if (empty($linked_promotions[$promotion_id])) {
            return;
        }

        $product_amounts = [];
        foreach ($cart_products as $product) {
            $product_amounts[$product['product_id']] = isset($product_amounts[$product['product_id']])
                ? $product_amounts[$product['product_id']] + $product['amount']
                : (int) $product['amount'];
        }
        $bundle_ids = $bundle_service->checkForPotentialCompleteBundles($product_amounts, [$promotion_id]);

        if (empty($bundle_ids)) {
            return;
        }
        foreach ($bundle_ids as $bundle_id => $bundle_amount) {
            if (!$bundle_amount) {
                $result = false;
                $stop_validating = true;
                continue;
            }
            list($bundle_data,) = $bundle_service->getBundles(['bundle_id' => $bundle_id, 'get_child_bundles' => true, 'full_info' => true]);
            if (empty($bundle_data)) {
                $result = false;
                $stop_validating = true;
                continue;
            }
            $bundle_data = reset($bundle_data);
            if ($bundle_service->checkCartForCompleteBundles($bundle_data, $cart_products)) {
                $result = true;
                $stop_validating = true;
                return;
            }
        }
    }
}
