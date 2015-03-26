<?php

namespace Anax\Home;

class HomeController implements \Anax\DI\IInjectionAware
{
		use \Anax\DI\TInjectable;
		
		
		public function initialize()
		{
				$this->questions = new \Anax\Ask\Vquestions();
				$this->questions->setDI($this->di);
				
				$this->tags = new \Anax\Tags\Tags();
				$this->tags->setDI($this->di);
				
				$this->user = new \Anax\Users\User();
				$this->user->setDI($this->di);
				
				$this->sub_answers = new \Anax\Ask\Sub_answers();
				$this->sub_answers->setDI($this->di);
		}
		/**
		 * Create frontpage "home".
		 *
		 */
		public function indexAction()
		{
				$questions = $this->questions->query('vquestions.*, count(answers.id) AS answers')
													->leftJoin('answers', 'vquestions.id = answers.q_id')
													->orderBy('created DESC')
													->limit('5')
													->groupBy('vquestions.id')
													->execute();
																		 
				$users = $this->user->query()
														->limit('3')
														->orderBy('active DESC')
														->execute();
				
				$tags = $this->tags->query('tags.name, count(question_tags.id) AS count')
													 ->join('question_tags', 'tags.id = question_tags.tags_id')
													 ->groupBy('tags.name')
													 ->orderBy('count DESC')
													 ->limit('5')
													 ->execute();
													 
				$this->di->theme->setTitle('Hem');
				$this->di->views->add('bowl/page', [
						'content'     => '<p>Bowl är ett frågeforum för bowlingintresserade. Syftet är öka bowlingintresset samt sprida kunskap om sporten.
															Vem som helst kan ställa frågor och besvara andras frågor, allt man behöver är ett konto.</p>',
						'title'       => 'Välkommen till bowl!', 
				], 'main-large');		

				$this->di->views->add('bowl/homequestions', [
						'title'      => 'Senaste fem frågorna',
						'questions'  => $questions,
				], 'main');		

				$this->di->views->add('bowl/homesidebar', [
						'title'      => 'Populära taggar',
						'tags'  => $tags,
						'users' => $users,
				], 'sidebar');		
				
		}
		

		
		
}