<?php

class ClientPlugin extends Plugin
{
	public function action_plugin_activation( $plugin_file )
	{
		Post::add_new_type( 'client' );
	}

	public function action_plugin_deactivation( $plugin_file )
	{
		Post::deactivate_post_type( 'client' );
	}

	public function filter_post_type_display($type, $foruse) 
	{ 
		$names = array( 
			'client' => array(
				'singular' => _t( 'Client', 'proposal' ),
				'plural' => _t( 'Clients', 'proposal' ),
			)
		); 
		return isset($names[$type][$foruse]) ? $names[$type][$foruse] : $type; 
	}

/*	public function action_form_publish_client( $form, $post )
	{
		$form->title->caption = 'Company Name';

		$user_options = array();
		foreach(Users::get_all() as $user) {
			$user_options[$user->id] = $user->displayname;
		}
		asort($user_options);
		$form->insert('content', new FormControlSelect('contact', $post, 'Staff', $user_options, 'admincontrol_select'));

	}*/

	public function action_form_user($form, User $edit_user)
	{
		$fieldset = $form->insert( 'change_password', 'wrapper', 'client_info', 'Client Information');
		$fieldset->class = 'container settings';
		$fieldset->append( 'static', 'client_info', '<h2>Client Information</h2>' );


		$clients = Posts::get(array('content_type' => 'client', 'status' => 'published'));
		$client_options = array();
		foreach($clients as $client) {
			$client_options[$client->id] = $client->title;
		}
		$fieldset->append(new FormControlSelect('client', $edit_user, 'Client', $client_options, 'optionscontrol_select'))->class[] = 'item clear';

		$fieldset->append(new FormControlText('phone', $edit_user, 'Contact Phone', 'optionscontrol_text'))->class[] = 'item clear';
	}

	public function filter_form_user_update($update, $form, $edit_user)
	{
		if($form->twitteruserid->value != $edit_user->info->twitter__user_id) {
			$edit_user->info->twitter__user_id = $form->twitteruserid->value;
			return true;
		}
		return $update;
	}

	public function filter_user_client($client, $user)
	{
		if(intval($user->info->client) != 0) {
			$client = Post::get(array(
				'id' => $user->info->client,
				'ignore_permissions' => true
			));
		}
		return $client;
	}
}

?>
