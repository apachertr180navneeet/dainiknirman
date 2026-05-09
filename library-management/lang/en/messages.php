<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global Messages Language
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the app to show the message.
    |
     */

    // Records
    'record_found' => ':Record found.',
    'record_not_found' => 'The :record you are looking for could not be found.',
    

    'records_found' => ':Records found.',
    'records_not_found' => ':Records not available.',

    'record_created' => ':Record created successfully.',
    'record_creation_failed' => 'Unable to create :record, Please try again!.',

    'records_updated' => ':Record Updated successfully.',
    'records_updation_failed' => 'Unable to update :record, Please try again!.',

    'records_saved' => ':Records saved successfully.',
    'records_saving_failed' => 'Unable to save :records, Please try again!.',

    'record_saved' => ':Record saved successfully.',
    'record_saving_failed' => 'Unable to save :record, Please try again!.',

    'status_changed' => 'Status changed successfully.',
    'status_change_failed' => 'Unable to change status, Please try again!.',

    'record_deleted' => ':Record (s) deleted successfully.',
    'record_deletion_failed' => 'Unable to delete :records, Please try again!.',

    'default_destroy_failed' => 'Default :Records cannot be deleted.',

    'record_import' => ':Record (s) import successfully .',
    'record_import_failed' => 'Unable to import :records, Please try again!.',

    'record_image_deleted' => ':Record image remove successfully.',
    'record_image_deleted_failed' => 'Unable to remove :record image, Please try again!.',

    'record_upload' => ':Record (s) upload successfully .',
    'record_upload_failed' => 'Unable to upload :records, Please try again!.',

    'order_updated' => 'Order updated successfully.',
    'order_update_failed' => 'Unable to update order, Please try again!.',

    'record_unblocked' => ':Record(s) unblocked successfully.',
    'record_unblock_failed' => 'Unable to unblock :records, Please try again!.',


    // Success and failure
    'record_success' => ':Record successfully.',
    'record_failed' => 'Unable to :records, Please try again!.',
    //--------------------

    // Duplicate
    'duplicate_name' => ':Record already exists, Please try again with different :Record!',
    'duplicate_cart' => ':Record already exists in cart, Please try again with different :Record!',
    //--------------------

    //Requests
    'already_pending_request' => "Request cannot be sent, you already have a pending request.",
    //--------

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Messages Language
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the app to show the message.
    |
     */

    // Profile
    'profile_updation_failed' => 'Unable to update profile, Please try again!.',
    'profile_updated' => 'Profile updated successfully.',
    //--------

    // Change Password
    'password_updation_failed' => 'Unable to change password, Please try again!.',
    'password_updated' => 'Password changed successfully.',
    'password_not_matched' => 'Incorrect current password, Please try again!.',
    //--------

    // Image Upload
    'image_uploaded' => 'Image uploaded successfully.',
    'image_uploading_failed' => 'Unable to upload image, Please try again!.',
    //-------------

    // User
    //--------

    // Papers
    'add_section_error' => 'Please select category & paper type before add sections.',
    //-------

    // Package
    'add_category_error' => 'Please select category & package type before add papers.',
    //--------

    /*
    |--------------------------------------------------------------------------
    | API Messages Language
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the app to show the message.
    |
     */

    // Auth
    'logged_in' => 'Login successfully.',
    'account_pending' => 'Your account has been currently pending for approval.',
    'account_decline' => 'Your account has been Declined. Please contact to support.',
    'account_inactive' => 'Your account has been deactivated. Please contact Support team.',
    'account_created' => 'Account created.',
    'account_creation_failed' => 'Unable create an account, Please try again!',
    'invalid_one_time_password' => 'You\'ve entered an incorrect OTP, please enter the correct OTP to continue.',
    'one_time_password_required' => 'Please enter a valid one time password.',
    'otp_creation_failed' => 'Unable to send OTP, Please check your details and please try again!',
    'otp_send_successfully' => 'We have sent you a OTP, Please enter your one time password in it.',
    'logged_out' => 'You have been logged out.',
    'logging_out_failed' => 'Unable to logout, Please try again!',
    'social_login_failed' => 'Unable to login, Please try again!',

    'something_went_wrong' => 'Oops something went wrong, please try again.',

    'email_send_successfully' => 'E-mail sent successfully.',
    'email_send_failed' => 'Unable to send E-mail',
    'contactus_email_send_successfully' => 'Request sent successfully.',
    'contactus_email_send_failed' => 'Unable to send request.',
];