<?php

namespace Anax\Ask;

class QuestionsController implements \Anax\DI\IInjectionAware
{
		use \Anax\DI\TInjectable;
		
		
		public function initialize()
		{
				$this->vquestions = new \Anax\Ask\Vquestions();
				$this->vquestions->setDI($this->di);
				
				$this->questions = new \Anax\Ask\Questions();
				$this->questions->setDI($this->di);
				
				$this->question_tags = new \Anax\Ask\Question_tags();
				$this->question_tags->setDI($this->di);
				
				$this->answers = new \Anax\Ask\Answers();
				$this->answers->setDI($this->di);
				
				$this->user = new \Anax\Users\User();
				$this->user->setDI($this->di);
				
				$this->tags = new \Anax\Tags\Tags();
				$this->tags->setDI($this->di);
				
				$this->sub_answers = new \Anax\Ask\Sub_answers();
				$this->sub_answers->setDI($this->di);
		}
		
		/**
		 * View all questions
		 *
		 */
		public function showAllAction($page = 1)
		{
				if(!preg_match('/^[0-9]+$/', $page)){
						$page = 1;
				}
				$limit = 10;
				$offset = (($page - 1) * $limit);
				
				$check = $this->vquestions->query('id')
														 ->execute();
				$max = ceil(count($check) / $limit);
				
				$questions = $this->vquestions->query('vquestions.*, count(answers.id) AS answers')
													->leftJoin('answers', 'vquestions.id = answers.q_id')
													->orderBy('created DESC')
													->limit($limit)
													->offset($offset)
													->groupBy('vquestions.id')
													->execute();
				
				if($this->user->loginStatus()){
						$content = '<p><a class="btn" href="' . $this->url->create('questions/create') . '"><i class="fa fa-pencil-square-o"></i> Skapa ny fråga</a></p>';
				}
				else{
						$content = null;
				}
													
				if(!empty($questions)){
						$this->di->theme->setTitle('Alla frågor');
						$this->di->views->add('bowl/questions/listquestions', [
								'content'     => $content,
								'title'       => 'Alla frågor', 
								'questions'   => $questions,
								'pagination'  => $this->getPageNavigation('questions/show-all', $page, $max)
						], 'main-large');
						
				}
				else{
						$this->di->theme->setTitle('Alla frågor');
						$this->di->views->add('bowl/questions/listquestions', [
								'title'        => 'Alla frågor',
								'content'      => '<p>Det finns inga frågor att visa.<br/>' . $content . '</p>',
						], 'main-large');
				}									
				
				
		}
		
		/** 
		 * View single question with comments.
		 *
		 */
		public function showAction($id)
		{
				if(!preg_match('/^[0-9]+$/', $id)){
						$url = $this->url->create('questions/show-all');
						$this->response->redirect($url);
				}
				
				$question = $this->vquestions->query()
																		->Where('id = ?')
																		->execute([$id]);
																		
				if(empty($question)){
						$url = $this->url->create('questions/show-all');
						$this->response->redirect($url);
				}				
																		
				$answers = $this->answers->query()
																 ->Where('q_id = ?')
																 ->execute([$id]);
				
				if($this->user->loginStatus()){
						$actionbtn = '| <i class="fa fa-pencil-square"></i> <a href="' . $this->url->create('questions/answer/' . $id) . '">Svara på frågan</a>';
				}
				else{
						$actionbtn = '| <i class="fa fa-pencil-square"></i> <a href="' . $this->url->create('account/login') . '">Logga in för att svara</a>';
				}
				
				
				
				$this->di->theme->setTitle('Fråga');
						$this->di->views->add('bowl/questions/listquestion', [
								'content'    => null,
								'question'   => $question,
								'count'      => count($answers),
								'actionbtn'  => $actionbtn,
								'answers'    => $this->buildComments($answers),
						], 'main-large');
				
		}
		
