<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| AddChat Configurations
|--------------------------------------------------------------------------
|
|
*/

$config['addchat']			=  (object) [

	/*
    |--------------------------------------------------------------------------
    | Logged-in user-id Session Key
    |--------------------------------------------------------------------------
    |
    | Enter the $_SESSION variable key name in which your application stores
    | the logged-in user id e.g $_SESSION['user_id'] then enter 'user_id'
    | 
    |
    */
    'session_user_id'     => 'user_id',
    

    

    /**
     * --------------------------------------------------------------------------
     * Brand Identity
     * --------------------------------------------------------------------------
     *
     * Add your website identity & branding to the AddChat widget. These changes 
     * are for the AddChat widget only and have nothing to do with the main website.
     * 
     * widget_name          : required
     * widget_logo          : required
     * widget_icon          : required
     * widget_user_avatar   : required
     * widget_notify_sound  : required
     * widget_footer_text   : optional
     * widget_footer_url    : optional
     * 
    */
    'widget_name'           => 'AddChat Codeigniter Pro',
    'widget_logo'           => 'addchat/img/addchat-logo-white.png',
    'widget_icon'           => 'addchat/img/addchat-shadow.png',
    'widget_user_avatar'    => 'addchat/img/avatar.png',
    'widget_notify_sound'   => 'addchat/sound/notification.mp3',
    'widget_footer_text'    => 'AddChat | by Classiebit',
    'widget_footer_url'     => 'https://classiebit.com/addchat-codeigniter-pro',

   
    /**
     * --------------------------------------------------------------------------
     * Upload Path
     * --------------------------------------------------------------------------
     *
     * AddChat uploads User profile pics & Messages attachments in 
     * storage public folder.
     * 
     * 
     * upload_path          : required
     * 
    */
    'upload_path'           => 'upload',
    

    /**
     * --------------------------------------------------------------------------
     * Users Table
     * --------------------------------------------------------------------------
     *
     * AddChat fetches your website existing users from the users table. If your
     * users table and columns names are something else, add them below.
     * 
     * users_table (required)
     * Users table name
     * 
     * users_col_id (required)
     * Users table column name of id
     * 
     * users_col_email (required)
     * Users table column name of email
     * 
    */

    'users_table'       => 'users',
    'users_col_id'      => 'id',
    'users_col_email'   => 'email',


    /**
     * --------------------------------------------------------------------------
     * Super Admin
     * --------------------------------------------------------------------------
     *
     * As of now, AddChat can have only one Super Admin 
     * (will make it to multiple Admins in upcoming versions)
     * 
     * admin_user_id (required)
     * Default Super Admin is the User with User-id = 1
     *  
     * Change it to any other value
     * e.g if you want to make the Super Admin with User-id = 5
     * Then change it to -
     *  admin_user_id = 5
     * 
    */

    'admin_user_id' => 1,

   
    /**
     * --------------------------------------------------------------------------
     * User Groups Table
     * --------------------------------------------------------------------------
     *
     * To enable AddChat multi-user groups feature. Your website database must have 
     * User-Groups table and a pivot table that connects User to a group.
     * 
     * AddChat also support a user belongs to multiple groups. 
     * Just mention those tables names below.
     * 
     * groups_table (optional)
     * User-groups table name
     * 
     * groups_col_id (optional)
     * User-groups table column name of id
     * 
     * groups_col_name (optional)
     * User-groups table column name of group name/title
     * 
     * ug_table (optional)
     * User-groups pivot table name
     * 
     * ug_col_user_id (optional)
     * User-groups pivot table column name of user-id
     * 
     * ug_col_group_id (optional)
     * User-groups pivot table column name of group-id
     * 
     * 
    */

    'groups_table'      => NULL,
    'groups_col_id'     => NULL,
    'groups_col_name'   => NULL,
    
    'ug_table'          => NULL,
    'ug_col_user_id'    => NULL,
    'ug_col_group_id'   => NULL,
    
    
     /**
     * --------------------------------------------------------------------------
     * Guest Mode
     * --------------------------------------------------------------------------
     *
     * Guest mode allows your website visitors to use AddChat widget to 
     * send messages without signup or login. As like Chat support.
     * 
     * guest_group_id (optional)
     * To enable guest mode, AddChat only requires a User Group which will chat 
     * with the guests. You can create a seperate User Group and add that group's id.active
     * 
     * e.g 
     * 
     * guest_group_id = 8
     * 
     * 
    */

    'guest_group_id'     => NULL,
    

    /**
     * --------------------------------------------------------------------------
     * Customize default behaviour
     * --------------------------------------------------------------------------
     *
     * hide_email (TRUE/FALSE)
     * Whether to show users email in conversation list.
     * 
     * enter_send (TRUE/FALSE)
     * Whether to send message on pressing Enter
     * 
     * open_chat_on_notification (TRUE/FALSE)
     * Whether to automatically open user chat window 
     * when a new message arrives. 
     * 
    */

    'hide_email'                => FALSE,
    'enter_send'                => TRUE,
    'open_chat_on_notification' => FALSE,

    
    /**
     * --------------------------------------------------------------------------
     * Pagination
     * --------------------------------------------------------------------------
     *
     * Global Pagination Limit
     * 
     * pagination_limit (required|Greater than 0)
     * At a time AddChat fetch 5 rows e.g users, messages, groups, etc
     * 
     * Greater value = More loading time  
     * 
    */

    'pagination_limit' => 5,

    
    /**
     * --------------------------------------------------------------------------
     * Include/Exclude URLs
     * --------------------------------------------------------------------------
     *
     * Control whether to show or not to show AddChat widget on specific URLs
     * 
     * NOTE: 
     * 1. At a time, either include URL or exclude URL will work
     * 2. First priority will be of include_url
     * 3. Make both include_url & exclude_url to empty array 
     *   to show AddChat widget on all pages (URLs).
     * 4. Only add URL segments. 
     *    e.g to show only on www.example.com/profile
     *        enter 'include_url'   => ['profile'],
     * 
     * 
     * include_url (optional)
     * 
     * Show AddChat widget only on these URLs.
     * 
     * e.g
     * 'include_url'   => ['profile', 'dashboard', 'timeline'],
    */

    'include_url'   => [],

    /**
     * exclude_url (optional)
     * 
     * Hide AddChat widget only on these URLs.
     * 
     * e.g
     * 'exclude_url'   => ['about', 'terms', 'privacy-policy'],
    */

    'exclude_url'   => [],
    

     /**
     * --------------------------------------------------------------------------
     * Notification Type
     * --------------------------------------------------------------------------
     *
     * By default, AddChat works on a custom internal realtime notification system
     * build with VueJs.
     * 
     * notification_type: internal/pusher
     * 
     * AddChat comes with integrated Pusher service. To switch to Pusher realtime
     * notifications, add the Pusher API credentials and change notification_type 
     * to 'pusher'
     * 
    */

    'notification_type' => 'internal', 
    'pusher_app_id'     => NULL, 
    'pusher_key'        => NULL, 
    'pusher_secret'     => NULL, 
    'pusher_cluster'    => NULL,

];
