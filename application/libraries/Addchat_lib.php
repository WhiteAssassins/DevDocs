<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Library Addchat_lib
 *
 * This class handles all the functionality
 *
 * @package     addChat
 * @author      classiebit
**/

class Addchat_lib 
{
    // globals
	private $AC_LIB;
	private $AC_SETTINGS;
	private $AC_CONFIG;
	
    function __construct()
    {
        // load addchat prerequisite
        $this->AC_LIB =& get_instance();
        $this->AC_LIB->load->helper(array('form', 'url', 'email', 'language'));
		$this->AC_LIB->load->library(array('addchat_db_lib', 'form_validation', 'session'));

        // get application default language + sync with AddChat lang
        // get default lang if in session
        if(!empty($this->AC_LIB->session->language))
            $this->AC_LIB->config->set_item('language', $this->AC_LIB->session->language);
            
        $language = $this->AC_LIB->config->item('language');
        $this->AC_LIB->lang->load('addchat', $language);

		
        // get addchat config
        $this->AC_LIB->config->load('addchat', TRUE);
		$this->AC_CONFIG = $this->AC_LIB->config->item('addchat', 'addchat');

        // get addchat settings
		$this->AC_SETTINGS  				= (object) $this->AC_CONFIG;

        // get the logged in user
		$this->AC_SETTINGS->logged_user_id  = empty($this->AC_SETTINGS->session_user_id) ? 0 : 
												$this->AC_LIB->session->userdata($this->AC_SETTINGS->session_user_id);
		
        // get the admin user
		$this->AC_SETTINGS->admin_user_id 	= (int) $this->AC_SETTINGS->admin_user_id;
		$this->AC_SETTINGS->is_admin 		= $this->AC_SETTINGS->admin_user_id == $this->AC_SETTINGS->logged_user_id ? $this->AC_SETTINGS->admin_user_id : 0;
		
		
        // get the guest group id
        $this->AC_SETTINGS->guest_mode      = 0;
        if((int) $this->AC_SETTINGS->guest_group_id)
			$this->AC_SETTINGS->guest_mode  = 1;
			
        // user belongs to multiple groups
		$logged_group_ids  = [];
		if(!empty($this->AC_SETTINGS->logged_user_id) && ((int) $this->AC_SETTINGS->guest_mode > 0))
			$logged_group_ids 					= $this->AC_LIB->addchat_db_lib->get_groups_id($this->AC_SETTINGS->logged_user_id);
        // tell addchat about it
		$this->AC_SETTINGS->logged_group_id = [];
		if(!empty($logged_group_ids))
			foreach($logged_group_ids as $key => $value)
				$this->AC_SETTINGS->logged_group_id[$key] = $value->group_id;
	}


	/*
    * Get-set lang
    */
    public function get_lang()
    {
        // get lang variables
        $lang_variables =    $this->AC_LIB->lang->language;

        // send to app
        $this->format_json(['lang' => $lang_variables]);
    }
	
	/*
    * Get configurations
    */
	public function get_config()
	{
		// return only selected settings
		$data['config'] 						    = array();
		$data['config']['widget_name'] 		        = $this->AC_SETTINGS->widget_name;
		$data['config']['widget_logo'] 		        = $this->AC_SETTINGS->widget_logo;
		$data['config']['widget_icon'] 		        = $this->AC_SETTINGS->widget_icon;
		$data['config']['widget_user_avatar'] 		= $this->AC_SETTINGS->widget_user_avatar;
		$data['config']['widget_notify_sound'] 		= $this->AC_SETTINGS->widget_notify_sound;
		$data['config']['widget_footer_text'] 	    = $this->AC_SETTINGS->widget_footer_text;
		$data['config']['widget_footer_url']        = $this->AC_SETTINGS->widget_footer_url;
		$data['config']['upload_path']          	= $this->AC_SETTINGS->upload_path;
		$data['config']['aui'] 		                = (int) $this->AC_SETTINGS->admin_user_id;
		$data['config']['lui'] 		                = (int) $this->AC_SETTINGS->logged_user_id;
		$data['config']['hide_email'] 		        = $this->AC_SETTINGS->hide_email ? 1 : 0;
		$data['config']['enter_send'] 		        = $this->AC_SETTINGS->enter_send ? 1 : 0;
		$data['config']['open_chat_on_notification']= $this->AC_SETTINGS->open_chat_on_notification ? 1 : 0;
		$data['config']['pusher_key'] 		        = $this->AC_SETTINGS->pusher_key;
		$data['config']['pusher_cluster'] 		    = $this->AC_SETTINGS->pusher_cluster;

		$data['config']['s_host']            = $_SERVER['REMOTE_ADDR'];
		$data['config']['check_session']     = $this->AC_LIB->session->userdata("ac_verify") ? 1 : 0;
		$data['config']['is_admin']			 = $this->AC_SETTINGS->is_admin;
		$data['config']['is_groups']		 = $this->AC_SETTINGS->groups_table ? 1 : 0;
		$data['config']['include_or_exclude']= (!empty($this->AC_SETTINGS->include_url) || !empty($this->AC_SETTINGS->exclude_url)) ? 1 : 0;
		$data['config']['guest_mode']	     = $this->AC_SETTINGS->guest_mode;
		$data['config']['notification_type'] = $this->AC_SETTINGS->notification_type != 'internal' ? 1 : 0;
		
		$this->format_json($data);
	}

	
	/**
	 *  check session
	 */
    
    public function check_session()
    {
		$this->AC_LIB->session->set_userdata("ac_verify", 1);
		$this->format_json(['status' => 1]);
	}   
	
	/*
	*	Get user's profile 
	*/
	public function get_profile($is_return = false)
    {
        // check is logged-in
        $this->check_auth();

		$data					= array();
		$data['status'] 		= true;
		$data['profile'] 		= $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->logged_user_id, 0 , $this->AC_SETTINGS->guest_group_id);

		
		// if login user is admin then is_guest_group alway is 1 means true 
		if($this->AC_SETTINGS->logged_user_id == $this->AC_SETTINGS->admin_user_id && (int)$this->AC_SETTINGS->guest_mode > 0)
		{
			$data['profile']->is_guest_group = 1;
		}
		
		if($is_return)
			return $data;

