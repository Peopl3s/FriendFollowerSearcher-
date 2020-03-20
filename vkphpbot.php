<?php

ini_set('max_execution_time', 900);

class VkFriendFollowerSearcher
{
    private $token = '*************';
	private $u_id;
	
	function  __construct($user_id)
	{
        $this->u_id = $this->clean_var($user_id);
	}
	
    private function clean_var($var)
	{
         $var = strip_tags($var);
         $var = preg_replace('~\D+~', '', $var);        
         $var = trim($var);   
         return $var;
    }
	
	public function get_followers()
	{
		$received_peoples = array();
		$followersCount = $this->get_followers_count();
		
		while($followersCount > count($received_peoples))
		{
			$code = $this->get_vk_script_code_for_followers($followersCount, count($received_peoples));
			$followes = $this->get_json_decoded_content($code);
			$received_peoples = array_merge($received_peoples, $followes);
			usleep(333);
		}
		return $this->get_result($received_peoples);
	}
	
	private function get_followers_count()
	{
		$code = 'var followers =API.users.getFollowers({"user_id":"'.$this->u_id.'",  "v":"5.2", "count":1000}); '
				.'var count = followers.count; return count;';
				
		$countFollowers = (int)($this->get_json_decoded_content($code));
		return $countFollowers;
	}
	
	private function get_vk_script_code_for_followers( $total_followers_count, $received_followers)
	{
	    $code = 
				'var followers =API.users.getFollowers({"user_id":"'.$this->u_id.'",  "v":"5.2",  "count":1000, "offset":'.$received_followers.'}).items; '
				.'var offset = 1000;'
				.'do{'
				.'followers = followers + API.users.getFollowers({"user_id":"'.$this->u_id.'",  "v":"5.2", "count":1000, "offset":(offset+'.$received_followers.')}).items;'
				.'offset = offset + 1000; } while(offset < 25000 && (offset + '.$received_followers.') < '.$total_followers_count.');'
				.'return followers ;'; 
				
		return $code;
	}
	
	private function get_json_decoded_content($code)
	{
		$content = file_get_contents('https://api.vk.com/method/execute?code='.urlencode($code).'&v=5.2&access_token='.$this->token);
		$content = json_decode($content)->response;
		return $content;
	}
	
	private function get_result($data)
	{
		return (!isset($data->error))? $data : '';
	}
	
    public function get_friends() 
	{
		$code = $this->get_vk_script_code_for_friends();
		$friends = $this->get_json_decoded_content($code);
        return $this->get_result($friends);
    } 
	
	private function get_vk_script_code_for_friends()
	{	
	    $code = 
			'var friends=API.friends.get({"user_id":"'.$this->u_id.'",  "v":"5.2", "offset":0}).items;'
			.'var count = friends.count;'		
			.'friends = friends + API.friends.get({"user_id":"'.$this->u_id.'",  "v":"5.2", "offset":5000}).items;'
			.'return friends ;';
			
		return $code;
	}    
}
?>
