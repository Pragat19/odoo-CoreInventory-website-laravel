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
    const user_profile = '/user/profile';
    const user_changePassword = '/user/changePassword';
    const user_updateProfile = '/user/updateProfile';
    const user_logout = '/user/logout';
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
     * Middeleware Route
     * ========================================================================
     */
    const unauthorised = 'unauthorised';
    const adminaccess = 'adminaccess';
    const activeaccess = 'activeaccess';
    const password_reset = 'password/reset';
    const password_update = 'password/update';

}