		$this->format_json($data);
	}
	
	/**
	 * Get buddy
	 */
	public function get_buddy()
	{
        // check is logged-in
        $this->check_auth();

		/* Validate form input */
        $this->AC_LIB->form_validation
		->set_rules('user', lang('user'), 'trim|is_natural_no_zero');
		
		if($this->AC_LIB->form_validation->run() === FALSE)
        {
        	$this->format_json(array('status' => false, 'response'=> validation_errors()));
		}
		   
		$data				= array();
		$buddy 				= (int) $this->AC_LIB->input->post('user');
		$chatbuddy 			= $this->AC_LIB->addchat_db_lib->get_user($buddy, $this->AC_SETTINGS->logged_user_id, $this->AC_SETTINGS->guest_group_id);

		$c_buddy = array(
			'name' 		 	=> ucwords($chatbuddy->fullname),
			'status' 	 	=> $chatbuddy->online,
			'avatar'		=> $chatbuddy->avatar,
			'is_blocked' 	=> $chatbuddy->is_blocked,
			'id' 		 	=> $chatbuddy->id,
			'is_contact'	=> $chatbuddy->is_contact,
			'email'			=> $chatbuddy->email,
		)
        ;
		$data['buddy']		=	$c_buddy;
		$data['status']		=	true;
		$this->format_json($data);
	}

	/*
    * Get users list get_users
    */
    public function get_users($offset = 0,  $users_id = array(), $flag = false)
    {   
        // check is logged-in
        $this->check_auth();

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;
		$filters['search']          = (string) $this->AC_LIB->input->post('search');

		//other users blocked by logged in user
		$blocked_by 		 		= array();
		$blocked_by_t 		 		= $this->AC_LIB->addchat_db_lib->get_blocked_by_users($this->AC_SETTINGS->logged_user_id, true);
		//other users blocked to logged in user
		$blocked_to_me				= array();
		$blocked_to_me_t  			= $this->AC_LIB->addchat_db_lib->get_blocked_by_users($this->AC_SETTINGS->logged_user_id);
		
		foreach ($blocked_by_t as $val) 
			$blocked_by[] = $val->user_id;	

		foreach ($blocked_to_me_t as $val) 
			$blocked_to_me[] = $val->user_id;

		$groupchat_id = [];		
		// if logged in user have no group's ids then don't call this function
		if(!empty($this->AC_SETTINGS->logged_group_id))
		{	
			// seach only groupchat users
			$groupchat_id  	= $this->AC_LIB->addchat_db_lib->get_groupchat($this->AC_SETTINGS->logged_group_id);
		}	
		
		// if the specific groups has no other group to chat with or
		// if login user is admin then he can chat with all groups
		$gc_id          = array();
		if(!empty($groupchat_id))
		{
			// remove duplicate group's id
			foreach($groupchat_id as $val)
				if(!in_array($val->gc_id, $gc_id))
					$gc_id[]	= $val->gc_id;			
		}

		// if no chatgroups added then do not query chatgroups table
		$chat_users_id_temp = array();
		if(!empty($gc_id))
			$chat_users_id_temp				=   $this->AC_LIB->addchat_db_lib->get_groups_users_id(null, null, $gc_id);

		$chat_users_id					=	array();

		if(!empty($chat_users_id_temp))
		{
			foreach($chat_users_id_temp as $val)
				$chat_users_id[]	= $val->user_id;
		}

		// get contacts
		$contacts_id				= array();
		if(empty($filters['search']) && empty($flag) && empty($contact_users))
		{
			$contact_users				= $this->AC_LIB->addchat_db_lib->get_contact_users($this->AC_SETTINGS->logged_user_id);
			if(!empty($contact_users))
			{
				foreach ($contact_users as $val) 
					$contacts_id[] = $val->contacts_id;	
			}
		}

		$users      = 	$this->AC_LIB->addchat_db_lib->get_users(
            $this->AC_SETTINGS->logged_user_id, 
            $blocked_by, 
            $filters, 
            $contacts_id, 
            $blocked_to_me, 
            $users_id, 
            $chat_users_id, 
            $this->AC_SETTINGS->is_admin, 
            $this->AC_SETTINGS->groups_table ? 1 : 0
        );
	
        // get groupchat users and flag ture for groupchat users
		if($flag)
			return $users;

		if(empty($users))
        {
            $data       = array(
                            'users'  	=> array(),
                            'offset'    => 0,
							'more'      => 0,  // to stop load more process
							'status'    => true,
                        );
            $this->format_json($data);
        }
        
        $data                       = array();
        $data['users'] 				= $users;
		$data['offset']             = $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'];
		$data['more']               = 1;  // to continue load more process
		$data['status'] 			= true;

		$this->format_json($data);
	}
	

	/*
	* Get messages get_messages
	*/
	public  function get_messages($buddy_id = null, $offset = 0)
	{
        // check is logged-in
        $this->check_auth();

		$buddy_id         			= (int) $buddy_id;
		
		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;

		$total_messages 			= $this->AC_LIB->addchat_db_lib->get_messages($this->AC_SETTINGS->logged_user_id, $buddy_id, $filters);
 
		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] : 0;
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}
		
		$messages 					= $this->AC_LIB->addchat_db_lib->get_messages($this->AC_SETTINGS->logged_user_id, $buddy_id, $filters, true);

		if(empty($messages))
        {
			$data       = array(
				'messages'  => array(),
				'offset'    => 0,
				'more'      => 0,  // to stop load more process
				'status'    => true,
			);
            $this->format_json($data);
		}

		// remove notification
		$this->AC_LIB->addchat_db_lib->remove_notification(array('buddy_id'=>$this->AC_SETTINGS->logged_user_id, 'users_id'=>$buddy_id));

		$data 					= array();
		$data['messages'] 		= array();
		foreach ($messages as $key => $message) 
		{
			$data['messages'][$key]['message_id'] 			= $message->id;
			$data['messages'][$key]['sender'] 				= $message->m_from;
			$data['messages'][$key]['recipient'] 			= $message->m_to;
			$data['messages'][$key]['message'] 				= $message->message;
			$data['messages'][$key]['is_read'] 				= $message->is_read;
			$data['messages'][$key]['attachment'] 			= $message->attachment;
			$data['messages'][$key]['dt_updated'] 			= $message->dt_updated; 
			$data['messages'][$key]['m_reply_id'] 			= $message->m_reply_id;
			$data['messages'][$key]['reply_user_id'] 		= $message->reply_user_id;
			$data['messages'][$key]['quote_message'] 		= $message->quote_message;
			
		}
		
		$data['offset']				= $filters['offset'];			
		$data['more']               = $more;  // to continue load more process
		$data['status'] 			= true;
		
		$this->format_json($data);
	}
	
	/*
	* Send message send_message
	*/
	public function send_message()
	{
        // check is logged-in
        $this->check_auth();

		/* Validate form input */
        $this->AC_LIB->form_validation
        ->set_rules('user', lang('user'), 'required|trim|is_natural_no_zero')
        ->set_rules('m_reply_id', lang('user'), 'trim|is_natural_no_zero')
		->set_rules('reply_user_id', lang('user'), 'trim|is_natural_no_zero')
		->set_rules('message', lang('message'), 'trim');
		
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
        }

		$buddy 				= (int) $this->AC_LIB->input->post('user');
		$m_reply_id 		= (int) $this->AC_LIB->input->post('m_reply_id');
		$reply_user_id 		= (int) $this->AC_LIB->input->post('reply_user_id');
		$message 			= nl2br($this->AC_LIB->input->post('message'));

        // return null if buddy or message is empty
        if(!$buddy)
			$this->format_json(['status' => false,'response' => 'N/A']);

        // reject if user is blocked by logged in user
        $blocked_by 	= $this->AC_LIB->addchat_db_lib->get_blocked_by_users($this->AC_SETTINGS->logged_user_id, true, $buddy);
        if(!empty($blocked_by))
            $this->format_json(['status' => false,'response' => 'N/A']);

		// upload attachment image (only if exists)
		$filename               = null;
        if(! empty($_FILES['attachment']['name'])) // if image 
        {
			$file               = array('folder'=> $this->AC_SETTINGS->upload_path, 'input_file'=>'attachment');
	        $filename           = $this->upload_file($file);
            // through image upload error
            if(!empty($filename['error']))
            {
				$data	=	array('status' => false, 'response'=> lang('image_upload').' (png | jpg | jpeg)' );
				$this->format_json($data);
	        }
		}
		
		if(empty($filename))
		{
			/* Validate form input */
			$this->AC_LIB->form_validation
			->set_rules('message', lang('message'), 'required|trim');
			
			if($this->AC_LIB->form_validation->run() === FALSE)
			{
				   $data = array('status' => false, 'response'=> validation_errors());
				$this->format_json($data);
			}
		}

        $msg    = array(
            "m_from"		=> $this->AC_SETTINGS->logged_user_id,
            "m_to" 			=> $buddy,
            "message" 		=> $message,
            "attachment" 	=> $filename,
            "dt_updated" 	=> date('Y-m-d H:i:s'),
            "m_reply_id"    => $m_reply_id,
            "reply_user_id" => $reply_user_id,
        );
		
			
        $msg_id = $this->AC_LIB->addchat_db_lib->send_message($msg);

        $data = array(
            array(
                'users_id' 		=> $this->AC_SETTINGS->logged_user_id,
                'contacts_id' 	=> $buddy,
            ),
            array(
                'users_id' 		=> $buddy,
                'contacts_id' 	=> $this->AC_SETTINGS->logged_user_id,
            )
        );

        
        $this->AC_LIB->addchat_db_lib->create_contacts(array('users_id' => $this->AC_SETTINGS->logged_user_id, 'contacts_id' => $buddy ));
        
        // 2. set_notification
        $this->AC_LIB->addchat_db_lib->set_notification(array('users_id' => $this->AC_SETTINGS->logged_user_id, 'buddy_id' => $buddy));
    
        $chat = array(
            'message_id' 		=> $msg_id,
            'sender' 			=> $msg['m_from'], 
            'recipient' 		=> $msg['m_to'],
            'attachment' 		=> $msg['attachment'],
            'message' 			=> $msg['message'],
            'dt_updated' 		=> $msg['dt_updated'],
            'is_read' 			=> 0,
            "m_reply_id"    	=> $m_reply_id,
            "reply_user_id" 	=> $reply_user_id,
            "quote_message" 	=> $this->AC_LIB->input->post('quote_message'),
        );

        $data = array(
            'status' 	=> true,
            'message' 	=> $chat 	  
        );
		
		$this->format_json($data);
	}

	/*
	* Block user block_user
	*/
	public function block_user($user_id = null, $is_report = null)
    {   
        // check is logged-in
        $this->check_auth();

		$user_id  	=  (int) $user_id;
		$is_report  =  (int) $is_report;

		if(empty($user_id))
        	$this->format_json(array('status' => false, 'response'=> lang('block').' '.lang('fail')));

		// block user
		$data   				= array();
		$data['status']			= $this->AC_LIB->addchat_db_lib->block_user($this->AC_SETTINGS->logged_user_id, $user_id, $is_report);

		$this->format_json($data);
	}

	/*
	* Delete chat history delete_chat
	*/
	public function delete_chat($user_id = null)
	{
        // check is logged-in
        $this->check_auth();

		$user_id = (int) $user_id;
        if(empty($user_id))
        {
        	$data  =  array('status' => false, 'response'=> lang('delete').' '.lang('fail'));
			$this->format_json($data);
        }

		$data					= array();
		$data['status'] 		= $this->AC_LIB->addchat_db_lib->delete_chat($this->AC_SETTINGS->logged_user_id, $user_id);

		$this->format_json($data);
	}

	/**
	 * 	Remove  user from contact list
	 */
	public function remove_contacts($user_id = null)
	{
        // check is logged-in
        $this->check_auth();

		$user_id = (int) $user_id;

		if(empty($user_id))
        {
        	$data  =  array('status' => false, 'response'=> lang('remove').' '.lang('fail'));
			$this->format_json($data);
		}

		// remove user from contact list
		$remove_user			= array('users_id' => $this->AC_SETTINGS->logged_user_id, 'contacts_id' => $user_id);
		$data['status']			= $this->AC_LIB->addchat_db_lib->remove_contacts($remove_user);

		$this->format_json($data);
	}
	
	/**
	 *  Add user to contact list
	 */
	public function add_contacts($user_id = null)
	{
        // check is logged-in
        $this->check_auth();

		$user_id = (int) $user_id;
		if(empty($user_id))
        {
        	$data  =  array('status' => false, 'response'=> lang('add').' '.lang('fail'));
			$this->format_json($data);
		}
		
		$status  =  $this->AC_LIB->addchat_db_lib->create_contacts(array('users_id' => $this->AC_SETTINGS->logged_user_id, 'contacts_id' => $user_id));

		$this->format_json(array('status'=>$status));
	}

	/*
	* Update profile profile_update
	*/
    public function profile_update()
    {
        // check is logged-in
        $this->check_auth();
		
		$this->AC_LIB->form_validation
		->set_rules('status', lang('status'), 'required|trim')
		->set_rules('fullname', lang('fullname'), 'required|trim')
		->set_rules('user_id', lang('user'), 'required|trim|is_natural_no_zero');
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
		}

		// upload attachment image
		$filename               = null;
		if(! empty($_FILES['image']['name'])) // if image 
        {
			$file               = array('folder'=>$this->AC_SETTINGS->upload_path, 'input_file'=>'image');
	        $filename           = $this->upload_file($file);
            // through image upload error
            if(!empty($filename['error']))
            {
				$data	=	array('status' => false, 'response'=> lang('image_upload').' (png | jpg | jpeg)' );
				$this->format_json($data);
	        }
        }

		$data								= array();
		$data['status']		= $this->AC_LIB->input->post('status');
		$data['fullname']	= $this->AC_LIB->input->post('fullname');
		$data['user_id']	= $this->AC_LIB->input->post('user_id');
		$data['dt_updated'] =  date("Y-m-d H:i:s");

		if(!empty($filename))
			$data['avatar'] = $filename;
		
		// update user status
		$status           =  $this->AC_LIB->addchat_db_lib->update_user($this->AC_SETTINGS->logged_user_id, $data);
		if($status)
			$this->format_json($this->get_profile(true));

	}

	
    /*
    * Get realtime updates of messages get_updates
    */
    public function get_updates()
	{
        // check is logged-in
        $this->check_auth();

		$notification 	= $this->AC_LIB->addchat_db_lib->get_updates($this->AC_SETTINGS->logged_user_id);

		// stop sending notification if in case of same notification
		$is_same = false;
		if(!empty($_POST['notification']))
		{
			$post['notification'] = json_decode($_POST['notification'], true);
			$notification         = json_encode($notification);
			$notification         = json_decode($notification, true);
			
			// check notification same or not
			$difference = $this->multi_array_diff($notification, $post['notification']);
			
			// if have no difference then is_same will be true
			if(!$difference)
				$is_same = true;
		}
		

		// if no messages then do nothing
	    if(empty($notification) || $is_same)
	   		$this->format_json(array('status' => false, 'response'=> 'N/A'));
		
		$this->format_json(array('status' => true, 'notification' => $notification));
	}

	/*
    * Get latest message of active buddy
    */
    public function get_latest_message($buddy_id = null)
	{
		// check is logged-in
        $this->check_auth();

		$buddy_id = (int) $buddy_id;
		$messages 	= array();
		if($buddy_id)
		{
			$messages 	= $this->AC_LIB->addchat_db_lib->get_latest_message($this->AC_SETTINGS->logged_user_id, $buddy_id);

			// if any new message then remove the specific notification
			// remove notification
			$this->AC_LIB->addchat_db_lib->remove_notification(array('buddy_id'=>$this->AC_SETTINGS->logged_user_id, 'users_id'=>$buddy_id));

		}

		// if no messages then do nothing
	    if(empty($messages))
	   		$this->format_json(array('status' => false, 'response'=> 'N/A'));

		$this->format_json(array('status' => true, 'messages' => $messages));
	}

	
	/**
	 * Get groups name of logged in user 
	 */
	public function get_groups()
	{	
        // check is logged-in
        $this->check_auth();

		// if logged in user have no group's ids then don't call this function
		$groupchat_id = [];
		if(!empty($this->AC_SETTINGS->logged_group_id))
			$groupchat_id  	= $this->AC_LIB->addchat_db_lib->get_groupchat($this->AC_SETTINGS->logged_group_id);

		$gc_id          = array();
		if(empty($groupchat_id) && empty($this->AC_SETTINGS->is_admin))
			$this->format_json(array('status' => true , 'responce' => 'N/A'));
		
		foreach($groupchat_id as $val)
			if(!in_array($val->gc_id, $gc_id))
				$gc_id[]	= $val->gc_id;
		
		$groups 		= $this->AC_LIB->addchat_db_lib->get_chatgroups($gc_id, $this->AC_SETTINGS->is_admin);
		if(!empty($groups))
			$this->format_json(array('status' => true, 'groups' => $groups));
		
		$this->format_json(array('status' => false));
	}

	/**
	 * 	GET users from group  
	 */
	public function get_groupschat_users($group_id = null, $offset = 0)
	{
        // check is logged-in
        $this->check_auth();
		
		$group_id					=  	(int) $group_id;
		
        // filters
		$filters                    = 	array();
		$filters['limit']           = 	$this->AC_SETTINGS->pagination_limit;;
		$filters['offset']          = 	(int) $offset;

		$users_id					=	array();
		
		// get users id of particular group
		$users_id_temp				=   $this->AC_LIB->addchat_db_lib->get_groups_users_id($group_id, $filters, null, $this->AC_SETTINGS->logged_user_id);
		if(!empty($users_id_temp))
		{
			foreach($users_id_temp as $val)
				$users_id[]	= $val->user_id;

			$group_users    	=   $this->get_users(0 , $users_id, true);
			
			// get guest group users only when group id == 6 
			if($group_id == 6)
				$group_users = $this->AC_LIB->addchat_db_lib->get_guest_group_users($users_id);
		
			if(!empty($group_users))
			{
				$data					=	array();	
				$data['offset']     	=   $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'];
				$data['more']       	=   1;  // to continue load more process
				$data['status']			=   true;
				$data['group_users']	=   $group_users;
				$this->format_json($data);
			}
			else
			{
				$data       = array(
					'group_users'  	=> array(),
					'offset'        => 0,
					'more'          => 0,  // to stop load more process
					'status'        => true,
				);
				$this->format_json($data);
			}
		}
		
        $data       = array(
                        'group_users'  	=> array(),
                        'offset'    => 0,
                        'more'      => 0,  // to stop load more process
                        'status'    => true,
                    );
        $this->format_json($data);
	}

	/*
	* unsend Message 
	*/
	public function message_unsend($message_id = null)
	{
        // check is logged-in
        $this->check_auth();

		$message_id = (int) $message_id;
        if(empty($message_id))
			$this->format_json(array('status' => false, 'message' => lang('message').' '.lang('not_found')));

		$status  	= $this->AC_LIB->addchat_db_lib->message_unsend($message_id, $this->AC_SETTINGS->logged_user_id);
		if($status)
			$this->format_json(array('status' => true, 'message' => lang('unsent')));
		
		$this->format_json(array('status' => false, 'message'=> lang('unsent_fail')));
	}

	/**
	 *  message delete
	 */
	public function message_delete($message_id = null)
	{
        // check is logged-in
        $this->check_auth();
		
		$message_id = (int) $message_id;
		if(empty($message_id))
			$this->format_json(array('status' => false));

		$status  	= $this->AC_LIB->addchat_db_lib->message_delete($message_id, $this->AC_SETTINGS->logged_user_id);

		if($status)
			$this->format_json(array('status' => true, 'message' => lang('message').' '.lang('deleted')));
		
		$this->format_json(array('status' => false, 'message'=> lang('delete').' '.lang('fail')));
	}

	/**
	 *  size change 
	 */
	public function size_change()
	{   
        // check is logged-in
        $this->check_auth();

		$this->AC_LIB->form_validation
		->set_rules('size', lang('resize'), 'trim')
		->set_rules('fullname', lang('fullname'), 'required|trim');
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
		}

		$data = [
			'size_small'  => (int) $this->AC_LIB->input->post('size'),
			'fullname'    => $this->AC_LIB->input->post('fullname'),
			'user_id'     => $this->AC_SETTINGS->logged_user_id,
			'dt_updated'   => date('Y-m-d H:i:s'),
		];
		
		$status  	= $this->AC_LIB->addchat_db_lib->update_user($this->AC_SETTINGS->logged_user_id, $data);

		$this->format_json(array('status' => $status));
	}

	/**
	 *  dark mode change
	 */
	public function dark_mode_change()
	{
		// check is logged-in
        $this->check_auth();
		
		$this->AC_LIB->form_validation
		->set_rules('dark_mode', lang('dark_mode'), 'trim')
		->set_rules('fullname', lang('fullname'), 'required|trim');
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
		}

		$data = [
			'dark_mode'   => (int) $this->AC_LIB->input->post('dark_mode'),
			'fullname'    => $this->AC_LIB->input->post('fullname'),
			'user_id'     => $this->AC_SETTINGS->logged_user_id,
			'dt_updated'   => date('Y-m-d H:i:s'),
		];
		
		$status  	= $this->AC_LIB->addchat_db_lib->update_user($this->AC_SETTINGS->logged_user_id, $data);

		$this->format_json(array('status' => $status));
		
	}


	/* ========== ADMIN PANEL APIs start==========*/

    /**
     * Check admin auth
    */
    public function check_admin($is_return = false)
    {
	    // check if logged-in user is admin
		if((int) $this->AC_SETTINGS->admin_user_id !== (int) $this->AC_SETTINGS->logged_user_id)
			$this->format_json(array('status' => false));

		if(!$is_return)
			$this->format_json(array('status' => true));

		return true;
	}

	
	/*
	*	Get all blocked users
	*/
	public function get_blocked_users($offset = 0)
	{
		//check admin authentication
		$this->check_admin(true);

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;

		$blocked_users    = $this->AC_LIB->addchat_db_lib->get_blocked_users($filters);
		if(empty($blocked_users))
        {
            $data       = array(
                            'blocked_users'  	=> array(),
                            'offset'    		=> 0,
							'more'      		=> 0,  // to stop load more process
							'status'    		=> true,
                        );

            $this->format_json($data);
        }

		$data                       = array();
        $data['blocked_users'] 		= $blocked_users;
		$data['offset']             = $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'];
		$data['more']               = 1;  // to continue load more process
		$data['status'] 			= true;

		$this->format_json($data);

		
	}

	/**
	 *  save groupchat settings means one group can chat other groups
	 */
	public function save_groupchat()
	{
		//check admin authentication
		$this->check_admin(true);

		/* Validate form input */
        $this->AC_LIB->form_validation
        ->set_rules('group_id', lang('groups_id'), 'required|trim|is_natural_no_zero');
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
        }

		$group_id 			= (int) $this->AC_LIB->input->post('group_id');
		$gc_id 				=  $this->AC_LIB->input->post('gc_id');
		if(empty($gc_id))
		{
			$data = array('status' => false, 'response'=> lang('select').' '.lang('groups'));
			$this->format_json($data);
		}

		$data					= array();
		foreach($gc_id as $key => $value)
		{
			$data[$key]['group_id']	= $group_id;
			$data[$key]['gc_id']	= $value;
		}

		$status     			= $this->AC_LIB->addchat_db_lib->save_groupchat($data, $group_id);	

		$this->format_json(array('status' => $status));
		
	} 

	/**
	 * 	get group names
	 */
	public function a_get_groups() 
	{
		//check admin authentication
		$this->check_admin(true);

		$groups 		= $this->AC_LIB->addchat_db_lib->a_get_groups();
		if(empty($groups))
			$this->format_json(array('status' => false));
		
		// chatgroups
		$chatgroups_tmp 	= $this->AC_LIB->addchat_db_lib->a_get_chatgroups();
		$chatgroups 		= array();
		foreach($chatgroups_tmp as $key => $val)
			$chatgroups[$val->group_id][] = $val->gc_id;

		$data = array(
			'status' 		=> true,
			'groups' 		=> $groups,
			'chatgroups'	=> $chatgroups,
		);

		$this->format_json($data);
	}

	/**
	 *  get chat users who chat with each other means between users
	 * 
	 */
	public function a_chat_between($offset = 0)
	{
		//check admin authentication
		$this->check_admin(true);

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']    		= (int) $offset;

		$chat_betweens 	= $this->AC_LIB->addchat_db_lib->a_chat_between($filters, $this->AC_SETTINGS->logged_user_id);
		if(empty($chat_betweens))
		{
			$data       = array(
				'chat_betweens'  	=> array(),
				'offset'    		=> 0,
				'more'      		=> 0,  // to stop load more process
				'status'    		=> true,
			);
			$this->format_json($data);
		}

		// remove duplicate rows from chat_betweens
		$chat_data  = [];
		
		foreach($chat_betweens as $key => $value)
		{
			if(empty($chat_data))
			{
				$chat_data[] = $value;
			}
			else
			{
				$duplicate = false;

				foreach($chat_data as $key1 => $value1)
				{
					if($value1->m_from == $value->m_to && $value1->m_to == $value->m_from)
					{
						$duplicate = true;			
					}
				}
				if(!$duplicate)
					$chat_data[] = $value;
			}
		}
		
		$data = array(
			'status' 				=> true,
			'offset'    			=> $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'],
			'more'      			=> 1,  // to stop load more process
			'chat_betweens' 		=> $chat_data,
		);

		$this->format_json($data);
	}

	/**
	 *   get conversation of between to  users
	 */
	public function a_get_conversations($m_from = null, $m_to = null, $offset = 0)
	{
		//check admin authentication
		$this->check_admin(true);

		$m_from			= (int) $m_from;
		$m_to			= (int)	$m_to;

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;

		$total_messages 			= $this->AC_LIB->addchat_db_lib->a_get_conversations($m_from, $m_to, $filters, true);

		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] :	0;

			
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}

		$conversations 	= $this->AC_LIB->addchat_db_lib->a_get_conversations($m_from, $m_to, $filters);

		if(empty($conversations))
        {
			$data       = array(
				'conversations'  => array(),
				'offset'    	 => 0,
				'more'      	 => 0,  // to stop load more process
				'status'         => true,
			);
            $this->format_json($data);
		}
	
		$data       = array(
			'conversations'  	=> $conversations,
			'status'    		=> true,
			'more'				=> $more,	// to continue load more process
			'offset'			=> $filters['offset'],
		);
		$this->format_json($data);
	}

    /**
	 *  chat between guest user
	 */
	public function a_chat_between_guest($offset = 0)
    {
		//check admin authentication
		$this->check_admin(true);

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']    		= (int) $offset;

		$chat_between_guest 		= $this->AC_LIB->addchat_db_lib->a_chat_between_guest($filters, $this->AC_SETTINGS->logged_user_id);
		
		if(empty($chat_between_guest))
		{
			$data       = array(
				'chat_between_guest'  	=> array(),
				'offset'    			=> 0,
				'more'      			=> 0,  // to stop load more process
				'status'    			=> true,
			);
			$this->format_json($data);
		}

		// remove duplicate rows from chat_betweens
		$chat_data  = [];
		
		foreach($chat_between_guest as $key => $value)
		{
			if(empty($chat_data))
			{
				$chat_data[] = $value;
			}
			else
			{
				$duplicate = false;

				foreach($chat_data as $key1 => $value1)
				{
					if($value1->m_from == $value->m_to && $value1->m_to == $value->m_from && $value1->g_from == $value->g_to && 		$value1->g_to == $value->g_from
					)
					{
						$duplicate = true;			
					}
				}
				if(!$duplicate)
					$chat_data[] = $value;
			}
		}
		
		$data = array(
			'status' 				=> true,
			'offset'    			=> $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'],
			'more'      			=> 1,  // to stop load more process
			'chat_between_guest' 	=> $chat_data,
		);

		$this->format_json($data);
	}		
	
	/**
	 *   get conversation of between to  users
	 */
	public function a_get_guest_conversations()
	{
		//check admin authentication
		$this->check_admin(true);

		$user_id	    = (int) $_POST['user_id'];
		$guest_id		= (int)	$_POST['guest_id'];

		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $_POST['offset'];

		$total_messages 			= $this->AC_LIB->addchat_db_lib->a_get_guest_conversations($user_id, $guest_id, $filters, true);

		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] :	0;

			
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}

		$conversations 	= $this->AC_LIB->addchat_db_lib->a_get_guest_conversations($user_id, $guest_id, $filters);

		if(empty($conversations))
        {
			$data       = array(
				'conversations'  => array(),
				'offset'    	 => 0,
				'more'      	 => 0,  // to stop load more process
				'status'         => true,
			);
            $this->format_json($data);
		}
	
		$data       = array(
			'conversations'  	=> $conversations,
			'status'    		=> true,
			'more'				=> $more,	// to continue load more process
			'offset'			=> $filters['offset'],
		);
		$this->format_json($data);
	}

    /* ========== ADMIN PANEL APIs end==========*/

	

	/* ========== Guest System APIs start==========*/

	/**
	 *  Guest user login
	 */ 
	public function guest_login()
	{
		$this->AC_LIB->form_validation
		->set_rules('fullname', lang('fullname'), 'required|trim|max_length[256]|min_length[3]')
		->set_rules('email', lang('email'), 'trim|required');

		// manually validate email
		if(!$this->isValidEmail($this->AC_LIB->input->post('email')))
			$this->AC_LIB->form_validation
			->set_rules('email', lang('email'), 'required|max_length[0]', ['max_length' => lang('invalid').' '.lang('email')]);

        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
		}
		
		$params =  [
			'fullname' 		=> $_POST['fullname'],
			'email'    		=> $_POST['email'],
			'dt_created'	=> date('Y-m-d H:i:s'),
			'dt_updated'    => date('Y-m-d H:i:s'),
		];
		
		$guest_user      = $this->AC_LIB->addchat_db_lib->guest_login($params);	
		
		$this->AC_LIB->load->library('encryption');
		$this->AC_LIB->encryption->initialize(
			array(
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => 'adkahjdhkasjdh29378aadsjkasdh'
			)
		);
		$guest_user_id 	 = $this->AC_LIB->encryption->encrypt($guest_user->id);

		$data = [
			'guest_user_id' 		=> $guest_user_id,
			'guest_user_email'		=> $guest_user->email,
			'guest_user_fullname'	=> $guest_user->fullname,
			'status'                => true
		];
		
		$this->format_json($data);
	}

	/**
	 *  check guest user exist or not and match id
	 */

	public function get_guest_user()
	{
		$this->AC_LIB->load->library('encryption');
		$this->AC_LIB->encryption->initialize(
			array(
					'cipher' => 'aes-256',
					'mode' => 'ctr',
					'key' => 'adkahjdhkasjdh29378aadsjkasdh'
			)
		);

		
		$guest_user_id  = null;
		if($_POST['guest_user_id'])
			$guest_user_id = $this->AC_LIB->encryption->decrypt($_POST['guest_user_id']);

		$guest_user      = $this->AC_LIB->addchat_db_lib->get_guest_user($guest_user_id);
		
		$group_users  = [];
		
		// get guest group users	
		$group_users  		= (array)$this->get_guest_group_users();

		// get admin user
		if(!empty($this->AC_SETTINGS->admin_user_id))
		{
			$admin_guest        = $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->admin_user_id, 0, $this->AC_SETTINGS->guest_group_id);

			if(!empty($group_users && !empty($admin_guest)))
				array_push($group_users, $admin_guest);
			else if(empty($group_users) && !empty($admin_guest))
				$group_users = 	$admin_guest;
		}

		$guest_group_name  = null;

		if(!empty($this->AC_SETTINGS->guest_group_id))
		{
			$guest_group = $this->AC_LIB->addchat_db_lib->get_chatgroups([$this->AC_SETTINGS->guest_group_id]);

			if(!empty($guest_group))
				$guest_group_name = $guest_group[0]->name;
		}	
		
		if(empty($guest_user))
			$this->format_json(['status' => false, 'guest_group_users' => $group_users, 'guest_group_name' => $guest_group_name]);

		$this->format_json(['status' => true, 'guest_group_users' => $group_users, 'guest' => $guest_user,  'guest_group_name' => $guest_group_name]);
	}

	/**
	 *  guest user can send message
	 */
	public function guest_send_message()
	{
		$this->AC_LIB->form_validation
		->set_rules('guest_id', lang('guest'), 'required')
		->set_rules('message', lang('message'), 'required|trim|max_length[1000]');
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
		}

		$this->AC_LIB->load->library('encryption');
		$this->AC_LIB->encryption->initialize(
			array(
					'cipher' => 'aes-256',
					'mode' => 'ctr',
					'key' => 'adkahjdhkasjdh29378aadsjkasdh'
			)
		);

		$guest_id     = $this->AC_LIB->encryption->decrypt($_POST['guest_id']);

		$guest_user   = $this->AC_LIB->addchat_db_lib->get_guest_user($guest_id);		

		if(empty($guest_user))
			$this->format_json(['status' => false]);

		$group_users  = [];
		// get guest group users	
		$group_users  		= (array)$this->get_guest_group_users();

		// get admin user
		if(!empty($this->AC_SETTINGS->admin_user_id))
		{
			$admin_guest        = $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->admin_user_id, 0, $this->AC_SETTINGS->guest_group_id);

			if(!empty($group_users && !empty($admin_guest)))
				array_push($group_users, $admin_guest);
			else if(empty($group_users) && !empty($admin_guest))
				$group_users = 	$admin_guest;
		}
		
		if(empty($group_users))
			$this->format_json(array('status' => false));
		
		$messages	  		= [];
		$notification       = [];

		$g_random  			= time().rand(1,988);
		foreach($group_users as $key => $value)
		{
			$messages[$key]['m_to']  	    	= $value->id;
			$messages[$key]['message']  		= $_POST['message'];
			$messages[$key]['g_from']   		= $guest_user->id;
			$messages[$key]['m_from'] 			= 0;
			$messages[$key]['is_read'] 			= 0;
			$messages[$key]['dt_updated']   	= date('Y-m-d H:i:s');
			$messages[$key]['g_random']   		= $g_random;
			$notification[$key]['m_to']			= $value->id;
			$notification[$key]['g_from']		= $guest_user->id;

		}
		
		$status  =  $this->AC_LIB->addchat_db_lib->guest_send_message($messages);

		// 2. set_notification for guest
		$this->AC_LIB->addchat_db_lib->set_guest_notification($notification);

		if(empty($status))
			$this->format_json(array('status' => false));

		$data  = [
			'message'  		=> $messages[0],
			'status'   		=> true,
			
		];		
	
		$this->format_json($data);		
	}

	/*
    * get guest users for login user if login user have guest group  
    */
    public function get_guests($offset = 0)
    {
		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;
		$filters['search']          = (string) $this->AC_LIB->input->post('search');

		$params  = [
			'filters'  => 	$filters,
		];

		$guest_users      				= 	$this->AC_LIB->addchat_db_lib->get_guests($params);
		if(empty($guest_users))
        {
            $data       = array(
                            'guest_users'  	=> array(),
                            'offset'    => 0,
							'more'      => 0,  // to stop load more process
							'status'    => true,
                        );
            $this->format_json($data);
		}

        $data                       = array();
        $data['guest_users'] 		= $guest_users;
		$data['offset']             = $filters['offset'] == 0 ? $filters['limit'] : $filters['limit']+$filters['offset'];
		$data['more']               = 1;  // to continue load more process
		$data['status'] 			= true;

		$this->format_json($data);
	}

	/*
	* Get messages get_messages for login user
	*/
	public  function get_guest_messages($guest_id = null, $offset = 0)
	{
		$guest_id         			= (int) $guest_id;
		
		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $offset;

		$total_messages 			= $this->AC_LIB->addchat_db_lib->get_guest_messages($this->AC_SETTINGS->logged_user_id, $guest_id, $filters);
		
		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] :	0;
			
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}
		
		$messages 			= $this->AC_LIB->addchat_db_lib->get_guest_messages($this->AC_SETTINGS->logged_user_id, $guest_id, $filters, true);

		if(empty($messages))
        {
			$data       = array(
				'messages'  => array(),
				'offset'    => 0,
				'more'      => 0,  // to stop load more process
				'status'    => true,
			);
            $this->format_json($data);
		}

		// remove notification
		
		$this->AC_LIB->addchat_db_lib->remove_guest_notification(array('m_to'=>$this->AC_SETTINGS->logged_user_id, 'g_from'=>$guest_id));

		$data 					= array();
		$data['messages'] 		= array();
		foreach ($messages as $key => $message) 
		{
			$data['messages'][$key]['message_id'] 			= $message->id;
			$data['messages'][$key]['sender'] 				= $message->m_from;
			$data['messages'][$key]['recipient'] 			= $message->m_to;
			$data['messages'][$key]['message'] 				= $message->message;
			$data['messages'][$key]['is_read'] 				= $message->is_read;
			$data['messages'][$key]['attachment'] 			= $message->attachment;
			$data['messages'][$key]['dt_updated'] 			= $message->dt_updated; 
		}
		$data['offset']				= $filters['offset'];			
		$data['more']               = $more;  // to continue load more process
		$data['status'] 			= true;

		$this->format_json($data);
	}

	
	/**
	 * Get guest buddy for login user
	 */

	public function get_guest_buddy()
	{
		/* Validate form input */
        $this->AC_LIB->form_validation
		->set_rules('guest_id', lang('guest'), 'trim|is_natural_no_zero');
		
		if($this->AC_LIB->form_validation->run() === FALSE)
        {
        	$this->format_json(array('status' => false, 'response'=> validation_errors()));
		}
		   
		$data				= array();
		$guest_user_id 		= (int) $this->AC_LIB->input->post('guest_id');

		$guest_buddy 	    = $this->AC_LIB->addchat_db_lib->get_guest_user($guest_user_id);

		$data['buddy']		=	$guest_buddy;
		$data['status']		=	true;
		$this->format_json($data);
	}

	/**
	 *	 login user send message to guest users  
	 */
    public function send_to_guest()
    {
		/* Validate form input */
        $this->AC_LIB->form_validation
        ->set_rules('guest_id', lang('guest'), 'required')
    	->set_rules('message', lang('message'), 'required|trim|max_length[1000]');
		
        if($this->AC_LIB->form_validation->run() === FALSE)
        {
       		$data = array('status' => false, 'response'=> validation_errors());
			$this->format_json($data);
        }

		$guest_id 		= (int) $this->AC_LIB->input->post('guest_id');
		$message 		= nl2br($this->AC_LIB->input->post('message'));
		$g_random  	    = time().rand(1,988);

        // return null if guest or message is empty
        if(!$message || !$guest_id)
			$this->format_json(['status' => false,'response' => 'N/A']);

		// guest messages for pusher
		$guest_messages     = [];
		
        $msg    = array(
                    "m_from"		=> $this->AC_SETTINGS->logged_user_id,
                    "g_to" 			=> $guest_id,
                    "message" 		=> $message,
                    "dt_updated" 	=> date('Y-m-d H:i:s'),
                    'g_random'	    => $g_random,
                    
                );
        $msg_id = $this->AC_LIB->addchat_db_lib->send_to_guest($msg);

        if(empty($msg_id))
            $this->format_json(['status' => false]);

        $notification 			   = [];
        $notification[0]['m_from'] = $this->AC_SETTINGS->logged_user_id;
        $notification[0]['g_to']   = $guest_id;

        
        // 2. set_notification
        $this->AC_LIB->addchat_db_lib->set_guest_notification($notification);
            
        
        $chat = array(
            'message_id' 		=> $msg_id,
            'sender' 			=> $msg['m_from'], 
            'recipient' 		=> $msg['g_to'],
            'message' 			=> $msg['message'],
            'dt_updated' 		=> $msg['dt_updated'],
            'is_read' 			=> 0,
            
        );

        //it is for pusher notification
        $guest_messages = array(
            'message_id' 		=> $msg_id,
            'm_from' 			=> $msg['m_from'], 
            'g_to' 				=> $msg['g_to'],
            'g_from'            => 0,
            'm_to'            	=> 0,
            'message' 			=> $msg['message'],
            'dt_updated' 		=> $msg['dt_updated'],
            'is_read' 			=> 0,
            
        );
        $data = array(
            'status' 	=> true,
            'message' 	=> $chat, 	  
            'guest_messages' => $guest_messages
        );
				
		//add the header here
		$this->format_json($data);
    }

	/*
	*  guest can see all messages of guest group users with his messages 
	*/
	public  function get_messages_of_guest()
	{
		$guest_id         			= $_POST['guest_id'];

		$this->AC_LIB->load->library('encryption');
		$this->AC_LIB->encryption->initialize(
			array(
					'cipher' => 'aes-256',
					'mode' => 'ctr',
					'key' => 'adkahjdhkasjdh29378aadsjkasdh'
			)
		);

		$guest_id     				= $this->AC_LIB->encryption->decrypt($guest_id);
		
		if(empty($guest_id))
			$this->format_json(['status' => false]);

		$guest_user                 = $this->AC_LIB->addchat_db_lib->get_guest_user($guest_id);		

		if(empty($guest_user))
			$this->format_json(['status' => false]);
		
		// filters
		$filters                    = array();
		$filters['limit']           = $this->AC_SETTINGS->pagination_limit;
		$filters['offset']          = (int) $_POST['offset'];
	
		$total_messages 			= $this->AC_LIB->addchat_db_lib->get_messages_of_guest($guest_user->id, $filters);
		
		// 1st case
		if($filters['offset'] == 0)
			$filters['offset']		= $total_messages > $filters['limit'] ? $total_messages - $filters['limit'] :	0;
			
		else
			$filters['offset']		= $filters['offset'] - $filters['limit'];

		// last case
		$more = 1;
		if($filters['offset'] < 0 || $filters['offset']==0)
		{
			$filters['limit']  		= $filters['limit'] - $filters['offset'];
			$filters['offset'] 		= 0;
			$more = 0;
		}
		
		$messages 			= $this->AC_LIB->addchat_db_lib->get_messages_of_guest($guest_id, $filters, true);
		
		if(empty($messages))
        {
			$data       = array(
				'messages'  => array(),
				'offset'    => 0,
				'more'      => 0,  // to stop load more process
				'status'    => true,
			);
            $this->format_json($data);
		}

		// remove notification
		$this->AC_LIB->addchat_db_lib->remove_guest_notification(array( 'g_to'=>$guest_id));

		$data 					= array();
		$data['messages'] 		= array();
		foreach ($messages as $key => $message) 
		{
			$data['messages'][$key]['message_id'] 			= $message->id;
			$data['messages'][$key]['m_from'] 				= $message->m_from;
			$data['messages'][$key]['m_to'] 				= $message->m_to;
			$data['messages'][$key]['g_from'] 			    = $message->g_from;
			$data['messages'][$key]['g_to'] 			    = $message->g_to;
			$data['messages'][$key]['message'] 				= $message->message;
			$data['messages'][$key]['is_read'] 				= $message->is_read;
			$data['messages'][$key]['dt_updated'] 			= $message->dt_updated;
			$data['messages'][$key]['m_from_image'] 	    = $message->m_from_image;
			$data['messages'][$key]['m_from_name'] 	        = $message->m_from_name; 
		}
		
		$data['offset']				= $filters['offset'];			
		$data['more']               = $more;  // to continue load more process
		$data['status'] 			= true;

		$this->format_json($data);
	}

	/**
	 *  get guest notification 
	 */

	public function get_guest_updates()
	{
		$guest_notifications = [];
		// notification for loing user
		if(!empty($this->AC_SETTINGS->logged_user_id))
		{
			$guest_notifications     = $this->AC_LIB->addchat_db_lib->get_guest_updates($this->AC_SETTINGS->logged_user_id);
		}	
		else
		{	
			// notification for guests user
			$this->AC_LIB->load->library('encryption');
			$this->AC_LIB->encryption->initialize(
				array(
						'cipher' => 'aes-256',
						'mode' => 'ctr',
						'key' => 'adkahjdhkasjdh29378aadsjkasdh'
				)
			);

			$guest_id     			= $this->AC_LIB->encryption->decrypt($_POST['guest_id']);
			$guest_user   			= $this->AC_LIB->addchat_db_lib->get_guest_user($guest_id);
			if(empty($guest_user))
				$this->format_json(array('status' => false, 'response'=> lang('guest').' '.lang('not_found')));

			$guest_notifications    = $this->AC_LIB->addchat_db_lib->get_guest_updates($this->AC_SETTINGS->logged_user_id, $guest_user->id);
		}

		
		// stop sending notification if in case of same notification
		$is_same = false;
		if(!empty($_POST['notification']))
		{
			$post['notification'] = json_decode($_POST['notification'], true);
			$guest_notifications  = json_encode($guest_notifications);
			$guest_notifications  = json_decode($guest_notifications, true);
			
			// check notification same or not
			$difference = $this->multi_array_diff($guest_notifications, $post['notification']);
			
			// if have no difference then is_same will be true
			if(!$difference)
				$is_same = true;
		}
			
		// if no messages then do nothing
	    if(empty($guest_notifications) || $is_same)
	    {
	   		$this->format_json(array('status' => false, 'response'=> 'N/A'));
		}
		
		$this->format_json(array('status' => true, 'guest_notifications' => $guest_notifications));
	}

	/*
    * Get latest message for guest user when  guest login
    */
    public function get_guest_latest_message($guest_id = null)
	{
		// check is logged-in
        $this->check_auth();

		$guest_id 	= (int) $guest_id;
		$messages 	= array();
		if($guest_id)
		{
			$messages 	= $this->AC_LIB->addchat_db_lib->get_guest_latest_message($this->AC_SETTINGS->logged_user_id, $guest_id);
			
			// if any new message then remove the specific notification
			// remove notification
			$this->AC_LIB->addchat_db_lib->remove_guest_notification(array('m_to'=>$this->AC_SETTINGS->logged_user_id, 'g_from'=>$guest_id));

		}

		// if no messages then do nothing
	    if(empty($messages))
	    {
	   		$this->format_json(array('status' => false, 'response'=> 'N/A'));
		}

		$this->format_json(array('status' => true, 'messages' => $messages));
	}

	/*
	* Get latest message for guest user when guest without login
	 */
    public function get_guest_latest_message1()
	{
		// guest id
		$this->AC_LIB->load->library('encryption');
		$this->AC_LIB->encryption->initialize(
			array(
					'cipher' => 'aes-256',
					'mode' => 'ctr',
					'key' => 'adkahjdhkasjdh29378aadsjkasdh'
			)
		);

		$guest_id     = $this->AC_LIB->encryption->decrypt($_POST['guest_user_id']);

		$guest_user   = $this->AC_LIB->addchat_db_lib->get_guest_user($guest_id);	
		
		if(empty($guest_user))
			$this->format_json(['status' => false]);

		
		$messages 	= array();

		if($guest_id)
		{
			$messages 	= $this->AC_LIB->addchat_db_lib->get_guest_latest_message($this->AC_SETTINGS->logged_user_id, $guest_id);

		}

		// if no messages then do nothing
	    if(empty($messages))
	    {
	   		$this->format_json(array('status' => false, 'response'=> 'N/A', 'messages' => null ));
		}

		// remove notification
		$this->AC_LIB->addchat_db_lib->remove_guest_notification(array( 'g_to'=>$guest_id));

		$this->format_json(array('status' => true, 'messages' => $messages));
	}
	
	/**
	 * if user is logged in then
	 *  check user have login account or not 
	 */
	public function check_guest_account()
	{
		// guest id
		$this->AC_LIB->load->library('encryption');
		$this->AC_LIB->encryption->initialize(
			array(
					'cipher' => 'aes-256',
					'mode' => 'ctr',
					'key' => 'adkahjdhkasjdh29378aadsjkasdh'
			)
		);

		$guest_id     = $this->AC_LIB->encryption->decrypt($_POST['guest_id']);

		$guest_user   = $this->AC_LIB->addchat_db_lib->get_guest_user($guest_id);	
		
		if(empty($guest_user))
			$this->format_json(['status' => false]);

        // in case if a guest user has registered and login with the same email
        // then merger the user conversations of guest + login user
		$logged_user 		= $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->logged_user_id, 0, $this->AC_SETTINGS->guest_group_id);
		if($logged_user->email == $guest_user->email)
		{
			$params  = [
				'guest_id' 	 		=> $guest_user->id,
				'logged_user_id'    => $this->AC_SETTINGS->logged_user_id,
			];
			
			// update ac_guest_messages table because now guest user have became login user
			$this->AC_LIB->addchat_db_lib->update_guest_messages($params);

			// get one user id of guest group user
			// insert that user id to this guest to registered user id contact list
			// get guest group users
			$group_users  = $this->get_guest_group_users();
			
			if(!empty($group_users))
			{
				$this->AC_LIB->addchat_db_lib
                ->create_contacts(array('users_id' => $this->AC_SETTINGS->logged_user_id, 'contacts_id' => $group_users[0]->id));
			}						
			
			$this->format_json(['status' => true]);
		}

		$this->format_json(['status' => false]);
	}

	/**
	 * ====================== Guest user function End==========================================================
	 */



	/* ===================== Pusher Notification ===================== */
	/**
	 *  check user authorization
	 */
	public function auth()
	{
        // autoload composer files
        require(APPPATH . "/vendor/autoload.php");


        $pusher_key      = str_replace('"', '',$this->AC_SETTINGS->pusher_key);
		$pusher_secret	 = str_replace('"', '',$this->AC_SETTINGS->pusher_secret);
		$pusher_app_id   = str_replace('"', '',$this->AC_SETTINGS->pusher_app_id);
		$pusher_cluster  = str_replace('"', '',$this->AC_SETTINGS->pusher_cluster);
		
		if(empty($this->AC_SETTINGS->logged_user_id) && empty($_POST['guest_id']))
		{
			echo(lang('login_first'));
			die();
		}
	
		$socket_id  	= $_POST['socket_id'];
		$channel_name    = $_POST['channel_name'];
		
		$pusher = new \Pusher\Pusher($pusher_key, $pusher_secret, $pusher_app_id , array('cluster' => $pusher_cluster));
		
		
		$auth  = $pusher->socket_auth($channel_name, $socket_id);
		
		echo($auth);
	}

	/**
	 *   real time message send notification
	 */
	public function message_notification()
	{
		// pusher intialization
		$pusher = $this->pusher_init();
		
		// get notification
		$notification 	= $this->AC_LIB->addchat_db_lib->get_updates($_POST['buddy_id']);

		$data  = [
            'message' 	  =>	json_decode($_POST['latest_message']), 
            'notification' =>	$notification,
            'status'       =>   true,
		];

		$pusher->trigger('private-message.'.$_POST['buddy_id'], 'message-send', $data);

		$this->format_json(['status' =>true]);
	}

	/**
	 * 	is typing
	 */
	public function is_typing()
    {
		// pusher intialization
		$pusher = $this->pusher_init();
		
		$pusher->trigger('private-typing.'.$_POST['buddy_id'], 'is-typing', array('typing_user' => json_decode($_POST['typing_user'])));

		$this->format_json(['status' =>true]);
	}
	
	/**
	 *   remove notification if chat box is opened
	 */
	public function remove_notifications()
	{
		// if any new message then remove the specific notification
		// remove notification
		$this->AC_LIB->addchat_db_lib->remove_notification(array('buddy_id'=>$this->AC_SETTINGS->logged_user_id, 'users_id'=>$_POST['buddy_id']));

		// get notification
		$notification 	= $this->AC_LIB->addchat_db_lib->get_updates($this->AC_SETTINGS->logged_user_id);

		$this->format_json(['notifications' =>$notification]);
	}

	/**
	 *   is_read _update
	 */
	public function is_read()
	{	
		// pusher intialization
		$pusher = $this->pusher_init();

		$this->AC_LIB->addchat_db_lib->is_read($this->AC_SETTINGS->logged_user_id, $_POST['buddy_id']);

		$data['status'] 			= true;
		
		$pusher->trigger('private-read.'.$_POST['buddy_id'], 'is-read', array('data' => $data, 'buddy_id' => $this->AC_SETTINGS->logged_user_id));

		$this->format_json(['status' =>true]);
		
	} 

	//========================= Puhser notificatin for guest============================================

	/**
	 *   real time message send notification for guest
	 */
	public function guest_message_notification()
	{
		
		// pusher intialization
		$pusher = $this->pusher_init();

		$guest_messages      = [];
		$guest_notifications = [];

		$guest_messages	 = (array)json_decode($_POST['latest_message']);

		
		// notification for loing user
		if(!empty($this->AC_SETTINGS->logged_user_id))
		{
			$guest_notifications    = $this->AC_LIB->addchat_db_lib->get_guest_updates(null, $_POST['guest_id']);
			$user                   = $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->logged_user_id, 0, $this->AC_SETTINGS->guest_group_id);
			
			$guest_messages['m_from_image']    = $user->avatar;
			$guest_messages['m_from_name']    = $user->fullname;
			
			
			$data  = [
				'guest_messages' 	  =>	$guest_messages, 
				'guest_notifications' =>	$guest_notifications,
				'status'              =>    true,
			];
			
			$pusher->trigger('private-guest-message.'.'g_'.$_POST['guest_id'], 'guest-message-send', $data);
		}	
		else
		{	
			$group_users  = [];
			// get guest group users	
			$group_users  		= (array)$this->get_guest_group_users();

			// get admin user
			if(!empty($this->AC_SETTINGS->admin_user_id))
			{
				$admin_guest        = $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->admin_user_id, 0, $this->AC_SETTINGS->guest_group_id);

				if(!empty($group_users && !empty($admin_guest)))
					array_push($group_users, $admin_guest);
				else if(empty($group_users) && !empty($admin_guest))
					$group_users = 	$admin_guest;
			}
			
			if(empty($group_users))
				$this->format_json(array('status' => false));


			foreach($group_users as $key => $value)
			{
				$guest_notifications     = $this->AC_LIB->addchat_db_lib->get_guest_updates($value->id);

				$data  = [
					'guest_messages' 	  =>	$guest_messages, 
					'guest_notifications' =>	$guest_notifications,
					'status'              =>    true,
				
				];
				$pusher->trigger('private-guest-message.'.'lg_'.$value->id, 'guest-message-send', $data);
			}	
		}

		$this->format_json(['status' =>true]);
	}

	/**
	 *  remove guest notification
	 */
	public function remove_guest_notifications()
	{
		$guest_id 		= $_POST['guest_id'];

		$guest_user     = $this->AC_LIB->addchat_db_lib->get_guest_user($guest_id);

		if(empty($guest_user))
			$this->format_json(array('status' => false, 'response'=> lang('guest').' '.lang('not_found')));

		$guest_notifications = [];
		
		// notification for loing user
		if(!empty($this->AC_SETTINGS->logged_user_id))
		{
			// remove notification
			$this->AC_LIB->addchat_db_lib->remove_guest_notification(array('m_to'=>$this->AC_SETTINGS->logged_user_id, 'g_from'=>$guest_id));
			
			$guest_notifications     = $this->AC_LIB->addchat_db_lib->get_guest_updates($this->AC_SETTINGS->logged_user_id);
		}	
		else
		{
			// remove notification
			$this->AC_LIB->addchat_db_lib->remove_guest_notification(array( 'g_to'=>$guest_id));	
			$guest_notifications    = $this->AC_LIB->addchat_db_lib->get_guest_updates($this->AC_SETTINGS->logged_user_id, $guest_user->id);
		}

		$this->format_json(['guest_notifications' =>$guest_notifications]);
		
	}

	/**
	 *   is_read _update
	 */
	public function is_read_guest()
	{	
		// pusher intialization
		$pusher = $this->pusher_init();

		$this->AC_LIB->addchat_db_lib->is_read_guest($this->AC_SETTINGS->logged_user_id, $_POST['guest_id']);

		if(empty($this->AC_SETTINGS->logged_user_id))
		{
			$group_users  = [];
			// get guest group users	
			$group_users  		= (array)$this->get_guest_group_users();

			// get admin user
			if(!empty($this->AC_SETTINGS->admin_user_id))
			{
				$admin_guest        = $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->admin_user_id, 0, $this->AC_SETTINGS->guest_group_id);

				if(!empty($group_users && !empty($admin_guest)))
					array_push($group_users, $admin_guest);
				else if(empty($group_users) && !empty($admin_guest))
					$group_users = 	$admin_guest;
			}

			if(empty($group_users))
				$this->format_json(array('status' => false));

			$data['status'] 			= true;

			foreach($group_users as $key => $value)
			{
				$pusher->trigger('private-guest-read.'.'lg_'.$value->id, 'guest-is-read', array('data' => $data, 'guest_id' => $_POST['guest_id']));	
			}
		}
		else
		{	
			$data['status'] 			= true;
			$pusher->trigger('private-guest-read.'.'g_'.$_POST['guest_id'], 'guest-is-read', array('data' => $data));	
		}	
		
		$this->format_json(['status' =>true]);
	} 


	/**
	 * 	is typing guest
	 */
	public function is_typing_guest()
    {
		
		// pusher intialization
		$pusher = $this->pusher_init();

		$this->AC_LIB->addchat_db_lib->is_read_guest($this->AC_SETTINGS->logged_user_id, $_POST['guest_id']);

		if(empty($this->AC_SETTINGS->logged_user_id))
		{
			$group_users  = [];
			// get guest group users	
			$group_users  		= (array)$this->get_guest_group_users();

			// get admin user
			if(!empty($this->AC_SETTINGS->admin_user_id))
			{
				$admin_guest        = $this->AC_LIB->addchat_db_lib->get_user($this->AC_SETTINGS->admin_user_id, 0, $this->AC_SETTINGS->guest_group_id);

				if(!empty($group_users && !empty($admin_guest)))
					array_push($group_users, $admin_guest);
				else if(empty($group_users) && !empty($admin_guest))
					$group_users = 	$admin_guest;
			}

			if(empty($group_users))
				$this->format_json(array('status' => false));

			$data['status'] 			= true;

			foreach($group_users as $key => $value)
			{
				$pusher->trigger('private-guest-typing.'.'lg_'.$value->id, 'guest-is-typing', array('guest_typing_user' => json_decode($_POST['guest_typing_user'])));
			}
		}
		else
		{	
			$data['status'] 			= true;
				
			$pusher->trigger('private-guest-typing.'.'g_'.$_POST['guest_id'], 'guest-is-typing', array('guest_typing_user' => json_decode($_POST['guest_typing_user']), 'guest_id' => $_POST['guest_id']));	
		}	

		$this->format_json(['status' =>true]);
		
	}
	
	// -------- Pusher notification end for guest -------------

	/* ===================== Pusher Notification End ===================== */


    /* ========== PRIVATE HELPER FUNCTIONS ==========*/
	/**
    * Upload File
    */
    private function upload_file($data = array())
    {
        $this->AC_LIB->load->library(array('upload', 'image_lib'));
        
        $config                         = array();
        $config['allowed_types']        = 'jpg|JPG|jpeg|JPEG|png|PNG';
        $config['size']                 = '8388608';
        $config['file_ext_tolower']     = TRUE;
        $config['overwrite']            = TRUE;
        $config['remove_spaces']        = TRUE;
        $config['upload_path']          = './'.$data['folder'].'/';
        
        if (!is_dir($config['upload_path']))
            mkdir($config['upload_path'], 0777, TRUE);
        
        $filename                       = time().rand(1,988);
        $extension                      = strtolower(pathinfo($_FILES[$data['input_file']]['name'], PATHINFO_EXTENSION));
        
        // original file for resizing
        $config['file_name']            = $filename.'_large'.'.'.$extension;

        // file name for further use
        $filename                       = $filename.'.'.$extension;
        
        $this->AC_LIB->upload->initialize($config);

        if (! $this->AC_LIB->upload->do_upload($data['input_file'])) 
        {            
            // remove all uploaded files in case of error
            $this->reset_file($config['upload_path'], $filename);
            return array('error' => $this->AC_LIB->upload->display_errors());
        }

        // cropped thumbnail
        $thumb                          = array();
        $thumb['image_library']         = 'gd2';
        $thumb['source_image']          = $config['upload_path'].$config['file_name'];
        $thumb['new_image']             = $config['upload_path'].$filename;
        $thumb['maintain_ratio']        = TRUE;
        $thumb['width']                 = 800;
        $thumb['height']                = 600;
        $thumn['quality']               = 50;
        $thumb['file_permissions']      = 0644;
        
        $this->AC_LIB->image_lib->initialize($thumb);  
        
        if (! $this->AC_LIB->image_lib->resize()) 
        {
            $this->reset_file($config['upload_path'], $filename);
            return array('error' => $this->AC_LIB->image_lib->display_errors());
        }

        $this->AC_LIB->image_lib->clear();        

        // remove the original image
        unlink($config['upload_path'].$config['file_name']);
        
        return $filename;
        
    } 

    /**
     * Reset File
    */
    private function reset_file($path = '', $data = '')
    {
        if(file_exists($path.$data))
            @unlink($path.$data);
        
        return 1;
	}
	
    /**
     * Validate email
    */
	private function isValidEmail($email)
    {
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}

    /**
     * Check if user logged in
    */
    private function check_auth()
    {
        if(!$this->AC_SETTINGS->logged_user_id) 
    		$this->format_json(array('status' => false, 'response'=> lang('access_denied')));

        return true;
    }

    /**
     * Echo json
    */
    private function format_json($data = array())
	{
		header('Content-Type: application/json');
		echo json_encode($data);
		exit;
	}

    /**
	 * Get Support Group 
     * users who can chat with guest user 
	 */
	private function get_guest_group_users()
	{
		$guest_group_id		= $this->AC_SETTINGS->guest_group_id;
		$group_users_ids 	= $this->AC_LIB->addchat_db_lib->get_guest_group_users_ids($guest_group_id);
		if(empty($group_users_ids))
			return array();

		$users_ids = [];
		foreach($group_users_ids as $key => $value)
			$users_ids[$key] = $value->user_id;

		$group_users 		= $this->AC_LIB->addchat_db_lib->get_guest_group_users($users_ids);

		return  $group_users;
	}

    /**
	 * Pusher intialization
	 */
	private function pusher_init()
	{
		// autoload composer files
        require(APPPATH . "/vendor/autoload.php");

		$pusher_key      = str_replace('"', '',$this->AC_SETTINGS->pusher_key);
		$pusher_secret	 = str_replace('"', '',$this->AC_SETTINGS->pusher_secret);
		$pusher_app_id   = str_replace('"', '',$this->AC_SETTINGS->pusher_app_id);
		$pusher_cluster  = str_replace('"', '',$this->AC_SETTINGS->pusher_cluster);
		
		$pusher = new Pusher\Pusher($pusher_key, $pusher_secret, $pusher_app_id, array('cluster' => $pusher_cluster));

		return $pusher;
	}

	/**
     *  Detect demo mode
    */
    private function demo_mode()
    {
        $domain = strtolower($_SERVER['SERVER_NAME']);
        if (strpos($domain, 'classiebit.com') !== FALSE || strpos($domain, 'addchat-codeigniter.test') !== FALSE)
            return true;
        
        return FALSE;
	}
	
	/**
	 *  multi_array_diff
	 */  
	public function multi_array_diff($arraya, $arrayb)
	{
		foreach ($arraya as $keya => $valuea) 
		{
			if(!in_array($valuea, $arrayb))
			{
				return true;
			}
		}
		return false;
	}

}
/*End Addchat_lib Class*/