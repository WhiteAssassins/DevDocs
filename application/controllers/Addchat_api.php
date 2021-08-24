<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Addchat_api Controller
 *
 * This class connect addChat to AddChat_lib
 *
 * @package     addchat
 * @author      classiebit
*/

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('content-type: application/json; charset=utf-8');

class Addchat_api extends CI_Controller {

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // Load AddChat required libraries
        $this->load->helper(array('form', 'url', 'language'));
        $this->load->library(array('form_validation'));

        // AddChat Lib
        $this->load->library('addchat_lib');

        
    }

    // get language
    public function get_lang()
    {
        $this->addchat_lib->get_lang();
    }


    // pusher auth
    public function auth()
    {
        $this->addchat_lib->auth();
    }


    public function get_profile()
    {
        $this->addchat_lib->get_profile();
    }

    public function get_buddy()
    {
        $this->addchat_lib->get_buddy();
    }

    public function get_config()
    {
        $this->addchat_lib->get_config();
    }

    public function get_users($offset = 0)
    {
        $this->addchat_lib->get_users($offset,  null, null);
    }

    public function size_change()
    {
        $this->addchat_lib->size_change();
    }

    public function dark_mode_change()
    {
        $this->addchat_lib->dark_mode_change();
    }

    
    public function upload_profile_pic()
    {
        $this->addchat_lib->upload_profile_pic();
    }

    public function get_updates()
    {
        $this->addchat_lib->get_updates();
    }

    
    public function get_latest_message($buddy_id = null)
    {
        $this->addchat_lib->get_latest_message($buddy_id);
    }

    public function profile_update()
    {
        $this->addchat_lib->profile_update();
    }

    public function block_user($user_id = null, $is_report = null )
    {
        $this->addchat_lib->block_user($user_id, $is_report);
    }

    public function delete_chat($user_id = null)
    {
        $this->addchat_lib->delete_chat($user_id);
    }

    public function send_message()
    {
        $this->addchat_lib->send_message();
    }

    public function get_messages($buddy_id = null,$offset = 0)
    {
        $this->addchat_lib->get_messages($buddy_id,$offset);
    }

    public function remove_contacts($user_id = null)
    {
        $this->addchat_lib->remove_contacts($user_id);
    }

    public function add_contacts($user_id = null)
    {
        $this->addchat_lib->add_contacts($user_id);
    }

    public function get_groups()
    {
        $this->addchat_lib->get_groups();
    }

    public function get_groupschat_users($group_id = null, $offset = null)
    {
        $this->addchat_lib->get_groupschat_users($group_id, $offset);
    }   

    // unsend message
    public function message_unsend($message_id = null)
    {   
        $this->addchat_lib->message_unsend($message_id);
    }

    // unsend message
    public function message_delete($message_id = null)
    {   
        $this->addchat_lib->message_delete($message_id);
    }

   // admin functions
   
    public function save_settings()
    {
        $this->addchat_lib->save_settings();
    }

    public function get_blocked_users($offset = 0)
    {
        $this->addchat_lib->get_blocked_users($offset);
    }

    public function save_groupchat()
    {
        $this->addchat_lib->save_groupchat();
    }

    /* Admin functions */
    public function a_get_groups()
    {
            $this->addchat_lib->a_get_groups();
    }

    public function a_chat_between($offset = 0)
    {
        $this->addchat_lib->a_chat_between($offset);
    }

    public function a_get_conversations($m_from = null, $m_to = null, $offset = 0)
    {
        
        $this->addchat_lib->a_get_conversations($m_from, $m_to, $offset);
    }

    public function check_admin()
    {
        $this->addchat_lib->check_admin();
    }
   
   /**
    * =====================  End Admin function===============================================
    */


   /**
    *===========================Guest User function Start=======================================================   
    */

    // guest user login
    public function guest_login()
    {
        $this->addchat_lib->guest_login();
    }

    // check guest user exist or not and match id
    public function get_guest_user()
    {
        $this->addchat_lib->get_guest_user();
    }

    //guest user send message 
    public function guest_send_message()
    {
        $this->addchat_lib->guest_send_message();
    }

    //get guest users for login user if login user have guest group  
    public function get_guests($offset = 0)
    {
        $this->addchat_lib->get_guests($offset);
    }

    // get messages for guest
    public function get_guest_messages($guest_id = null, $offset = 0)
    {
        $this->addchat_lib->get_guest_messages($guest_id, $offset);
    }

    // get guest buddy for guest buddy component
    public function get_guest_buddy()
    {
        $this->addchat_lib->get_guest_buddy();
    }

    // login user send message to guest users
    public function send_to_guest()
    {
        $this->addchat_lib->send_to_guest();
    }

    // guest can see all messages og guest group users with his messages
    public function get_messages_of_guest()
    {   
        $this->addchat_lib->get_messages_of_guest();
    }

    // get guest notification
    public function get_guest_updates()
    {
        $this->addchat_lib->get_guest_updates();
    }

    // get latest message for guest user when he is login
    public function get_guest_latest_message($guest_id = null)
    {
        $this->addchat_lib->get_guest_latest_message($guest_id);
    }

    // get latest message for guest user when he is login
    public function get_guest_latest_message1()
    {
        $this->addchat_lib->get_guest_latest_message1();
    }

    // get latest message for guest user when he is login
    public function check_guest_account()
    {
        $this->addchat_lib->check_guest_account();
    }

    // chat between guests
    public function a_chat_between_guest($offset = 0)
    {
        $this->addchat_lib->a_chat_between_guest($offset);
    }

    // a_get_guest_conversations
    public function a_get_guest_conversations()
    {
        $this->addchat_lib->a_get_guest_conversations();
    }
    
    
    /**
     * ============End Guest users function=======================================================================
     */
    
     /***
     *================  Pusher notification start ========================================
     */
    // message_notification
    
    public function message_notification()
    {
        $this->addchat_lib->message_notification();
    }

     // is_typing
    public function is_typing()
    {
        $this->addchat_lib->is_typing();
    }

    // remove notification if message component already open
    public function remove_notifications()
    {
        $this->addchat_lib->remove_notifications();
    }

    // is_read update
    public function is_read()
    {
        $this->addchat_lib->is_read();
    }


    // guest_message_notification
    
    public function guest_message_notification()
    {
        $this->addchat_lib->guest_message_notification();
    }

    // pusher notification for guest

    public function remove_guest_notifications()
    {
        $this->addchat_lib->remove_guest_notifications();
    }

    // is_read_guest update
    public function is_read_guest()
    {
        $this->addchat_lib->is_read_guest();
    }

    // is_typing
    public function is_typing_guest()
    {
        $this->addchat_lib->is_typing_guest();
    }
    /**
     *===================================    pusher notification end =============================================  
     */

     /**
      *  check session
      */
    public function check_session() 
    {
        $this->addchat_lib->check_session();
    } 
}

/* Addchat_api controller ends */