<?php
namespace Anax\Users;
/*
 * A controller for users & admin events
 *
 */
class AccountController implements \Anax\DI\IInjectionAware
{
		use \Anax\DI\TInjectable;
		
		private $user, $userData;
		
		/**
		 * Init
		 * 
		 */
		public function initialize()
		{
				$this->users = new \Anax\Users\User();
				$this->users->setDI($this->di);
				
				$this->questions = new \Anax\Ask\Vquestions();
				$this->questions->setDI($this->di);
				
				$this->answers = new \Anax\Ask\Answers();
				$this->answers->setDI($this->di);
				
				// Keep userData updated at all times.
				if($this->users->loginStatus()){
						$this->user = $this->session->get('user');
						$this->userData = $this->users->query()
																	 ->where('id = ?')
																	 ->execute([$this->user->id]);
				}
				
		}
		
		
		/**
		 * Show user account page.
		 *
		 */
		public function indexAction()
		{
				$this->users->reqLoggedIn();
				
				// Display questions made by user
			  $questions = $this->questions->query('vquestions.*, count(answers.id) AS answers')
												  ->leftJoin('answers', 'vquestions.id = answers.q_id')
												  ->Where('vquestions.creator = ?')
													->orderBy('vquestions.created DESC')
												  ->groupBy('vquestions.id')
												  ->execute([$this->userData[0]->acronym]);
													
				// Display answers made by user
				$answers = $this->answers->query('answers.*, vquestions.title AS q_title')
																 ->leftJoin('vquestions', 'answers.q_id = vquestions.id')
																 ->Where('answers.creator = ?')
																 ->groupBy('q_title')
																 ->limit('10')
																 ->execute([$this->userData[0]->acronym]);									
				
				$this->di->theme->setTitle("Mitt konto");
				$this->di->views->add('bowl/account/account', [
            'user'      => $this->userData[0],
						'questions' => $questions,
						'answers'   => $answers,
        ], 'main');
				$this->di->views->add('bowl/account/sidebar', [
						'name'     => $this->userData[0]->name,
						'img'      => $this->users->gravatar($this->userData[0]->email),
        ], 'sidebar');
		}
		
		
		/**
		 * Account settings
		 *
		 */
		public function settingsAction()
		{
				$this->di->session();
				$this->users->reqLoggedIn();
				
				$form = new \Mos\HTMLForm\CForm([], [
            'email' => [
                'type'        => 'text',
								'label'       => 'E-postadress:',
                'required'    => true,
								'value'       => $this->userData[0]->email,
                'validation'  => ['not_empty', 'email_adress', 								
								'custom_test' => [
									  'message' => 'E-postadressen är redan kopplad till ett konto.',
										'test'    => [$this, 'duplicateEmail']
										]],
								'class'				=> 'padding5',
            ],
						'name' => [
                'type'        => 'text',
								'label'       => 'Namn:',
								'value'       => $this->userData[0]->name,
                'required'    => true,
                'validation'  => ['not_empty', 'range_3_30', 'alpha_spaces', 'ctype_spaces'],
								'class'				=> 'padding5',
            ],
            'submit' => [
                'type'      => 'submit',
								'value'     => 'Spara',
								'class'		  => 'btn',
								'callback'  => function($form) {
                  $form->saveInSession = false;
                  return true;
                }
            ],
        ]);
				
				$status = $form->check();
				
				if($status === true) {	
					  $now = gmdate('Y-m-d H:i:s');
						$res = $this->users->save([
								'id'       => $this->userData[0]->id,
								'email'    => str_replace(' ', '', $form->Value('email')),
								'name'     => preg_replace('/\s{2,}/u', ' ', $form->Value('name')),
								'active'   => $now,
								'updated'  => $now,
						]);
						
						$message = '<output class="success">Du har uppdaterat dina kontoinställningar.</output>';
						$this->session->set('flash_msg', $message);
						$url = $this->url->create('account');
						$this->response->redirect($url);
						
				}
				else if($status === false) {
						$form->AddOutput('Ett eller flera fel uppstod vid valideringen, vänligen kolla att fälten ovan stämmer.');
						$url = $this->url->create('account/settings');
						$this->response->redirect($url);
				}
				
				$this->di->theme->setTitle("Kontoinställningar");
        $this->di->views->add('default/page', [
            'title' => "Kontoinställningar",
            'content' => $form->getHTML()
        ], 'main');
				$this->di->views->add('bowl/account/sidebar', [
						'name'     => htmlentities($this->userData[0]->name),
						'img'      => $this->users->gravatar($this->userData[0]->email),
        ], 'sidebar');
		}
				
