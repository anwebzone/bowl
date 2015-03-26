<h1>Användare</h1>
<div class="users-list">
<?php foreach($users AS $user): ?>
<figure>
	<a href="<?=$this->url->create('users/profile/' . strtolower(urlencode($user->acronym)))?>" title="<?=$user->acronym?>"><img src="<?=\Anax\Gravatar\Gravatar::get_gravatar($user->email, 150);?>" alt="<?=$user->acronym?> profilbild"/></a>
	<figcaption><a href="<?=$this->url->create('users/profile/' . strtolower(urlencode($user->acronym)))?>" title="<?=$user->acronym?>"><?=ucfirst($user->acronym)?></a></figcaption>
</figure>
<?php endforeach; ?>
<div class="pagination"><?=$pagination?></div>
</div>

