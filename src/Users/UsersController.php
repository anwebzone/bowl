<?php
namespace Anax\Users;
/*
 * A controller for users & admin events
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
		use \Anax\DI\TInjectable;
		
		
		/**
		 * Init 
		 */
		public function initialize()
		{
				$this->users = new \Anax\Users\User();
				$this->users->setDI($this->di);
				
				$this->questions = new \Anax\Ask\Vquestions();
				$this->questions->setDI($this->di);
				
				$this->answers = new \Anax\Ask\Answers();
				$this->answers->setDI($this->di);
		}
		
		
		/**
		 * List all users
		 *
		 */
		public function showAllAction($page = 1)
		{
				if(!preg_match('/^[0-9]+$/', $page)){
						$page = 1;
				}
				$limit = 10;
				$offset = (($page - 1) * $limit);
				
				$check = $this->users->query('id')
														 ->execute();
				$count = count($check);
				$max = ceil($count / $limit);
				
				$users = $this->users->query()
														 ->limit($limit)
														 ->offset($offset)
														 ->execute();
														 
				
				$this->di->theme->setTitle('Användare');
				$this->di->views->add('bowl/users/list', [
            'users'      => $users,
						'pagination' => $this->getPageNavigation($page, $max),
						'page'       => 'Sida ' . $page . ' av ' . $max,
        ], 'main-large');
			
		}
		

		/**
		 * Paginering navbar
		 *
		 */
		public function getPageNavigation($page, $max, $min=1) 
		{
		
				// Första
				$nav  = "<div style='float:left;'><a href='" . $this->url->create('users/show-all') . '/' . $min . "' title='Första'><i class='fa fa-angle-double-left fa-2x' style='vertical-align: middle;'></i></a> ";
				// Föregående
				$nav .= "<a href='" . $this->url->create('users/show-all') . '/' . ($page > $min ? $page - 1 : $min) . "' title='Föregående'><i class='fa fa-angle-left fa-2x' style='vertical-align: middle;'></i></a></div>";
			 
				/* Sidor numrerade
				for($i=$min; $i<=$max; $i++) {
						if($page == $i){
							  $nav .= " $i ";
						}
						else{
								$nav .= "<a href='" . $this->url->create('users/show-all') . '/' . $i . "'>$i</a> ";
						}
				}*/
				
				$nav .= "Sida " . $page . " av " . $max;
			 
				// Nästa
				$nav .= "<div style='float:right;'><a href='" . $this->url->create('users/show-all') . '/' . ($page < $max ? $page + 1 : $max) . "' title='Nästa'><i class='fa fa-angle-right fa-2x' style='vertical-align: middle;'></i></a> ";
				// Sista
				$nav .= "<a href='" . $this->url->create('users/show-all') . '/' . $max . "' title='Sista'><i class='fa fa-angle-double-right fa-2x' style='vertical-align: middle;'></i></a></div>";
				return $nav;
				
		}
		
		/**
		 * View one user.
		 *
		 */
		public function profileAction($acronym)
		{
				if(!preg_match('/^[a-zA-Z]+$/', $acronym)) {
						$url = $this->url->create('users/show-all');
						$this->response->redirect($url);
				}
				
				$user = $this->users->query()
										->Where('acronym = ?')
										->execute([$acronym]);
										
				if(empty($user)) {
						$url = $this->url->create('users/show-all');
						$this->response->redirect($url);
				}
				
				// Display questions made by user
			  $questions = $this->questions->query('vquestions.*, count(answers.id) AS answers')
												  ->leftJoin('answers', 'vquestions.id = answers.q_id')
												  ->Where('vquestions.creator = ?')
													->orderBy('vquestions.created DESC')
												  ->groupBy('vquestions.id')
													->limit('10')
												  ->execute([$acronym]);
				
				// Display answers made by user
				$answers = $this->answers->query('answers.*, vquestions.title AS q_title')
																 ->leftJoin('vquestions', 'answers.q_id = vquestions.id')
																 ->Where('answers.creator = ?')
																 ->groupBy('q_title')
																 ->limit('10')
																 ->execute([$acronym]);
																 
													
				
				$this->di->theme->setTitle(ucfirst($acronym));
				$this->di->views->add('bowl/users/profile', [
            'user'       => $user[0],
						'questions'  => $questions,
						'answers'    => $answers,
        ], 'main');
				$this->di->views->add('bowl/users/sidebar', [
						'name'     => $user[0]->name,
						'img'      => $this->users->gravatar($user[0]->email),
        ], 'sidebar');
				
				
		}
		
		
}