		/**
		 * Account change password.
		 *
		 */
		public function passwordAction()
		{
				$this->di->session();
				$this->users->reqLoggedIn();
				
				$form = new \Mos\HTMLForm\CForm([], [
            'current_pass' => [
                'type'        => 'password',
								'label'       => 'Ange ditt nuvarande lösenord:',
                'required'    => true,
                'validation'  => ['not_empty', 								
								'custom_test' => [
									  'message' => 'Lösenordet du angav stämmer inte med nuvarande lösenord.',
										'test'    => [$this, 'currentPasswordMatch']
										]],
								'class'				=> 'padding5',
            ],
						'new_pass' => [
                'type'        => 'password',
								'label'       => 'Nytt lösenord:',
                'required'    => true,
                'validation'  => ['not_empty',								
								'custom_test' => [
										'message' => 'Måste vara minst 8 symboler långt.',
										'test'    => function($value){
												if(strlen(trim($value)) < 8) return false;
												return true;
										}
								]],
								'class'				=> 'padding5',
            ],
						'repeat_pass' => [
                'type'        => 'password',
								'label'       => 'Upprepa lösenordet:',
                'required'    => true,
                'validation'  => ['not_empty', 'match' => 'new_pass'],
								'class'				=> 'padding5',
            ],
            'submit' => [
                'type'      => 'submit',
								'value'     => 'Spara',
								'class'		  => 'btn',
								'callback'  => function($form) {
                  $form->saveInSession = false;
                  return true;
                }
            ],
        ]);
				
				
				$status = $form->check();
				
				if($status === true) {	
					  $now = gmdate('Y-m-d H:i:s');
						$res = $this->users->save([
								'id'       => $this->userData[0]->id,
								'password' => password_hash($form->Value('new_pass'), PASSWORD_DEFAULT),
								'active'   => $now,
								'updated'  => $now,
						]);
						
						$message = '<output class="success">Du har uppdaterat ditt lösenord</output>';
						$this->session->set('flash_msg', $message);
						$url = $this->url->create('account');
						$this->response->redirect($url);
						
				}
				else if($status === false) {
						$form->AddOutput('Ett eller flera fel uppstod vid valideringen, vänligen kolla att fälten ovan stämmer.');
						$url = $this->url->create('account/password');
						$this->response->redirect($url);
				}
				
				$this->di->theme->setTitle("Ändra lösenord");
        $this->di->views->add('default/page', [
            'title' => "Ändra lösenord",
            'content' => $form->getHTML()
        ], 'main');
				$this->di->views->add('bowl/account/sidebar', [
						'name'     => htmlentities($this->userData[0]->name),
						'img'      => $this->users->gravatar($this->userData[0]->email),
        ], 'sidebar');
		}
		
