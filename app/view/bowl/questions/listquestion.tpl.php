<h1><i class="fa fa-question-circle"></i> Fråga ställd av: <a href="<?=$this->url->create('users/profile/' . $question[0]->creator)?>"><?=$question[0]->creator?></a></h1>
<?=$content?>
<article class="questions-list">
	<div class="date">
		<div class="day">
			<?=date('d', $question[0]->created)?>
		</div>
		<div class="month">
			<?=date('M', $question[0]->created)?>
		</div>
		<div class="year">
			<?=date('Y', $question[0]->created)?>
		</div>
	</div>
	<header class="the-title">
		<h2><?=htmlentities($question[0]->title)?></h2>
	</header>
	<div class="the-post">
	<?=$this->textFilter->doFilter($question[0]->text, 'markdown');?>
	</div>
	<footer class="the-footer">
		<?php
				$tags = null;
				$get_tags = explode(',', $question[0]->tag_name);
				foreach($get_tags AS $tag){
						$tags .= '<a href="' . $this->url->create('tags/showtag/' . $tag) . '">#' . $tag . '</a> ';
				}
		?>
		<i class="fa fa-user"></i> <a href="<?=$this->url->create('users/profile/' . $question[0]->creator)?>"><?=ucfirst($question[0]->creator)?></a> | <i class="fa fa-tags"></i> <?=$tags?> | <i class="fa fa-clock-o"></i> <?=date('d M Y H:i', $question[0]->created)?>
	</footer>
</article>
<div class="answers-list">
<h3><i class="fa fa-comments"></i> <?=$count?> Svar <?=$actionbtn?></h3>
<?=$answers?>
</div>