		/**
		 * Build comments with subcomments.
		 *
	   */
		private function buildComments($answers)
		{
				$html = null;
				foreach($answers AS $answer)
				{
						$sub_comment = null;
						
						if($this->user->loginStatus()){
								$actionbtn = ' <a href="' . $this->url->create('questions/comment/' . $answer->id) . '">Kommentera svaret</a>';
						}
						else{
								$actionbtn = ' <a href="' . $this->url->create('account/login') . '">Logga in för att kommentera</a>';
						}
						$answer_html = '<article class="answer">
															 <div class="answer-text">
																	' . $this->textFilter->doFilter($answer->text, 'markdown') . '
															 </div>
															 <footer class="answer-footer"><i class="fa fa-user"></i> <a href="' . $this->url->create('users/profile/' . $answer->creator) . '">' . ucfirst($answer->creator) . '</a> | <i class="fa fa-clock-o"></i> ' . date('d M Y H:i', $answer->created) . '<span style="float:right;"><i class="fa fa-pencil-square"></i>' . $actionbtn . '</span></footer>
														</article>';
				
				
						$sub_answers = $this->sub_answers->query()
																						 ->Where('a_id = ?')
																						 ->orderBy('created ASC')
																						 ->execute([$answer->id]);
						
						if(!empty($sub_answers)) {
						
								foreach($sub_answers AS $sub) {
										$sub_comment .= '<article class="sub-answer">
																				<div class="answer-text">
																						' . $this->textFilter->doFilter($sub->text, 'markdown') . '
																				</div>
																				<footer class="sub-answer-footer"><i class="fa fa-user"></i> <a href="' . $this->url->create('users/profile/' . $sub->creator) . '">' . ucfirst($sub->creator) . '</a> | <i class="fa fa-clock-o"></i> ' . date('d M Y H:i', $sub->created) . '</footer>
																		 </article>';
								}
								
						}
						
						$html .= $answer_html . $sub_comment;
						
																		
				}
				
				return $html;
				
		}
		
