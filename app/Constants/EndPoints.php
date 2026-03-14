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
     * Middeleware Route
     * ========================================================================
     */
    const unauthorised = 'unauthorised';
    const adminaccess = 'adminaccess';
    const activeaccess = 'activeaccess';
    const password_reset = 'password/reset';
    const password_update = 'password/update';

}
