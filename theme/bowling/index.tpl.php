<!doctype html>
<html class='no-js' lang='<?=$lang?>'>
<head>
<meta charset='utf-8'/>
<title><?=$title . $title_append?></title>
<?php if(isset($favicon)): ?><link rel='icon' href='<?=$this->url->asset($favicon)?>'/><?php endif; ?>
<?php foreach($stylesheets as $stylesheet): ?>
<link rel='stylesheet' type='text/css' href='<?=$this->url->asset($stylesheet)?>'/>
<?php endforeach; ?>
<?php if(isset($style)): ?><style><?=$style?></style><?php endif; ?>
<script src='<?=$this->url->asset($modernizr)?>'></script>
</head>

<body>

<div id='wrapper'>

<div id='header'>
<?php if(isset($header)) echo $header?>
<?php $this->views->render('header')?>
<?php if ($this->views->hasContent('navbar')) : ?>
<?php $this->views->render('navbar')?>
<?php endif; ?>
</div>

<?php if ($this->views->hasContent('flash')) : ?>
<div id='flash'>
<?php $this->views->render('flash')?>
</div>
<?php endif; ?>

<?php if ($this->views->hasContent('main-large')) : ?>
<div id='main-large'>
<?php $this->views->render('main-large')?>
</div>
<?php endif; ?>

<?php if ($this->views->hasContent('main')) : ?>
<div id='main'>
<?php $this->views->render('main')?>
</div>
<?php endif; ?>

<?php if ($this->views->hasContent('sidebar')) : ?>
<div id='sidebar'>
<?php $this->views->render('sidebar')?>
</div>
<?php endif; ?>

<footer id='footer'>
<?php if(isset($footer)) echo $footer?>
<?php $this->views->render('footer')?>
</footer>

</div>

<?php if(isset($jquery)):?><script src='<?=$this->url->asset($jquery)?>'></script><?php endif; ?>

<?php if(isset($javascript_include)): foreach($javascript_include as $val): ?>
<script src='<?=$this->url->asset($val)?>'></script>
<?php endforeach; endif; ?>

<?php if(isset($google_analytics)): ?>
<script>
  var _gaq=[['_setAccount','<?=$google_analytics?>'],['_trackPageview']];
  (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
  g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
  s.parentNode.insertBefore(g,s)}(document,'script'));
</script>
<?php endif; ?>

</body>
</html>