		/**
		 * Answer on question
		 *
		 */
		public function answerAction($id)
		{
				$this->user->reqLoggedIn();
				$user = $this->di->session->get('user');
				
				if(!preg_match('/^[0-9]+$/', $id)){
						$url = $this->url->create('questions/show-all');
						$this->response->redirect($url);
				}
				
				$check = $this->vquestions->query()
																 ->Where('id = ?')
																 ->execute([$id]);
																 
				if(empty($check)){
						$url = $this->url->create('questions/show-all');
						$this->response->redirect($url);
				}				
				
				$form = new \Mos\HTMLForm\CForm([], [
						'text' => [
                'type'        => 'textarea',
								'label'       => 'Ditt svar (markdown aktiverat):',
                'required'    => true,
                'validation'  => ['not_empty', 								
								'custom_test' => [
										'message' => 'Ditt svar får inte vara tomt.',
										'test'    => function($value){
												if(ctype_space($value)) return false;
												return true;
										}
								]],
								'class'				=> 'padding5 textarea2',
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
				
						$res = $this->answers->save([
								'q_id'      => $id,
								'creator'   => $user->acronym,
								'text'      => strip_tags($form->Value('text')),
								'created'   => time(),
						]);
						
						// Update active
						$this->user->save([
								'id'     => $user->id,
								'active' => $now,
						]);
						
						
						$message = '<output class="success">Du har svarat på frågan.</output>';
						$this->session->set('flash_msg', $message);
						$url = $this->url->create('questions/show/' . $id);
						$this->response->redirect($url);
						
				}
				else if($status === false) {
						$form->AddOutput('Ett eller flera fel uppstod vid valideringen, vänligen kolla att fälten ovan stämmer.');
						$url = $this->url->create('questions/answer/' . $id);
						$this->response->redirect($url);
				}
				
				$this->di->theme->setTitle("Svara på fråga");
        $this->di->views->add('bowl/answers/answer', [
            'title' => "Svara på frågan",
						'question' => $check,
            'content' => $form->getHTML()
        ], 'main-large');
				
		}
		
		/**
		 * Comment on a question answer.
		 *
		 */
		public function commentAction($id)
		{
				$this->user->reqLoggedIn();
				$user = $this->di->session->get('user');
				
				if(!preg_match('/^[0-9]+$/', $id)){
						$url = $this->url->create('questions/show-all');
						$this->response->redirect($url);
				}
				
				$check = $this->answers->query()
																 ->Where('id = ?')
																 ->execute([$id]);
																 
				if(empty($check)){
						$url = $this->url->create('questions/show-all');
						$this->response->redirect($url);
				}				
				
				$form = new \Mos\HTMLForm\CForm([], [
						'text' => [
                'type'        => 'textarea',
								'label'       => 'Ditt svar (markdown aktiverat):',
                'required'    => true,
                'validation'  => ['not_empty', 								
								'custom_test' => [
										'message' => 'Ditt svar får inte vara tomt.',
										'test'    => function($value){
												if(ctype_space($value)) return false;
												return true;
										}
								]],
								'class'				=> 'padding5 textarea2',
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
				
						$res = $this->sub_answers->save([
								'a_id'      => $id,
								'creator'   => $user->acronym,
								'text'      => strip_tags($form->Value('text')),
								'created'   => time(),
						]);
						
						// Update active
						$this->user->save([
								'id'     => $user->id,
								'active' => $now,
						]);
						
						$message = '<output class="success">Du har kommenterat svaret på frågan.</output>';
						$this->session->set('flash_msg', $message);
						$url = $this->url->create('questions/show/' . $check[0]->q_id);
						$this->response->redirect($url);
						
				}
				else if($status === false) {
						$form->AddOutput('Ett eller flera fel uppstod vid valideringen, vänligen kolla att fälten ovan stämmer.');
						$url = $this->url->create('questions/comment/' . $id);
						$this->response->redirect($url);
				}
				
				$this->di->theme->setTitle("Kommentera svaret på frågan");
        $this->di->views->add('bowl/answers/comment', [
            'title' => "Kommentera svaret på frågan",
						'answer' => $check,
            'content' => $form->getHTML()
        ], 'main-large');
				
		}
		
		/**
		 * Create question.
		 *
		 */
		public function createAction()
		{
				$this->user->reqLoggedIn();
				$user = $this->di->session->get('user');
				
				$result = $this->tags->query('id, name')
													   ->orderBy('name ASC')
														 ->execute();
				
				$tags = [];
				foreach($result AS $tag){
						$tags[$tag->id] = $tag->name;
				}				
				
				
				$form = new \Mos\HTMLForm\CForm([], [
						'title' => [
                'type'        => 'text',
								'label'       => 'Titel på frågan:',
                'required'    => true,
                'validation'  => ['not_empty', 'range_10_100', 'ctype_spaces'],
								'class'				=> 'padding5 text-wide',
            ],
				  	'text' => [
                'type'        => 'textarea',
								'label'       => 'Text (markdown aktiverat):',
                'required'    => true,
                'validation'  => ['not_empty'],
								'class'				=> 'padding5 textarea',
            ],
						'tags' => [
								'type'        => 'select-multiple',
								'label'       => 'Taggar (välj en eller fler):',
								'options'     => $tags,
								'required'    => true,
								'class'				=> 'padding5 select',
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
			
						$res = $this->questions->save([
								'creator'   => $user->acronym,
								'title'     => $form->Value('title'),
								'text'      => strip_tags($form->Value('text')),
								'created'   => time(),
						]);
						
						foreach($_POST['tags'] AS $test => $value){
								$this->question_tags->db->insert('question_tags', ['tags_id', 'q_id']);
								$this->question_tags->db->execute([$value, $this->questions->id]);
						}
											
						// Update active
						$this->user->save([
								'id'     => $user->id,
								'active' => $now,
						]);
						
						$message = '<output class="success">Du har skapat en fråga.</output>';
						$this->session->set('flash_msg', $message);
						$url = $this->url->create('questions/show/' . $this->questions->id);
						$this->response->redirect($url);
						
				}
				else if($status === false) {
						$form->AddOutput('Ett eller flera fel uppstod vid valideringen, vänligen kolla att fälten ovan stämmer.');
						$url = $this->url->create('questions/create/');
						$this->response->redirect($url);
				}
				
				
				$this->di->theme->setTitle("Skapa fråga");
        $this->di->views->add('bowl/page', [
            'title' => "Skapa fråga",
            'content' => $form->getHTML()
        ], 'main');
				
				$content = '<p>Beskriva tydligt vad frågan/problemet handlar om för att läsaren lättast ska förstå din fråga.</p><hr/><p>Du måste välja minst en tagg som din fråga kan kopplas till, om du inte tycker att den passar in någonstans så väljer du "allmänt".</p>';
				
			  $this->di->views->add('bowl/sidebar', [
            'title' => "<h2>Tänk på att</h2>",
            'content' => $content,
        ], 'sidebar');
		}
		
		
		/**
		 * Paginering navbar
		 *
		 */
		public function getPageNavigation($link, $page, $max, $min=1) 
		{
		
				// Första
				$nav  = "<div style='float:left;'><a href='" . $this->url->create($link) . '/' . $min . "' title='Första'><i class='fa fa-angle-double-left fa-2x' style='vertical-align: middle;'></i></a> ";
				// Föregående
				$nav .= "<a href='" . $this->url->create($link) . '/' . ($page > $min ? $page - 1 : $min) . "' title='Föregående'><i class='fa fa-angle-left fa-2x' style='vertical-align: middle;'></i></a></div>";
			 
				/* Sidor numrerade
				for($i=$min; $i<=$max; $i++) {
						if($page == $i){
							  $nav .= " $i ";
						}
						else{
								$nav .= "<a href='" . $this->url->create('tags/show-all') . '/' . $i . "'>$i</a> ";
						}
				}*/
				
				$nav .= "Sida " . $page . " av " . $max;
			 
				// Nästa
				$nav .= "<div style='float:right;'><a href='" . $this->url->create($link) . '/' . ($page < $max ? $page + 1 : $max) . "' title='Nästa'><i class='fa fa-angle-right fa-2x' style='vertical-align: middle;'></i></a> ";
				// Sista
				$nav .= "<a href='" . $this->url->create($link) . '/' . $max . "' title='Sista'><i class='fa fa-angle-double-right fa-2x' style='vertical-align: middle;'></i></a></div>";
				return $nav;
				
		}
		
		
}