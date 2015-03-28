<h1><?=$title?></h1>
<article class="questions-list">
	<div class="the-post">
	<?=$this->textFilter->doFilter($answer[0]->text, 'markdown');?>
	</div>
	<footer class="the-footer">
		<i class="fa fa-user"></i> <a href="<?=$this->url->create('users/profile/' . $answer[0]->creator)?>"><?=ucfirst($answer[0]->creator)?></a> | <i class="fa fa-clock-o"></i> <?=date('d M Y H:i', $answer[0]->created)?>
	</footer>
</article>
<?=$content?>
