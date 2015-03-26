<?php

namespace Anax\Users;

/**
 * Model for users
 *
 */
class User extends \Anax\MVC\CDatabaseModel
{
		/**
		 * Return true or false if user isset.
		 *
		 */
		public function loginStatus()
		{
				$user = $this->di->session->get('user');
				if(!isset($user)){
						return false;
				}
				else{
						return true;
				}
		}
		
		/**
		 * Redirect to login page if not logged in.
		 * Used to secure pages.
		 */
		public function reqLoggedIn()
		{
				$user = $this->di->session->get('user');
				if(!isset($user)){
						$url = $this->url->create('account/login');
						$this->response->redirect($url);
				}
		}
		
		/**
		 * Redirect to account page if logged in
		 * Used to block logged in users from visiting login page etc.
		 */
		public function reqLoggedOut(){
				$user = $this->di->session->get('user');
				if(isset($user)){
						$url = $this->url->create('account/profile');
						$this->response->redirect($url);
				}
		}
		
		/**
		 * Get gravatar image by email.
		 * 
		 */
		public function gravatar($email, $size = 200)
		{
				$img_url = \Anax\Gravatar\Gravatar::get_gravatar($email);
				
				return $img_url;
		}

}