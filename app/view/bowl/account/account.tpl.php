<h1><?=$user->acronym?></h1>
<?=$this->textFilter->doFilter($user->presentation, 'markdown')?><hr/>
<div class="user-question-list">
<h3>Ställda frågor</h3>
<?php if(!empty($questions)): ?>
<ul>
<?php foreach($questions AS $question): ?> 
<li><i class="fa fa-angle-double-right"></i> <a href="<?=$this->url->create('questions/show/' . $question->id)?>"><?=htmlentities(ucfirst($question->title))?></a>&nbsp;&nbsp;<i class="fa fa-comments"></i> <?=$question->answers?></li>
<?php endforeach;?>
</ul>
<?php else: ?> 
Du har inte ställt någon fråga ännu.
<?php endif; ?>
<h3>Besvarade frågor</h3>
<?php if(!empty($answers)): ?>
<ul>
<?php foreach($answers AS $answer): ?> 
<li><i class="fa fa-angle-double-right"></i> <a href="<?=$this->url->create('questions/show/' . $answer->q_id)?>"><?=htmlentities(ucfirst($answer->q_title))?></a></li>
<?php endforeach;?>
</ul>
<?php else: ?> 
Du har inte besvarat någon fråga ännu.
<?php endif; ?>
</div>

