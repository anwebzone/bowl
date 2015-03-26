<figure class="user">
	<a href="<?=$this->url->create('account/profile')?>"><img src="<?=$img?>" alt="<?=$name?> bild"/></a>
	<figcaption><?=$name?></figcaption>
</figure>
<ul class="sidebar-menu">
	<li><i class="fa fa-cog"></i> <a href="<?=$this->url->create('account/settings')?>">Kontoinställningar</a></li>
	<li><i class="fa fa-pencil-square-o"></i> <a href="<?=$this->url->create('account/presentation')?>">Ändra presentation</a></li>
	<li><i class="fa fa-key"></i> <a href="<?=$this->url->create('account/password')?>">Ändra lösenord</a></li>
</ul>