		/**
		 * Account presentation
		 *
		 */
		public function presentationAction()
		{
				$this->di->session();
				$this->users->reqLoggedIn();
				
				$form = new \Mos\HTMLForm\CForm([], [
            'presentation' => [
                'type'        => 'textarea',
								'label'       => 'Skriv din presentation, markdown är aktiverat.',
                'required'    => true,
								'value'       => $this->userData[0]->presentation,
                'validation'  => ['not_empty',								
								'custom_test' => [
										'message' => 'Din presentation får inte vara tom.',
										'test'    => function($value){
												if(ctype_space($value)) return false;
												return true;
										}
								]],
								'class'				=> 'padding5 textarea',
            ],
            'submit' => [
                'type'      => 'submit',
								'value'     => 'Spara',
								'class'		  => 'btn',
								'callback'  => function($form) {
                  $form->saveInSession = false;
                  return true;
                }
            ],
        ]);
				
				$status = $form->check();
				
				if($status === true) {	
					  $now = gmdate('Y-m-d H:i:s');
						$res = $this->users->save([
								'id'            => $this->userData[0]->id,
								'presentation'  => strip_tags($form->Value('presentation')),
								'active'        => $now,
								'updated'       => $now,
						]);
						
						$message = '<output class="success">Du har uppdaterat din presentation.</output>';
						$this->session->set('flash_msg', $message);
						$url = $this->url->create('account');
						$this->response->redirect($url);
						
				}
				else if($status === false) {
						$form->AddOutput('Ett eller flera fel uppstod vid valideringen, vänligen kolla att fälten ovan stämmer.');
						$url = $this->url->create('account/presentation');
						$this->response->redirect($url);
				}
				
				$this->di->theme->setTitle("Ändra din presentation");
        $this->di->views->add('default/page', [
            'title' => "Presentation",
            'content' => $form->getHTML()
        ], 'main');
				$this->di->views->add('bowl/account/sidebar', [
						'name'     => htmlentities($this->userData[0]->name),
						'img'      => $this->users->gravatar($this->userData[0]->email),
        ], 'sidebar');
		}
		
		/**
		 * Login method, creates session user if login details are correct.
		 *
		 */
		public function loginAction()
		{
				$this->di->session();
				$this->users->reqLoggedOut();
				
				$form = new \Mos\HTMLForm\CForm([], [
            'email' => [
                'type'        => 'text',
								'label'       => 'E-postadress:',
                'required'    => true,
                'validation'  => ['not_empty'],
								'class'				=> 'padding5',
            ],
						'password' => [
                'type'        => 'password',
								'label'       => 'Lösenord:',
                'required'    => true,
                'validation'  => ['not_empty'],
								'class'				=> 'padding5',
            ],
            'login' => [
                'type'      => 'submit',
								'value'     => 'Logga in',
								'class'		  => 'btn',
								'callback'  => function($form) {
                  $form->saveInSession = false;
                  return true;
                }
            ],
        ]);
				
				$form->check();
				
				if(isset($_POST['login'])){
						$user = $this->users->query()
												->where('email = ?')
												->execute([$form->value('email')]);

						if(!empty($user)){
								
								// Check password
								if(password_verify($form->Value('password'), $user[0]->password)){
										// Success!
										$this->session->set('user', $user[0]);
										$url = $this->url->create('account');
										$this->response->redirect($url);
								}
								else{
										// Password did not match.
										$form->AddOutput('Fel användarnamn eller lösenord!');
										$url = $this->url->create('account/login');
										$this->response->redirect($url);
								}
						}
						else{
								$form->AddOutput('Fel användarnamn eller lösenord!');
								$url = $this->url->create('account/login');
								$this->response->redirect($url);
						}
						
				}
				
				$this->di->theme->setTitle("Logga in");
        $this->di->views->add('default/page', [
            'title' => "Logga in",
            'content' => $form->getHTML()
        ], 'main');
				$this->di->views->addString('<h4>Har du inget konto?</h4><p><a class="btn" href="' . $this->di->url->create('account/register') . '">Skapa konto nu</a></p>', 'sidebar');
				
		}
		
