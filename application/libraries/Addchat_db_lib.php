<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library Addchat_db_lib
 *
 * This class handles database interraction
 *
 * @package     addChat
 * @author      classiebit
**/

class Addchat_db_lib 
{
    private $AC_LIB;
    private $AC_SETTINGS;
    
    public function __construct()
    {
        $this->AC_LIB =& get_instance();
        $this->AC_LIB->load->database();
        $this->AC_LIB->load->library(array('session'));
        
        // Addchat tables
        $this->profiles_tb                  = 'ac_profiles';
        $this->ac_messages_tb               = 'ac_messages';
        $this->ac_users_messages_tb         = 'ac_users_messages';
        $this->ac_blocked_tb                = 'ac_blocked';
        $this->ac_contacts_tb               = 'ac_contacts';
        $this->ac_groupchat_tb              = 'ac_groupchat';
        $this->ac_settings_tb               = 'ac_settings';
        $this->ac_guests_tb                 = 'ac_guests';
        $this->ac_guests_messages_tb        = 'ac_guests_messages';

        
        
        // get addchat config
        $this->AC_LIB->config->load('addchat', TRUE);
		$this->AC_CONFIG = $this->AC_LIB->config->item('addchat', 'addchat');

        // get addchat settings
        $this->AC_SETTINGS  				= (object) $this->AC_CONFIG;
       
        // External tables
        // users table
        $this->users_tb                     = $this->AC_SETTINGS->users_table;
        $this->users_tb_id                  = $this->AC_SETTINGS->users_col_id;
        $this->users_tb_email               = $this->AC_SETTINGS->users_col_email;
            

        // groups table
        $this->groups_tb                    = $this->AC_SETTINGS->groups_table;
        $this->groups_tb_id                 = $this->AC_SETTINGS->groups_col_id;
        $this->groups_tb_name               = $this->AC_SETTINGS->groups_col_name;
            

        // users_groups pivot (bridge) table
        $this->ug_tb                        = $this->AC_SETTINGS->ug_table;
        $this->ug_tb_user_id                = $this->AC_SETTINGS->ug_col_user_id;
        $this->ug_tb_group_id               = $this->AC_SETTINGS->ug_col_group_id;
        
    }




    /**
     *======================END SETTINGS============================= 
     */ 

    /**
     *================== GET USER=====================================
     */
    
    // get specific user by id
    
    public function get_user($user_id = 0,  $logged_in_user  = 0, $guest_group_id = 0)
    {
        $guest_group_id = (int) $guest_group_id;

        $select = array(
            "$this->users_tb.$this->users_tb_id",
            "$this->users_tb.$this->users_tb_email",

            "$this->profiles_tb.fullname",
            "$this->profiles_tb.avatar",
            "$this->profiles_tb.dark_mode",
            "$this->profiles_tb.size_small",
            "$this->profiles_tb.status online",
            
        );

        // if guest_group_id is on then check that logged in user part of guest group or not
        if($guest_group_id > 0)
        {
            $is_guest_group =  "(SELECT UG.$this->ug_tb_user_id  FROM $this->ug_tb  UG WHERE  UG.$this->ug_tb_user_id = $user_id AND                                            UG.$this->ug_tb_group_id = $guest_group_id) is_guest_group";
            
            array_push($select, $is_guest_group); 
        }
        
        if($logged_in_user)
        {
            array_push($select, "(SELECT BU.users_id FROM $this->ac_blocked_tb BU WHERE  BU.blocked_users_id = $user_id AND BU.users_id =  $logged_in_user) is_blocked");
            array_push($select, "(SELECT AC.users_id FROM $this->ac_contacts_tb AC WHERE  AC.users_id = $logged_in_user AND AC.contacts_id =  $user_id) is_contact");
        }
            
        return  $this->AC_LIB->db
                ->select($select)
                ->join($this->profiles_tb, "$this->profiles_tb.user_id = $this->users_tb.$this->users_tb_id", "left")
                ->where("$this->users_tb.$this->users_tb_id", $user_id)
                ->get($this->users_tb)
                ->row();
    }
    
    
    // get_users list
    public function get_users(
        $login_user_id = 0, 
        $blocked_by = array(), 
        $filters = array(), 
        $contacts_id = array(), 
        $blocked_to_me = array(),
        $users_id = array(), 
        $chat_users_id = array(), 
        $is_admin = null, 
        $is_groups = null
    )
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->users_tb.$this->users_tb_id",
            "$this->users_tb.$this->users_tb_email",
            "$this->profiles_tb.avatar",
            "$this->profiles_tb.fullname username",
            "$this->profiles_tb.status  online",
        
