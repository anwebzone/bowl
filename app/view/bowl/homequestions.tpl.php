<h2><?=$title?></h2>
<?php if(!empty($questions)): foreach($questions AS $question): ?>
<article class="questions-list">
	<div class="date">
		<div class="day">
			<?=date('d', $question->created)?>
		</div>
		<div class="month">
			<?=date('M', $question->created)?>
		</div>
		<div class="year">
			<?=date('Y', $question->created)?>
		</div>
	</div>
	<header class="the-title-home">
		<h3><a href="<?=$this->url->create('questions/show/' . $question->id)?>"><?=htmlentities($question->title)?></a></h3>
	</header>
	<div class="the-post-home">
			<?php
				$shorten = $this->textFilter->shorten($question->text, 300, $this->url->create('questions/show/' . $question->id));
				echo $shorten;
			?>
	</div>
	<footer class="the-footer-home">
		<?php
				$tags = null;
				$get_tags = explode(',', $question->tag_name);
				foreach($get_tags AS $tag){
						$tags .= '<a href="' . $this->url->create('tags/showtag/' . $tag) . '">#' . $tag . '</a> ';
				}
		?>
		<i class="fa fa-user"></i> <a href="<?=$this->url->create('users/profile/' . $question->creator)?>"><?=ucfirst($question->creator)?></a> | <i class="fa fa-tags"></i> <?=$tags?> | <i class="fa fa-clock-o"></i> <?=date('d M Y H:i', $question->created)?> <span style="float:right;"><i class="fa fa-comments"></i> <?=$question->answers?> | <a href="<?=$this->url->create('questions/show/' . $question->id)?>">Visa</a></span>
	</footer>
</article>
<?php endforeach; else: ?>
<p>Det finns inga frågor ännu.
<?php endif; ?>


