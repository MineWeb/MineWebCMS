<div class="container">
    <div class="row">
		<div class="col-md-6">
			<h1>News</h1>
		</div>
	</div>
</div>
<div class="container">
    <div class="row">
		<?php foreach ($search_news as $news) {?>
			<div class="well">
				<a href="<?= $this->Html->url(array('controller' => 'blog', 'action' => $news['News']['slug'])) ?>"><h3><b><?= $news['News']['title'] ?></b></h3></a>
				<p><b><?= $Lang->get('GLOBAL__UPDATED') ?> : </b><?= $Lang->date($news['News']['updated']) ?></p>
				<p><b><?= $Lang->get('NEWS__COMMENTS_NBR') ?> : </b><?= $news['News']['count_comments'] ?></p>
				<p><b><?= $Lang->get('NEWS__LIKES_NBR') ?> : </b><?= $news['News']['count_likes'] ?></p>
					<hr>
				<p><?php $nmsg = substr($news['News']['content'], 0, 500); echo $nmsg; ?> ...</p>
			</div>
		<php? } ?>
	</div>
</div>