            "(SELECT IF(COUNT(ACM.id) > 0, COUNT(ACM.id), null) FROM $this->ac_messages_tb ACM WHERE ACM.m_to = '$login_user_id' AND ACM.m_from = '$this->users_tb.$this->users_tb_id' AND ACM.is_read = '0') unread",
        ));

        // exclude logged in user
        $this->AC_LIB->db
            ->join($this->profiles_tb, "$this->profiles_tb.user_id = $this->users_tb.$this->users_tb_id", "left")
            ->where(array("$this->users_tb.$this->users_tb_id !=" =>$login_user_id));
    
        if(!empty($blocked_by))
            $this->AC_LIB->db->where_not_in("$this->users_tb.$this->users_tb_id", $blocked_by);  

        if(!empty($blocked_to_me))
        $this->AC_LIB->db->where_not_in("$this->users_tb.$this->users_tb_id", $blocked_to_me);      

        // in case of search, search amongst all users
        if(!empty($filters['search']) )
        {
            if( !empty($chat_users_id) && !empty($is_admin) && !empty($is_groups) )
            {
                $this->AC_LIB->db
                ->where_in("$this->users_tb.$this->users_tb_id", $chat_users_id) // all group users     
                ->group_start()
                ->or_like("$this->profiles_tb.fullname", $filters['search'], 'both')
                ->or_like("$this->users_tb.$this->users_tb_email", $filters['search'], 'both')
                ->group_end();
            }
            else
            {   
                // admin can seach all users
                // and if have  is_groups off then user can search all users
                $this->AC_LIB->db
                ->group_start()
                ->or_like("$this->profiles_tb.fullname", $filters['search'], 'both')
                ->or_like("$this->users_tb.$this->users_tb_email", $filters['search'], 'both')
                ->group_end();
            }
        }
        else
        {   
            
            if(!empty($users_id))
            {
                $this->AC_LIB->db->where_in("$this->users_tb.$this->users_tb_id",$users_id); // only specific group users  
            }
            else
            {   
                // have no contact user then show all users
                if(!empty($contacts_id))
                {
                    $this->AC_LIB->db->where_in("$this->users_tb.$this->users_tb_id",$contacts_id); // only contact users 
                }    
            }
        }
        return  $this->AC_LIB->db
                ->limit($filters['limit'])
                ->offset($filters['offset'])
                ->get($this->users_tb)
                ->result();
    }
    
    
    // Update users update_user
    
    public function update_user($user_id = 0, $data = array())
    {
        $result =  $this->AC_LIB->db
                    ->select()
                    ->where('user_id', $user_id)
                    ->get("$this->profiles_tb")
                    ->row();
        
        // insert data in profile table if user have not exist 
        if(empty($result))
        {
            $this->AC_LIB->db->insert("$this->profiles_tb", $data);
        }
        else
        {
          // if user have exist then update user data  
             $this->AC_LIB->db
                    ->where("user_id", $user_id)
                    ->update("$this->profiles_tb", $data);
        }        

        return true;
    }

    /**
     * =======================USER END=======================================
     */



    /**
     * ===================BLOCK USER SECTION============================
     */
    
    // get blocked users
    
    public function get_blocked_by_users($user_id = 0, $inverse = false, $buddy = null)
    {
        // get other users who blocked by logged in user
        if($inverse)
        {
            $this->AC_LIB->db
                ->select(array(
                            "$this->ac_blocked_tb.blocked_users_id user_id",
                        ))
                ->where(array("$this->ac_blocked_tb.users_id"=>$user_id));
        } 
        // get others users who blocked to logged in user 
        else
        {
            $this->AC_LIB->db  
                 ->select(array(
                            "$this->ac_blocked_tb.users_id user_id",
                         ))
                 ->where(array("$this->ac_blocked_tb.blocked_users_id"=>$user_id));
        }    

        // can't send messages if user is blocked
        if(!empty($buddy))
        {
            $this->AC_LIB->db->where(array("$this->ac_blocked_tb.blocked_users_id"=>$buddy));
        }
        return  $this->AC_LIB->db
                     ->get($this->ac_blocked_tb)
                     ->result();  
    }

    // Block user 
    
    public function block_user($user_id = 0, $blocked_user_id = 0, $is_report = null)
    {
        $this->AC_LIB->db->delete($this->ac_blocked_tb, array("users_id" => $user_id, "blocked_users_id"=>$blocked_user_id));

        if($this->AC_LIB->db->affected_rows()) // if already blocked then unblock it
        {
            return 0;
        }
        else // block the user 
        {
            $this->AC_LIB->db->insert($this->ac_blocked_tb, array("users_id" => $user_id, "blocked_users_id"=>$blocked_user_id, "is_reported" => $is_report ? $is_report : 0, 'dt_updated' => date("Y-m-d H:i:s")));
            return 1;            
        }

        return FALSE;
    }



    /**
     * ======================= BLOCK END ========================================
     */

    /**
     * ======================DELETE CHAT HISTORY=================================
    */
    
    // Delete chat delete_chat
    
    public function delete_chat($user_id = 0, $sub_user_id = 0)
    {
        $this->AC_LIB->db
        ->where(array("$this->ac_messages_tb.m_from"=>$user_id, "$this->ac_messages_tb.m_to"=>$sub_user_id))
        ->update($this->ac_messages_tb, array("m_from_delete"=>1));

        $this->AC_LIB->db
        ->where(array("$this->ac_messages_tb.m_to"=>$user_id, "$this->ac_messages_tb.m_from"=>$sub_user_id))
        ->update($this->ac_messages_tb, array("m_to_delete"=>1));

        return TRUE;
    }

    /**
     * =======================DELETE END==========================================
     */


    /*
    * ============================SEND MESSAGES===================================
    */
    public function send_message($data = array()) 
    {
        $this->AC_LIB->db->insert($this->ac_messages_tb, $data);
        return $this->AC_LIB->db->insert_id();
    }
    /**
     * ========================SEND END==========================================
     */

    /**
     * ================= CONTACT LIST============================================
     */

     // Create contact list for logged in user
    public function create_contacts($contact = array())
    {
        $result =  $this->AC_LIB->db
                    ->select()
                    ->where($contact)
                    ->get($this->ac_contacts_tb)
                    ->row();
               
        
        if(empty($result))
        {            
            $this->AC_LIB->db->insert($this->ac_contacts_tb,array('users_id' => $contact['users_id'], 'contacts_id' => $contact['contacts_id'] ,'dt_updated' => date("Y-m-d H:i:s")));
        }

        // inverse
        $result =  $this->AC_LIB->db
                    ->select()
                    ->where(array(
                        'users_id' => $contact['contacts_id'], 
                        'contacts_id' => $contact['users_id']
                    ))
                    ->get($this->ac_contacts_tb)
                    ->row();
               
        if(empty($result))
        {            
            $this->AC_LIB->db->insert($this->ac_contacts_tb,array('users_id' => $contact['contacts_id'], 'contacts_id' => $contact['users_id'] ,'dt_updated' => date("Y-m-d H:i:s")));
        }

        return true;
        
    }

    //  Get contact users
    public function get_contact_users($login_user_id = null)
    {
        return $this->AC_LIB->db
                ->select()
                ->where('users_id',$login_user_id)
                ->get($this->ac_contacts_tb)
                ->result();
    }

    // Remove user from contact list
     
    public function remove_contacts($remove_user = array())
    {
        return $this->AC_LIB->db
                ->where($remove_user)
                ->delete($this->ac_contacts_tb); 
        
    }
    /**
     * ====================CONTACT END====================================
     */

   
     /**
     * ===================START NOTIFICATION=============================
     */

    // add notification 
    public function set_notification($notification = array())
    {
        $result =  $this->AC_LIB->db
                    ->select()
                    ->where($notification)
                    ->get($this->ac_users_messages_tb)
                    ->row();
        
        // insert
        if(empty($result))
        {            
            $this->AC_LIB->db->insert($this->ac_users_messages_tb, $notification);
        }
        else // update 
        {
            $this->AC_LIB->db
            ->where($notification)
            ->set('messages_count', 'messages_count+1', FALSE)
            ->update($this->ac_users_messages_tb);
        }

        return true;
        
    }
     
    // Remove notification
    public function remove_notification($notification = array())
    {
        return $this->AC_LIB->db
                ->where($notification)
                ->delete($this->ac_users_messages_tb); 
        
    }
    
    //  get notification
    public function get_updates($login_user_id = null)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_users_messages_tb.users_id",
            "$this->ac_users_messages_tb.buddy_id",
            "$this->ac_users_messages_tb.messages_count",
        ))
        ->where("buddy_id", $login_user_id);
        
        return $this->AC_LIB->db
                ->get($this->ac_users_messages_tb)
                ->result_array();
    }
    
    //  get latest message
    public function get_latest_message($login_user_id = null, $buddy_id = null)
    {
        $result =  $this->AC_LIB->db
                ->select(array(
                    "$this->ac_messages_tb.id ",
                    "$this->ac_messages_tb.m_from ",
                    "$this->ac_messages_tb.m_to ",
                    "$this->ac_messages_tb.message ",
                    "$this->ac_messages_tb.attachment ",
                    "$this->ac_messages_tb.is_read ",
                    "$this->ac_messages_tb.dt_updated ",
                    "$this->ac_messages_tb.m_reply_id ",
                    "$this->ac_messages_tb.reply_user_id ",
                    "(SELECT MU.message FROM $this->ac_messages_tb MU WHERE MU.id = $this->ac_messages_tb.m_reply_id) quote_message"
                ))
                ->where(array("$this->ac_messages_tb.m_from" => $buddy_id, "$this->ac_messages_tb.m_to" => $login_user_id, "$this->ac_messages_tb.is_read" => '0'))
            
                //group query for removing unsend messages
                ->where(["$this->ac_messages_tb.m_from_delete" => "0", "$this->ac_messages_tb.m_to_delete" => "0"])
                ->order_by("$this->ac_messages_tb.id")
                ->get($this->ac_messages_tb);

        // delete notification
        $this->AC_LIB->db
        ->where("$this->ac_messages_tb.m_to", $login_user_id)
        ->where("$this->ac_messages_tb.m_from", $buddy_id)
        ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));

        return $result->result();
    }



    /**
     * =============END NOTIFICATION========================================= 
     */


    /*
    * =============GET MESSAGES BETWEEN TWO USERS ============================
    */
    public function get_messages($user_id = 0, $chat_user = 0, $filters = array(), $count = false)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_messages_tb.id ",
            "$this->ac_messages_tb.m_from ",
            "$this->ac_messages_tb.m_to ",
            "$this->ac_messages_tb.message ",
            "$this->ac_messages_tb.attachment ",
            "$this->ac_messages_tb.is_read ",
            "$this->ac_messages_tb.dt_updated ",
            "$this->ac_messages_tb.m_reply_id ",
            "$this->ac_messages_tb.reply_user_id ",
            "(SELECT MU.message FROM $this->ac_messages_tb MU WHERE MU.id = $this->ac_messages_tb.m_reply_id) quote_message",
            
        ));
        // //group query for removing deleted messages
        $this->AC_LIB->db
        ->where("( (`$this->ac_messages_tb`.`m_from` = '$user_id' AND `$this->ac_messages_tb`.`m_to` = '$chat_user')", null, FALSE)
        ->or_where("(`$this->ac_messages_tb`.`m_from` = '$chat_user' AND `$this->ac_messages_tb`.`m_to` = '$user_id') )", null, FALSE)
        ->where("( (IF(`$this->ac_messages_tb`.`m_from` = '$user_id', `$this->ac_messages_tb`.`m_from_delete`, `$this->ac_messages_tb`.`m_to_delete`) = 0) AND (IF(`$this->ac_messages_tb`.`m_to` = '$user_id', `$this->ac_messages_tb`.`m_to_delete`, `$this->ac_messages_tb`.`m_from_delete`) = 0) )", null, FALSE);

        
        if(!$count)
            return $this->AC_LIB->db->count_all_results($this->ac_messages_tb);
        
        $messages   = $this->AC_LIB->db
                    ->order_by("$this->ac_messages_tb.id")
                    ->limit($filters['limit'])
                    ->offset($filters['offset'])
                    ->get($this->ac_messages_tb);

        $this->AC_LIB->db
        ->where("$this->ac_messages_tb.m_to", $user_id)
        ->where("$this->ac_messages_tb.m_from", $chat_user)
        ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));

        return $messages->result();
    }
    /**
     *===============================END MESSAGES========================================
     */
    
    
    /***
     * =========================GET GROUPS  ===================================
     */


    // get group id of logged in user
    public function get_groups_id($login_user_id = null)
    {
        return  $this->AC_LIB->db
                ->select(array(
                    $this->ug_tb_group_id,
                ))
                ->where($this->ug_tb_user_id, $login_user_id)
                ->get($this->ug_tb)
                ->result();
    }

    // get groupchat ids  
    public function get_groupchat($group_ids = [])
    {
        return    $this->AC_LIB->db
                ->select(array(
                    "gc_id",
                ))
                ->where_in("group_id", $group_ids)
                ->get($this->ac_groupchat_tb)
                ->result();
    }

    // get group users id 
    public function get_groups_users_id($group_id = null, $filters =  array(), $gc_id = array(), $logged_in_user = null)
    {
        
        $this->AC_LIB->db
            ->select(array(
                $this->ug_tb_user_id,
            ))
        // exclude logged in user
        ->where(array($this->ug_tb_user_id." !=" =>$logged_in_user));
    
        if(!empty($gc_id))
        {
            $this->AC_LIB->db        
            ->where_in($this->ug_tb_group_id, $gc_id);
        }
        else
        {
            $this->AC_LIB->db        
            ->where($this->ug_tb_group_id, $group_id)
            ->limit($filters['limit'])
            ->offset($filters['offset']);
        }
                 
        return  $this->AC_LIB->db
                    ->get($this->ug_tb)
                    ->result();
    }

    // get group name
    public function get_chatgroups($group_ids = [], $is_admin = null)
    {
        
        $this->AC_LIB->db
            ->select(array(
                "$this->groups_tb_id",
                "$this->groups_tb_name",
                "(SELECT count(*)  FROM $this->ug_tb  UG WHERE UG.$this->ug_tb_group_id = $this->groups_tb.$this->groups_tb_id) group_users_count",
            ));
        if(empty($is_admin))
        {
            $this->AC_LIB->db->where_in("$this->groups_tb_id", $group_ids);
        }
        return  $this->AC_LIB->db
                    ->get($this->groups_tb)
                    ->result();
    }

    

    // unsend message

    public function message_unsend($message_id = null, $login_user_id = null)
	{
        $this->AC_LIB->db
                ->where(array("id" => $message_id, "m_from" => $login_user_id, "is_read" => 0))
                ->update($this->ac_messages_tb, array("m_to_delete" => '1', "m_from_delete" => '1'));
        
                
        if($this->AC_LIB->db->affected_rows() > 0)
        {
            return true;
        }
        
    }
    
    // delete message

    public function message_delete($message_id = null, $login_user_id = null)
	{
        $message  =    $this->AC_LIB->db
                        ->select('*')
                        ->where("id", $message_id)
                        ->get($this->ac_messages_tb)
                        ->row();
        
        if(empty($message))
            return false;

        $this->AC_LIB->db
        ->where(array("id" => $message_id));
        
        if($message->m_from == $login_user_id)
            $this->AC_LIB->db->update($this->ac_messages_tb, array("m_from_delete" => '1'));

        if($message->m_to == $login_user_id)
            $this->AC_LIB->db->update($this->ac_messages_tb, array("m_to_delete" => '1'));    

        return true;
	}

   

    /**
     * ==============END====================================
     */
    
   


    
    /**
     * ================= GET SETTINGS ===============================
     */

    



    /* ========== ADMIN DB FUNCTIONS START ==========*/
    


    /*
     *   save group chat settings
     */
    public function save_groupchat($data = array(), $group_id = null)
    {
        // delete then insert
        $this->AC_LIB->db
                    ->where('group_id', $group_id)
                    ->delete($this->ac_groupchat_tb);

        return  $this->AC_LIB->db->insert_batch($this->ac_groupchat_tb, $data); 

    }

    /*
     *     get all blocked users
     */ 

    public function get_blocked_users($filters = array())
    {
        return    $this->AC_LIB->db
                    ->select(array(
                                "$this->ac_blocked_tb.is_reported",
                                "$this->ac_blocked_tb.dt_updated",
                                "(SELECT PR.fullname  FROM $this->profiles_tb   PR  WHERE PR.user_id  = $this->ac_blocked_tb.blocked_users_id) blocked_users",
                                "(SELECT PR2.fullname FROM $this->profiles_tb   PR2 WHERE PR2.user_id = $this->ac_blocked_tb.users_id) blocked_by_users",
                                "(SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_blocked_tb.blocked_users_id) blocked_users_email",
                                "(SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_blocked_tb.users_id) 
                                    blocked_by_users_email",
                            ))
                    ->limit($filters['limit'])
                    ->offset($filters['offset'])        
                    ->get($this->ac_blocked_tb)
                    ->result();          
    }
    
    /**
    *   get groups
    */
    public function a_get_groups()
    {
        return  $this->AC_LIB->db
                    ->get($this->groups_tb)
                    ->result();
    }

    
    /**
    *   get chatgroups
    */
    public function a_get_chatgroups()
    {
        return  $this->AC_LIB->db
                    ->get($this->ac_groupchat_tb)
                    ->result();
    }

    /**
     *   get chat users who chat with each other means between users
     */
    public function a_chat_between($filters = array(), $logged_in_user = null)
    {   
        $query = "SELECT `id` FROM $this->ac_messages_tb WHERE `m_from` = $logged_in_user GROUP BY `m_from`";

        if( $this->AC_LIB->db->simple_query($query) )
        {
            // safe mode is off
            $select = array(
                "$this->ac_messages_tb.id",
                "$this->ac_messages_tb.m_to",
                "$this->ac_messages_tb.m_from",
                "$this->ac_messages_tb.g_to",
                "$this->ac_messages_tb.g_from",
                "$this->ac_messages_tb.dt_updated",
                "$this->ac_messages_tb.message",
                "(SELECT PR.fullname  FROM $this->profiles_tb  PR  WHERE PR.user_id  = $this->ac_messages_tb.m_from) m_from_username",
                "(SELECT PR2.fullname FROM $this->profiles_tb  PR2 WHERE PR2.user_id = $this->ac_messages_tb.m_to) m_to_username",
                "(SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_messages_tb.m_from)
                    m_from_email",
                "(SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_messages_tb.m_to) 
                    m_to_email",
                
            );
        }           
        else
        {
            // safe mode is on
            $select = array(
                "ANY_VALUE($this->ac_messages_tb.id) id",
                "ANY_VALUE($this->ac_messages_tb.m_to) m_to",
                "ANY_VALUE($this->ac_messages_tb.g_to) g_to",
                "ANY_VALUE($this->ac_messages_tb.g_from) g_from",
                "$this->ac_messages_tb.m_from",
                "ANY_VALUE($this->ac_messages_tb.dt_updated) dt_updated",
                "ANY_VALUE($this->ac_messages_tb.message) message",
                "ANY_VALUE((SELECT PR.fullname  FROM $this->profiles_tb  PR  WHERE PR.user_id  = $this->ac_messages_tb.m_from)) m_from_username",
                "ANY_VALUE((SELECT PR2.fullname FROM $this->profiles_tb  PR2 WHERE PR2.user_id = $this->ac_messages_tb.m_to)) m_to_username",
                "ANY_VALUE((SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_messages_tb.m_from))
                    m_from_email",
                "ANY_VALUE((SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_messages_tb.m_to)) 
                    m_to_email",
            );
        }

        return  $this->AC_LIB->db
                ->select($select)
                ->where(['m_to !=' => '0', 'm_from !=' => '0'])
                ->group_by(array("$this->ac_messages_tb.m_from", "m_to"))
                ->order_by("id", 'DESC')
                ->limit($filters['limit'])
                ->offset($filters['offset'])  
                ->get($this->ac_messages_tb)
                ->result();
                 
    }

    /**
     *   get conversations between two users
     * 
     */

    public function a_get_conversations($m_from = null, $m_to = null, $filters = array(), $count = false)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_messages_tb.id ",
            "$this->ac_messages_tb.m_from ",
            "$this->ac_messages_tb.m_to ",
            "$this->ac_messages_tb.message ",
            "$this->ac_messages_tb.attachment ",
            "$this->ac_messages_tb.is_read ",
            "$this->ac_messages_tb.dt_updated ",
            "$this->ac_messages_tb.m_reply_id ",
            "$this->ac_messages_tb.reply_user_id ",
            "$this->ac_messages_tb.m_to_delete ",
            "$this->ac_messages_tb.m_from_delete ",
            "(SELECT MU.message FROM $this->ac_messages_tb MU WHERE MU.id = $this->ac_messages_tb.m_reply_id) quote_message",
            "(SELECT PR.avatar  FROM $this->profiles_tb PR WHERE PR.user_id    = $this->ac_messages_tb.m_from) m_from_image",
            "(SELECT PR2.avatar FROM $this->profiles_tb PR2 WHERE PR2.user_id  = $this->ac_messages_tb.m_to)   m_to_image",
        ));
        // //group query for removing deleted messages
        $this->AC_LIB->db
        ->where("( (`$this->ac_messages_tb`.`m_from` = '$m_from' AND `$this->ac_messages_tb`.`m_to` = '$m_to')", null, FALSE)
        ->or_where("(`$this->ac_messages_tb`.`m_from` = '$m_to' AND `$this->ac_messages_tb`.`m_to` = '$m_from') )", null, FALSE);
        

        
        if($count)
        return $this->AC_LIB->db->count_all_results($this->ac_messages_tb);

        return  $this->AC_LIB->db
                        ->order_by("$this->ac_messages_tb.id")
                        ->limit($filters['limit'])
                        ->offset($filters['offset'])
                        ->get($this->ac_messages_tb)
                        ->result();

    }
    /**
     * ================================Guest user function start==============================================================
     */

    /**
     *  Guest User login
     *  
     */

    public function guest_login($params = [])
    {
        $guest_user = [];
        // check guest user that he is already exits into table or not if user already exist into table then don't insert data again
        $guest_user      = $this->AC_LIB->db->select(['id', 'fullname', 'email', 'status'])->where(array('email' => $params['email']))->get($this->ac_guests_tb)->row();
        
        if(empty($guest_user))
        {   
            $this->AC_LIB->db->insert($this->ac_guests_tb, $params);
            $id             = $this->AC_LIB->db->insert_id();
            $guest_user     = $this->AC_LIB->db->select(['id', 'fullname', 'email'])->where( array('id' => $id))->get($this->ac_guests_tb)->row();
        }
        else
        {
            // update status of guest table 
            if(empty($guest_user->status))
            {
                $this->AC_LIB->db
                ->where("id", $guest_user->id)
                ->update("$this->ac_guests_tb", array('status' => 1));
            }
        }

        return $guest_user;
        
    }

    /**
     *   get guest user only one base on id
     */
    
    public function get_guest_user($guest_user_id = null)
    {
        return $this->AC_LIB->db->select(['id', 'fullname', 'email'])->where( array('id' => $guest_user_id, 'status' => 1))->get($this->ac_guests_tb)->row();   
    }

    // get guest group users ids form ac_group_chat table
    public function get_guest_group_users_ids($gc_id = null)
    {
        return  $this->AC_LIB->db
                    ->select(array(
                        $this->ug_tb_user_id,
                    ))
                    ->where($this->ug_tb_group_id, $gc_id)
                    ->get($this->ug_tb)
                    ->result();
    }

    // get guest group users from users table
    public function get_guest_group_users($users_ids = [])
    {
        $select = array(
            "$this->users_tb.$this->users_tb_id",
            "$this->users_tb.$this->users_tb_email",
            "$this->profiles_tb.avatar",
            "$this->profiles_tb.fullname username",
        );
            
        return  $this->AC_LIB->db
                ->select($select)
                ->join($this->profiles_tb, "$this->profiles_tb.user_id = $this->users_tb.$this->users_tb_id", "left")
                ->where_in("$this->users_tb.$this->users_tb_id", $users_ids)
                ->get($this->users_tb)
                ->result();
    }

    // guest user send messages
    public function guest_send_message($data = [])
    {   
        return $this->AC_LIB->db->insert_batch($this->ac_messages_tb, $data);
    }

    // get all guests list
    public function get_guests($params = [])
    {
        $this->AC_LIB->db
        ->select([
            'id',
            'fullname',
            'email',
            'dt_updated'
      ]);

            // in case of search, search amongst all users
        if(!empty($params['filters']['search']) )
        {
            $this->AC_LIB->db
            ->group_start()
            ->or_like("fullname", $params['filters']['search'], 'both')
            ->or_like("email", $params['filters']['search'], 'both')
            ->group_end();
        }
        
        return  $this->AC_LIB->db
                ->where(['status' => 1])
                ->limit($params['filters']['limit'])
                ->offset($params['filters']['offset'])
                ->order_by("dt_updated", 'DESC')
                ->get($this->ac_guests_tb)
                ->result();
    }

    // get guest messages  for guest login user
    public function get_guest_messages($logged_user_id = 0, $guest_id = 0, $filters = array(), $count = false)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_messages_tb.id ",
            "$this->ac_messages_tb.m_from ",
            "$this->ac_messages_tb.m_to",
            "$this->ac_messages_tb.g_from ",
            "$this->ac_messages_tb.g_to ",
            "$this->ac_messages_tb.message ",
            "$this->ac_messages_tb.attachment ",
            "$this->ac_messages_tb.is_read ",
            "$this->ac_messages_tb.dt_updated ",
            
            
        ));
        
        $this->AC_LIB->db
        ->where("( (`$this->ac_messages_tb`.`m_from` = '$logged_user_id' AND `$this->ac_messages_tb`.`g_to` = '$guest_id')", null, FALSE)
        ->or_where("(`$this->ac_messages_tb`.`g_from` = '$guest_id' AND `$this->ac_messages_tb`.`m_to` = '$logged_user_id') )", null, FALSE)

        // //group query for removing deleted messages
        ->where(["$this->ac_messages_tb.m_from_delete" => '0', "$this->ac_messages_tb.m_to_delete" => '0' ]); 

        if(!$count)
            return $this->AC_LIB->db->count_all_results($this->ac_messages_tb);
        
        $messages   = $this->AC_LIB->db
                    ->order_by("$this->ac_messages_tb.id")
                    ->limit($filters['limit'])
                    ->offset($filters['offset'])
                    ->get($this->ac_messages_tb);

        $this->AC_LIB->db
        ->where("$this->ac_messages_tb.m_to", $logged_user_id)
        ->where("$this->ac_messages_tb.g_from", $guest_id)
        ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));

        return $messages->result();
    }

    // login user  send message to guest
    public function send_to_guest($data = [])
    {
        $this->AC_LIB->db->insert($this->ac_messages_tb, $data);
        return $this->AC_LIB->db->insert_id();
    }

    // guest can see all messages og guest group users with his messages
    public function get_messages_of_guest($guest_id = 0, $filters = array(), $count = false)
    {
        $query = "SELECT `id` FROM $this->ac_messages_tb WHERE `m_from` = $guest_id GROUP BY `m_from`";

        $select  = [];
        if( $this->AC_LIB->db->simple_query($query) )
        {
            // safe mode is off
            $select = array(
                "$this->ac_messages_tb.id ",
                "$this->ac_messages_tb.m_from",
                "$this->ac_messages_tb.m_to",
                "$this->ac_messages_tb.m_from_delete ",
                "$this->ac_messages_tb.m_to_delete",
                "$this->ac_messages_tb.g_to ",
                "$this->ac_messages_tb.g_from ",
                "$this->ac_messages_tb.is_read ",
                "$this->ac_messages_tb.g_random ",
                "$this->ac_messages_tb.message ",
                "$this->ac_messages_tb.dt_updated ",
                "(SELECT P.avatar FROM $this->profiles_tb  P WHERE  P.user_id = $this->ac_messages_tb.m_from) m_from_image",
                "(SELECT P2.fullname  FROM $this->profiles_tb  P2 WHERE  P2.user_id = $this->ac_messages_tb.m_from) m_from_name",
            );
        }
        
        else
        {
            // safe mode is on
            $select = array(
                "ANY_VALUE($this->ac_messages_tb.id) id",
                "ANY_VALUE($this->ac_messages_tb.m_from) m_from",
                "ANY_VALUE($this->ac_messages_tb.m_to) m_to",
                "ANY_VALUE($this->ac_messages_tb.m_from_delete) m_from_delete",
                "ANY_VALUE($this->ac_messages_tb.m_to_delete) m_to_delete",
                "ANY_VALUE($this->ac_messages_tb.g_to) g_to",
                "ANY_VALUE($this->ac_messages_tb.g_from) g_from",
                "ANY_VALUE($this->ac_messages_tb.is_read) is_read",
                "$this->ac_messages_tb.g_random",
                "ANY_VALUE($this->ac_messages_tb.message) message",
                "ANY_VALUE($this->ac_messages_tb.dt_updated) dt_updated",
                "ANY_VALUE((SELECT P.avatar  FROM $this->profiles_tb  P WHERE  P.user_id = $this->ac_messages_tb.m_from)) m_from_image",
                "ANY_VALUE((SELECT P2.fullname  FROM $this->profiles_tb  P2 WHERE  P2.user_id = $this->ac_messages_tb.m_from)) m_from_name",
            );
        }

        
        $this->AC_LIB->db
        ->select($select)
        //group query for removing deleted messages
        ->where(["$this->ac_messages_tb.m_from_delete" => "0", "$this->ac_messages_tb.m_to_delete" => "0"]); 
        
        $where = '(g_to = "'.$guest_id.'"  OR  g_from = "'.$guest_id.'")';  // AND (g_to = guest_id OR g_from = guest_id)
        $this->AC_LIB->db->where($where);
        
        if(!$count)
            return $this->AC_LIB->db->group_by("$this->ac_messages_tb.g_random")->count_all_results($this->ac_messages_tb);
        
        $messages   = $this->AC_LIB->db
                    ->group_by("$this->ac_messages_tb.g_random")
                    ->order_by("id")
                    ->limit($filters['limit'])
                    ->offset($filters['offset'])
                    ->get($this->ac_messages_tb);

        $this->AC_LIB->db
        ->where("$this->ac_messages_tb.g_to", $guest_id)
        ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));

        return $messages->result();
    }

    // set notification for guest
    public function set_guest_notification($notification = array())
    {
        $data_insert   = [];
        $data_update   = [];

        foreach($notification as $key => $value)
        {
            $result =  $this->AC_LIB->db
                        ->select()
                        ->where($value)
                        ->get($this->ac_guests_messages_tb)
                        ->result();

            if(empty($result))
            {
                $data_insert[] = $value;
            }
            else
            {
                $data_update[] = $value;
            }            
        }           

        
        // insert
        if(!empty($data_insert))
        {
            $this->AC_LIB->db->insert_batch($this->ac_guests_messages_tb  , $data_insert);
        }
       
        //update
        if(!empty($data_update)) 
        {
            foreach($data_update as $key => $value)
            {
                $this->AC_LIB->db
                ->where($value)
                ->set('messages_count', 'messages_count+1', FALSE)
                ->update($this->ac_guests_messages_tb);
            }
         
        }

        return true;
        
    }

    //  get notification
    public function get_guest_updates($login_user_id = null, $guest_id = null)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_guests_messages_tb.m_to",
            "$this->ac_guests_messages_tb.m_from",
            "$this->ac_guests_messages_tb.g_to",
            "$this->ac_guests_messages_tb.g_from",
            "$this->ac_guests_messages_tb.messages_count",
        ));

        if(!empty($login_user_id))
            $this->AC_LIB->db->where(["m_to" => $login_user_id]);
        else    
        {   
            $this->AC_LIB->db->where([ "g_to" => $guest_id]);
        }    

        return $this->AC_LIB->db
                ->get($this->ac_guests_messages_tb)
                ->result_array();
    }

    //  get latest message for guest
    public function get_guest_latest_message($login_user_id = null, $guest_id = null)
    {
        $this->AC_LIB->db
            ->select(array(
                "$this->ac_messages_tb.id ",
                "$this->ac_messages_tb.m_from ",
                "$this->ac_messages_tb.m_to ",
                "$this->ac_messages_tb.g_from ",
                "$this->ac_messages_tb.g_to ",
                "$this->ac_messages_tb.message ",
                "$this->ac_messages_tb.is_read ",
                "$this->ac_messages_tb.dt_updated ",
                "(SELECT P.avatar FROM $this->profiles_tb  P WHERE  P.user_id = $this->ac_messages_tb.m_from) m_from_image",
            ));

        if(!empty($login_user_id))
        {
           
            // it is for login user
            $result = $this->AC_LIB->db    
                    ->where(array("$this->ac_messages_tb.g_from" => $guest_id, "$this->ac_messages_tb.m_to" => $login_user_id, "$this->ac_messages_tb.is_read" => '0'))
                    
                    //group query for removing deleted messages
                    ->where(["$this->ac_messages_tb.m_from_delete" => "0", "$this->ac_messages_tb.m_to_delete" => "0"])
                    ->order_by("$this->ac_messages_tb.id")
                    ->get($this->ac_messages_tb);
                    
                    
            // delete notification
            $this->AC_LIB->db
            ->where("$this->ac_messages_tb.m_to", $login_user_id)
            ->where("$this->ac_messages_tb.g_from", $guest_id)
            ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));
           
        }
        else
        {
            
            // guest user without login
             $result = $this->AC_LIB->db    
                    ->where(array("$this->ac_messages_tb.g_to" => $guest_id,  "$this->ac_messages_tb.is_read" => '0'))
                    
                    //group query for removing deleted messages
                    ->where(["$this->ac_messages_tb.m_from_delete" => "0", "$this->ac_messages_tb.m_to_delete" => "0"])
                    ->order_by("$this->ac_messages_tb.id")
                    ->get($this->ac_messages_tb);
          
            // delete notification
            $this->AC_LIB->db
            ->where("$this->ac_messages_tb.g_to", $guest_id)
            ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));
        }
       

        return $result->result();
    }

    // Remove notification
    public function remove_guest_notification($notification = array())
    {
        return $this->AC_LIB->db
                ->where($notification)
                ->delete($this->ac_guests_messages_tb); 
        
    }

    /**
     *   check login user have guest account
     */
    
    public  function update_guest_messages($params = [])
    {
        $this->AC_LIB->db
        ->where("g_to", $params['guest_id'])
        ->update("$this->ac_messages_tb", array('m_to' => $params['logged_user_id'] ));

        $this->AC_LIB->db
        ->where("g_from", $params['guest_id'])
        ->update("$this->ac_messages_tb", array('m_from' => $params['logged_user_id']));

        // update status of guest table 
        $this->AC_LIB->db
        ->where("id", $params['guest_id'])
        ->update("$this->ac_guests_tb", array('status' => '0'));

        return true;
    }

    /**
     *   get chat users who chat with each other means between users
     */
    public function a_chat_between_guest($filters = array(), $logged_in_user = null)
    {
        $query = "SELECT `id` FROM $this->ac_messages_tb WHERE `m_from` = $logged_in_user GROUP BY `m_from`";

        if( $this->AC_LIB->db->simple_query($query) )
        {
            // safe mode is off
            $select = array(
                "$this->ac_messages_tb.id",
                "$this->ac_messages_tb.m_to",
                "$this->ac_messages_tb.m_from",
                "$this->ac_messages_tb.g_to",
                "$this->ac_messages_tb.g_from",
                "$this->ac_messages_tb.dt_updated",
                "$this->ac_messages_tb.message",
                "(SELECT PR.fullname  FROM $this->profiles_tb  PR  WHERE PR.user_id  = $this->ac_messages_tb.m_from) m_from_username",
                "(SELECT PR2.fullname FROM $this->profiles_tb  PR2 WHERE PR2.user_id = $this->ac_messages_tb.m_to) m_to_username",
                "(SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_messages_tb.m_from)
                    m_from_email",
                "(SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_messages_tb.m_to) 
                    m_to_email",

                "(SELECT GS.fullname  FROM $this->ac_guests_tb  GS WHERE GS.id = $this->ac_messages_tb.g_from)
                    g_from_fullname",
                "(SELECT GS2.fullname FROM $this->ac_guests_tb GS2 WHERE GS2.id = $this->ac_messages_tb.g_to)
                    g_to_fullname",       
                
            );
        }           
        else
        {
            // safe mode is on
            $select = array(
                "ANY_VALUE($this->ac_messages_tb.id) id",
                "ANY_VALUE($this->ac_messages_tb.m_to) m_to",
                "ANY_VALUE($this->ac_messages_tb.g_to) g_to",
                "ANY_VALUE($this->ac_messages_tb.g_from) g_from",
                "ANY_VALUE($this->ac_messages_tb.m_from) m_from",
                "ANY_VALUE($this->ac_messages_tb.dt_updated) dt_updated",
                "ANY_VALUE($this->ac_messages_tb.message) message",
                "ANY_VALUE((SELECT PR.fullname  FROM $this->profiles_tb  PR  WHERE PR.user_id  = $this->ac_messages_tb.m_from)) m_from_username",
                "ANY_VALUE((SELECT PR2.fullname FROM $this->profiles_tb  PR2 WHERE PR2.user_id = $this->ac_messages_tb.m_to)) m_to_username",
                "ANY_VALUE((SELECT UR.$this->users_tb_email  FROM $this->users_tb UR WHERE UR.$this->users_tb_id = $this->ac_messages_tb.m_from))
                    m_from_email",
                "ANY_VALUE((SELECT UR2.$this->users_tb_email FROM $this->users_tb UR2 WHERE UR2.$this->users_tb_id = $this->ac_messages_tb.m_to)) 
                    m_to_email",
                
                "ANY_VALUE((SELECT GS.fullname  FROM $this->ac_guests_tb  GS WHERE GS.id = $this->ac_messages_tb.g_from))
                    g_from_fullname",
                "ANY_VALUE((SELECT GS2.fullname FROM $this->ac_guests_tb GS2 WHERE GS2.id = $this->ac_messages_tb.g_to)) 
                    g_to_fullname",    
            );
        }

        return  $this->AC_LIB->db
                ->select($select)
                ->or_where(['g_to !=' => '0', 'g_from !=' => '0'])
                ->group_by(array("$this->ac_messages_tb.g_from", "g_to", "m_from", "m_to"))
                ->order_by("id", 'DESC')
                ->limit($filters['limit'])
                ->offset($filters['offset'])  
                ->get($this->ac_messages_tb)
                ->result();
    }

    /**
     *   get conversations between guest and users
     * 
     */

    public function a_get_guest_conversations($user_id = null, $guest_id = null, $filters = array(), $count = false)
    {
        $this->AC_LIB->db
        ->select(array(
            "$this->ac_messages_tb.id ",
            "$this->ac_messages_tb.m_from ",
            "$this->ac_messages_tb.m_to ",
            "$this->ac_messages_tb.g_from ",
            "$this->ac_messages_tb.g_to ",
            "$this->ac_messages_tb.message ",
            "$this->ac_messages_tb.attachment ",
            "$this->ac_messages_tb.is_read ",
            "$this->ac_messages_tb.dt_updated ",
            "$this->ac_messages_tb.m_reply_id ",
            "$this->ac_messages_tb.reply_user_id ",
            "$this->ac_messages_tb.m_to_delete ",
            "$this->ac_messages_tb.m_from_delete ",
            "(SELECT PR.avatar  FROM $this->profiles_tb PR WHERE PR.user_id    = $this->ac_messages_tb.m_from) m_from_image",
            "(SELECT PR2.avatar FROM $this->profiles_tb PR2 WHERE PR2.user_id  = $this->ac_messages_tb.m_to)   m_to_image",
        ));
        // //group query for removing deleted messages
        $this->AC_LIB->db
        ->where("( (`$this->ac_messages_tb`.`m_from` = '$user_id' AND `$this->ac_messages_tb`.`g_to` = '$guest_id')", null, FALSE)
        ->or_where("(`$this->ac_messages_tb`.`g_from` = '$guest_id' AND `$this->ac_messages_tb`.`m_to` = '$user_id') )", null, FALSE);
        

        
        if($count)
        return $this->AC_LIB->db->count_all_results($this->ac_messages_tb);

        return  $this->AC_LIB->db
                        ->order_by("$this->ac_messages_tb.id")
                        ->limit($filters['limit'])
                        ->offset($filters['offset'])
                        ->get($this->ac_messages_tb)
                        ->result();

    }

    /**
     * =====================================Guest user funtion End==================================================================
     */



    /**
     *==================== Pusher notification start ===================================================================
     */

    public function is_read($login_user_id, $buddy_id)
    {
        $this->AC_LIB->db
        ->where("$this->ac_messages_tb.m_to", $login_user_id)
        ->where("$this->ac_messages_tb.m_from", $buddy_id)
        ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));

    }

    public function is_read_guest($login_user_id, $guest_id)
    {
        
        // login guest
        if(!empty($login_user_id))
        {
            // delete notification
            $this->AC_LIB->db
            ->where("$this->ac_messages_tb.g_from", $guest_id)
            ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));
        }    
        else
        {
            // without login guest
            // delete notification
            $this->AC_LIB->db
            ->where("$this->ac_messages_tb.g_to", $guest_id)
            ->update($this->ac_messages_tb, array("$this->ac_messages_tb.is_read"=>'1'));
        }

    }

    

    /**
     * ============================End pusher notification======================================================
     */









}

/*End Addchat_db_lib class*/