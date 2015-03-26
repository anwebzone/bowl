<h4>Populära taggar</h4>
<?php if(!empty($tags)): foreach($tags AS $tag): ?>
<a class="tagbtn" href="<?=$this->url->create('tags/showtag/' . $tag->name)?>"><i class="fa fa-tags"></i> <?=$tag->name?> (<?=$tag->count?>)</a>
<?php endforeach; else: ?>
<p>Det finns inga taggar att visa.</p>
<?php endif; ?>
<hr>
<div class="users-list">
<h4>Mest aktiva användare</h4>
<?php if(!empty($users)): foreach($users AS $user): ?>
<figure class="user">
<a href="<?=$this->url->create('users/profile/' . $user->acronym)?>"><img src="<?=\Anax\Gravatar\Gravatar::get_gravatar($user->email, 150);?>" alt=""></a>
<figcaption><a href="<?=$this->url->create('users/profile/' . $user->acronym)?>"><?=ucfirst($user->acronym)?></a></figcaption>
</figure>
<?php endforeach; else: ?>
<p>Det finns inga användare att visa</p>
<?php endif; ?>
</div>


