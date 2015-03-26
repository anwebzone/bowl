<?php
namespace Anax\Tags;
/*
 * A controller for users & admin events
 *
 */
class TagsController implements \Anax\DI\IInjectionAware
{
		use \Anax\DI\TInjectable;
		
		
		/**
		 * Init 
		 */
		public function initialize()
		{
				$this->tags = new \Anax\Tags\Tags();
				$this->tags->setDI($this->di);
				
				$this->questions = new \Anax\Ask\Vquestions();
				$this->questions->setDI($this->di);
		}
		
		
		/**
		 * Display all tags
		 *
		 */
		public function showAllAction($page = 1)
		{
				if(!preg_match('/^[0-9]+$/', $page)){
						$page = 1;
				}
				$limit = 20;
				$offset = (($page - 1) * $limit);
				
				$check = $this->tags->query('id')
														 ->execute();
				$max = ceil(count($check) / $limit);
				
				$tags = $this->tags->query()
													 ->limit($limit)
													 ->offset($offset)
													 ->execute();
				
				$this->di->theme->setTitle('Tags');
				$this->di->views->add('bowl/tags/list', [
						'tags'       => $tags,
						'pagination' => $this->getPageNavigation('tags/show-all', $page, $max),
				], 'main-large');
				
		}
		
		/**
		 * Show questions in category tag.
		 *
		 */
		public function showTagAction($tag = null, $page = 1)
		{
				// Redirect if tag doesn't exist.
				$tagexists = $this->tags->query()
				                        ->Where('name = ?')
																->execute([urldecode($tag)]);
				if(empty($tagexists)){
						$url = $this->url->create('tags/show-all');
						$this->response->redirect($url);
				}
				
				// Set current page number
				if(!preg_match('/^[0-9]+$/', $page)){
						$page = 1;
				}
				
				$limit = 10;
				$offset = (($page - 1) * $limit);
				$param = '[[:<:]]' . urldecode($tag) . '[[:>:]]';
				$check = $this->questions->query('id')
																		 ->Where('tag_name REGEXP ?')
																		 ->execute([$param]);
				$max = ceil(count($check) / $limit);
				
				$questions = $this->questions->query('vquestions.*, count(answers.id) AS answers')
													->leftJoin('answers', 'vquestions.id = answers.q_id')
													->Where('tag_name REGEXP ?')
													->orderBy('created DESC')
													->limit($limit)
													->offset($offset)
													->groupBy('vquestions.id')
													->execute([$param]);

				
				
				if(!empty($questions)){
						$this->di->theme->setTitle('#' . htmlentities(urldecode(ucfirst($tag))));
						$this->di->views->add('bowl/tags/listquestions', [
								'title'       => '#' . htmlentities(urldecode($tag)),
								'content'     => null,
								'questions'   => $questions,
								'pagination'  => $this->getPageNavigation('tags/showtag/' . $tag, $page, $max)
						], 'main-large');
						
				}
				else{
						$this->di->theme->setTitle('#' . htmlentities(urldecode(ucfirst($tag))));
						$this->di->views->add('bowl/tags/listquestions', [
								'title'        => '#' . htmlentities(urldecode($tag)),
								'content'      => 'Det finns inga frågor kopplade till denna tag.',
						], 'main-large');
				}
				
				
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