		/**
		 * Create account
		 *
		 */
		public function registerAction()
		{
				$this->di->session();
				$this->users->reqLoggedOut();
				
				$form = new \Mos\HTMLForm\CForm([], [
						'acronym' => [
								'type'        => 'text',
								'label'       => 'Användarnamn:',
								'required'    => true,
								'validation'  => ['not_empty', 'alpha_az', 'range_3_20',
								'custom_test' => [
									  'message' => 'Användarnamnet är redan upptaget.',
										'test'    => [$this, 'duplicateAcronym']
								]],
								'class'       => 'padding5',
						],
						'name'    => [
								'type'        => 'text',
								'label'       => 'Namn:',
								'required'    => true,
								'validation'  => ['not_empty', 'range_3_30', 'alpha_spaces', 'ctype_spaces'],
								'class'       => 'padding5',
								
						],
            'email' => [
                'type'        => 'text',
								'label'       => 'E-postadress:',
                'required'    => true,
                'validation'  => ['not_empty', 'email_adress',
								'custom_test' => [
									  'message' => 'E-postadressen är redan kopplad till ett konto.',
										'test'    => [$this, 'duplicateEmail']
										]
								],
								'class'				=> 'padding5',
            ],
						'password' => [
                'type'        => 'password',
								'label'       => 'Lösenord:',
                'required'    => true,
                'validation'  => ['not_empty', 								
								'custom_test' => [
										'message' => 'Måste vara minst 8 symboler långt.',
										'test'    => function($value){
												if(strlen(trim($value)) < 8) return false;
												return true;
										}
								]],
								'class'				=> 'padding5',
            ],
            'login' => [
                'type'      => 'submit',
								'value'     => 'Skapa konto',
								'class'		  => 'btn',
								'callback'  => function($form) {
                  $form->saveInSession = false;
                  return true;
                }
            ],
        ]);
				
				$status = $form->check();
				
				if($status === true) {	
						$now = gmdate('Y-m-d H:i:s');
						$res = $this->users->create([
								'acronym'      => $form->Value('acronym'),
								'email'        => str_replace(' ', '', $form->Value('email')),
								'name'         => preg_replace('/\s{2,}/u', ' ', $form->Value('name')),
								'password'     => password_hash($form->Value('password'), PASSWORD_DEFAULT),
								'presentation' => 'Denna användare har inte skrivit något i sin presentation ännu.',
								'created'      => $now,
								'active'       => $now,
						]);
						
						if($res === true){
								$message = '<output class="success">Ditt konto har skapats och du kan nu logga in!</output>';
								$this->session->set('flash_msg', $message);
								$url = $this->url->create('account/login');
								$this->response->redirect($url);
						}
						
				}
				else if($status === false) {
						$form->AddOutput('Ett eller flera fel uppstod vid valideringen, vänligen kolla att fälten ovan stämmer.');
						$url = $this->url->create('account/register');
						$this->response->redirect($url);
				}
				
				$this->di->theme->setTitle("Skapa konto");
        $this->di->views->add('default/page', [
            'title' => "Skapa konto",
            'content' => $form->getHTML()
        ], 'main');
				$this->di->views->addString('<h4>Redan medlem?</h4><p><a class="btn" href="' . $this->di->url->create('account/login') . '">Logga in här</a></p>', 'sidebar');
		}
		
		/**
		 * Check if email is already used.
		 *
		 */
		public function duplicateEmail($email)
		{
				// For updating profile purpose
				if(isset($this->user)){
						if($email == $this->userData[0]->email){
								return true;
						}
						else{
								$user = $this->users->query('email')
														 ->where('email = ?')
														 ->execute([$email]);
														 
								return empty($user);
						}
				}
				else{
						$user = $this->users->query('email')
												 ->where('email = ?')
												 ->execute([$email]);
												 
						return empty($user);
				}
		}
		
		/**
		 * Check if acronym is already used.
		 *
		 */
		public function duplicateAcronym($acronym)
		{
				
				$user = $this->users->query('acronym')
										 ->where('acronym = ?')
										 ->execute([$acronym]);
										 
				return empty($user);
		}
		
		/**
		 * Check if current password matches with database user password.
		 *
		 */
		public function currentPasswordMatch($current_pass)
		{
				if(password_verify($current_pass, $this->userData[0]->password)){
						return true;
				}
				else{
						return false;
				}
		}
		
		/**
		 * Logout method, destroys session user.
		 *
		 */
		public function logoutAction()
		{
				$this->users->reqLoggedIn();
				
				$this->session->destroy('user');
				$message = '<output class="success">Du har loggat ut, hoppas vi ses snart igen!</output>';
				$this->session->set('flash_msg', $message);
				$url = $this->url->create('account/login');
				$this->response->redirect($url);
		}
		
}