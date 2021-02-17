	</section>

	<section class="sidebar">

		<form method="GET" action="/">
			<input type="text" name="q" placeholder="Rechercher..."<?php if(isset($query)) { echo ' value="'.$query.'"'; } ?>>
			<input type="submit" value="OK">
		</form>

		<h4>Catégories</h4>
		<ul>
			<?php while($c = $side_categories->fetch(PDO::FETCH_ASSOC)) { ?>
			<li><a href="/?categorie=<?= $c['categorie_url'] ?>"><?= $c['categorie'] ?></a></li>
			<?php } ?>
		</ul>

		<h4>Commentaires récents</h4>
		<ul>
			<?php while($c = $side_commentaires->fetch(PDO::FETCH_ASSOC)) { ?>
			<li>
				<a href="article.php?id=<?= $c['id_article'] ?>#commentaires">
					<i><?= substr($c['contenu'], 0, 100) ?></i><br>
					- <?= $c['nom'] ?>
				</a>
			</li>
			<?php } ?>
		</ul>

	</section>
	<div class="clearfix"></div>
</div>

<footer>
	<div class="container">
		<p>&copy; Tous droits réservés ...</p>
	</div>
</footer>