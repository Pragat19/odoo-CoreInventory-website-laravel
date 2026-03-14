<?php


namespace App\Constants;


class ControllerMethods
{
    // User Auth
    const register = '@register';
    const login = '@login';
    const changePassword = '@changePassword';
    const forgotPassword = '@forgotPassword';
    const forgotPasswordCustom = '@forgotPasswordCustom';
    const resetPassword = '@resetPassword';
    const updatePassword = '@updatePassword';
    const logout = '@logout';

    // Users
    const profile = '@profile';
    const updateProfile = '@updateProfile';
    const adminUsers = '@adminUsers';
    const activeUsers = '@activeUsers';
    const newUsers = '@newUsers';
    const blockUsers = '@blockUsers';
    const unauthorised = '@unauthorised';
    const adminaccess = '@adminaccess';
    const activeaccess = '@activeaccess';

    // Common
    const add = '@add';
    const update = '@update';
    const delete = '@delete';
    const list = '@list';
    const detail = '@detail';
}
