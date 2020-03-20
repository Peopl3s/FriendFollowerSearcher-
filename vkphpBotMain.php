<?php
require_once 'vkphpbot.php';

function write_data_in_file($data, $file_name = 'friend_follower.txt')
{
	$fd = fopen($file_name, 'a') or die("Failed to create a file");
	fwrite($fd, "\r\n");
	foreach($data as $element)
	{
		fwrite($fd, $element."\r\n");
	}
	fclose($fd);
}

function is_have_friends_and_followers($friends, $followers)
{
	return $friends != '' and $followers != '';
}

function is_correct_vk_user_id($vk_user_id)
{
	return $vk_user_id != '' and preg_match('/(https:\/\/vk.com\/)?(id\/)?([0-9a-z_]+)/',$vk_user_id); 
}

$vk_user_id = "http://vk.com/********"; 

if(is_correct_vk_user_id($vk_user_id))
{
	$vk = new VkFriendFollowerSearcher($vk_user_id);
	
	$friends = $vk->get_friends();
	$followers = $vk->get_followers();
	
	if (is_have_friends_and_followers($friends, $followers))
	{
		write_data_in_file($friends);
		write_data_in_file($followers);
		
	} else 
	{
		print("<center><h2 class='error'>The user is unavailable</h2></center>");	
	}
}
?>
