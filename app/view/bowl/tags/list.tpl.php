<h1>Tags</h1>
<?php if(!empty($tags)): ?>
<div class="tags-list">
<?php 
	$html = null;
	$count = ceil(count($tags) / 5);
	
	for($i = 0; $i < $count; $i++){
	
			$offset = $i * 5;
			
			$html .= '<div class="row">'; 
			$y = $offset;
			$max = $y + 5;
			while($y < $max){
					if(!empty($tags[$y])){
							$html .= '<div class="tag">
													<h4><a href="' . $this->url->create('tags/showtag/' . strtolower(urlencode($tags[$y]->name))) . '">#' . $tags[$y]->name . '</a></h4>
													<p class="tags-text">' . $tags[$y]->text . '</p></div>';
					}
					$y++;
			}
			$html .= '</div>';
			
	}
	echo $html;
?>
</div>
<?php else: ?>
<p>Det finns inga taggar att visa.</p>
<?php endif; ?>
<div class="pagination">
<?=$pagination?>
</div>

