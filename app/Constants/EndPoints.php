<?php


namespace App\Constants;


class EndPoints
{
    /**
     * ========================================================================
     * Users Services
     * ========================================================================
     */
    const user_register = '/user/register';
    const user_login = '/user/login';
    const user_forgotPassword  = '/user/forgotPassword';
    const user_verifyOtp        = '/user/verifyOtp';
    const user_resetPassword    = '/user/resetPassword';
    const user_forgotPasswordCustom = '/user/forgotPasswordCustom';
    const user_adminUsers = '/user/adminUsers';
    const user_detail = '/user/detail';
    const user_profile        = '/user/profile';
    const user_changePassword = '/user/changePassword';
    const user_updateProfile  = '/user/updateProfile';
    const user_logout         = '/user/logout';
    const user_list = '/user/list';
    const user_activeUsers = '/user/activeUsers';
    const user_newUsers = '/user/newUsers';
    const user_blockUsers = '/user/blockUsers';


    /**
     * ========================================================================
     * Master Category Services
     * ========================================================================
     */
    const master_category_list   = '/master-category/list';
    const master_category_store  = '/master-category/store';
    const master_category_show   = '/master-category/show/{id}';
    const master_category_update = '/master-category/update/{id}';
    const master_category_delete = '/master-category/delete/{id}';

    /**
     * ========================================================================
     * Master Unit Services
     * ========================================================================
     */
    const master_unit_list   = '/master-unit/list';
    const master_unit_store  = '/master-unit/store';
    const master_unit_show   = '/master-unit/show/{id}';
    const master_unit_update = '/master-unit/update/{id}';
    const master_unit_delete = '/master-unit/delete/{id}';

    /**
     * ========================================================================
     * Product Services
     * ========================================================================
     */
    const product_list   = '/product/list';
    const product_store  = '/product/store';
    const product_show   = '/product/show/{id}';
    const product_update = '/product/update/{id}';
    const product_delete = '/product/delete/{id}';

    /**
     * ========================================================================
     * Receipt Services
     * ========================================================================
     */
    const receipt_list   = '/receipt/list';
    const receipt_store  = '/receipt/store';
    const receipt_show   = '/receipt/show/{id}';
    const receipt_update = '/receipt/update/{id}';
    const receipt_delete = '/receipt/delete/{id}';

    /**
     * ========================================================================
     * Delivery Order Services
     * ========================================================================
     */
    const delivery_order_list         = '/delivery-order/list';
    const delivery_order_store        = '/delivery-order/store';
    const delivery_order_show         = '/delivery-order/show/{id}';
    const delivery_order_update       = '/delivery-order/update/{id}';
    const delivery_order_delete       = '/delivery-order/delete/{id}';
    const delivery_order_changeStatus = '/delivery-order/change-status/{id}';

    /**
     * ========================================================================
     * Warehouse Services
     * ========================================================================
     */
    const warehouse_list   = '/warehouse/list';
    const warehouse_store  = '/warehouse/store';
    const warehouse_show   = '/warehouse/show/{id}';
    const warehouse_update = '/warehouse/update/{id}';
    const warehouse_delete = '/warehouse/delete/{id}';

    /**
     * ========================================================================
     * Internal Transfer Services
     * ========================================================================
     */
    const internal_transfer_list         = '/internal-transfer/list';
    const internal_transfer_store        = '/internal-transfer/store';
    const internal_transfer_show         = '/internal-transfer/show/{id}';
    const internal_transfer_update       = '/internal-transfer/update/{id}';
    const internal_transfer_delete       = '/internal-transfer/delete/{id}';
    const internal_transfer_changeStatus = '/internal-transfer/change-status/{id}';

    /**
     * ========================================================================
     * Stock Adjustment Services
     * ========================================================================
     */
    const stock_adjustment_list         = '/stock-adjustment/list';
    const stock_adjustment_store        = '/stock-adjustment/store';
    const stock_adjustment_show         = '/stock-adjustment/show/{id}';
    const stock_adjustment_update       = '/stock-adjustment/update/{id}';
    const stock_adjustment_delete       = '/stock-adjustment/delete/{id}';
    const stock_adjustment_changeStatus = '/stock-adjustment/change-status/{id}';

    /**
     * ========================================================================
     * Stock Ledger Services
     * ========================================================================
     */
    const stock_ledger_list = '/stock-ledger/list';

    /**
     * ========================================================================
     * Dashboard Services
     * ========================================================================
     */
    const dashboard = '/dashboard';

    /**
     * ========================================================================
     * Middeleware Route
     * ========================================================================
     */
    const unauthorised = 'unauthorised';
    const adminaccess = 'adminaccess';
    const activeaccess = 'activeaccess';
    const password_reset = 'password/reset';
    const password_update = 'password/update';

}
