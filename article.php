<?php
require_once 'php/config.php';
require_once 'php/functions.php';

if(!empty($_GET['id'])) {
	$id = htmlspecialchars($_GET['id']);

	$article = $bdd->prepare('SELECT *, DATE_FORMAT(datetime_post, "%d %M %Y") date_formatee FROM articles WHERE id = ?');
	$article->execute([$id]);

	$article = $article->fetch(PDO::FETCH_ASSOC);

	if(!$article) {
		header('Location: /');
		exit();
	}

	$message_commentaire = '';
	if(isset($_POST['commentaire'])) {
		if(isset($_POST['nom'], $_POST['email'], $_POST['contenu'])) {
			$nom = htmlspecialchars($_POST['nom']);
			$email = htmlspecialchars($_POST['email']);
			$contenu = htmlspecialchars($_POST['contenu']);

			if(!empty($nom) AND !empty($email) AND !empty($contenu)) {

				if(filter_var($email, FILTER_VALIDATE_EMAIL)) {

					$ins = $bdd->prepare('INSERT INTO commentaires (id_article, nom, email, contenu) VALUES (:id_article, :nom, :email, :contenu)');
					$res = $ins->execute([
							':id_article' => $article['id'],
							':nom' => $nom,
							':email' => $email,
							':contenu' => $contenu
						]);

					if($res) {
						$message_commentaire = 'Votre commentaire a bien été posté !';
					} else {
						$message_commentaire = 'Une erreur est survenue lors de la publication de votre commentaire';
					}

				} else {
					$message_commentaire = 'Le format de votre adresse mail ne semble pas conforme';
				}

			} else {
				$message_commentaire = 'Veuillez renseigner tous les champs';
			}
		} else {
			$message_commentaire = 'Veuillez renseigner tous les champs';
		}
	}

	$commentaires = $bdd->prepare('SELECT * FROM commentaires WHERE id_article = ? ORDER BY id DESC');
	$commentaires->execute([$article['id']]);

} else {
	header('Location: /');
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?= $article['titre'] ?> - Blog</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<?php include_once 'includes/head.php' ?>

<article>
	<img src="images/miniatures/<?= $article['id'] ?>.jpg" alt="miniature">

	<h2><?= $article['titre'] ?></h2>
	<span class="categorie"><?= getNomCategorie($article['categorie']) ?></span> - <span class="date"><?= $article['date_formatee'] ?></span>

	<div class="contenu">
		<?= htmlspecialchars_decode(nl2br($article['contenu'])) ?>
	</div>
</article>

<hr>

<section id="commentaires" class="commentaires">
	<h2>Commentaires</h2>
	<form method="POST" action="#commentaires">
		<input type="text" name="nom" placeholder="Nom"<?php if(isset($nom)) { echo ' value="'.$nom.'"'; } ?> required>
		<input type="email" name="email" placeholder="Email"<?php if(isset($email)) { echo ' value="'.$email.'"'; } ?> required>
		<br>
		<textarea name="contenu" placeholder="Votre commentaire..." required><?php if(isset($contenu)) { echo $contenu; } ?></textarea>
		<br>
		<input type="submit" name="commentaire" value="Poster le commentaire">
	</form>
	<?php if($message_commentaire) { echo '<p>'.$message_commentaire.'</p>'; } ?>

	<?php while($c = $commentaires->fetch(PDO::FETCH_ASSOC)) { ?>
	<div class="commentaire">
		<p><?= $c['contenu'] ?></p>
		<span class="auteur">- <?= $c['nom'] ?></span>
	</div>
	<?php } ?>

</section>

<?php include_once 'includes/foot.php' ?>

</body>
</html>