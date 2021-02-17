<?php
session_start();

require_once 'php/config.php';

if(!isset($_SESSION['admin']) OR !$_SESSION['admin']) {
	header('Location: /');
}

$message_categorie = '';
if(isset($_POST['categorie'])) {
	if(!empty($_POST['nom']) AND !empty($_POST['slug'])) {
		$nom = htmlspecialchars($_POST['nom']);
		$slug = htmlspecialchars($_POST['slug']);

		$ins = $bdd->prepare('INSERT INTO categories (categorie, categorie_url) VALUES (?, ?)');
		$res = $ins->execute([$nom, $slug]);

		if($res) {
			$message_categorie = 'La nouvelle catégorie a bien été ajoutée !';
		} else {
			$message_categorie = 'Une erreur est survenue durant l\'ajout de la catégorie';
		}

	} else {
		$message_categorie = 'Veuillez renseigner un nom de catégorie ainsi qu\'un slug';
	}
}

$categories = $bdd->query('SELECT * FROM categories');

$message_article = '';
$taille_maximum = 2;
if(isset($_POST['article'])) {
	if(isset($_POST['categorie_article'], $_POST['titre'], $_POST['contenu'], $_FILES['miniature']['tmp_name'])) {
		$categorie = htmlspecialchars($_POST['categorie_article']);
		$titre = htmlspecialchars($_POST['titre']);
		$contenu = htmlspecialchars($_POST['contenu']);
		$miniature = $_FILES['miniature'];

		if(!empty($categorie) AND !empty($titre) AND !empty($contenu) AND !empty($miniature)) {

			if(filesize($miniature['tmp_name']) <= $taille_maximum*1000000) {

				if(exif_imagetype($miniature['tmp_name']) == 2) {

					$ins = $bdd->prepare('INSERT INTO articles (titre, categorie, contenu, datetime_post) VALUES (:titre, :categorie, :contenu, NOW())');
					$res = $ins->execute([
							':titre' => $titre,
							':categorie' => $categorie,
							':contenu' => $contenu
						]);

					if($res) {
						$last_id = $bdd->lastInsertId();

						$chemin = 'images/miniatures/'.$last_id.'.jpg';

						$move = move_uploaded_file($miniature['tmp_name'], $chemin);

						if($move) {
							$message_article = 'Votre article a bien été créé !';
						} else {
							$message_article = 'Une erreur est survenue durant le transfert de votre miniature';
						}

					} else {
						$message_article = 'Une erreur est survenue durant l\'ajout de votre article';
					}

				} else {
					$message_article = 'Votre miniature doit être au format JPG';
				}

			} else {
				$message_article = 'Votre miniature ne peut pas dépasser '.$taille_maximum.'Mo';
			}

		} else {
			$message_article = 'Veuillez compléter tous les champs';
		}

	} else {
		$message_article = 'Veuillez compléter tous les champs';
	}
}

$message_suppr = '';
if(isset($_POST['supprimer_article'])) {
	if(!empty($_POST['id_article'])) {
		$id_article = htmlspecialchars($_POST['id_article']);

		$suppr = $bdd->prepare('DELETE FROM articles WHERE id = ?');
		$res = $suppr->execute([$id_article]);

		if($res) {
			$message_suppr = 'Votre article a bien été supprimé';
		} else {
			$message_suppr = 'Une erreur est survenue durant la suppression de votre article';
		}
	} else {
		$message_suppr = 'Veuillez préciser l\'id de votre article';
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Administration</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<?php include_once 'includes/head.php' ?>

<h2>Administration</h2>
<a href="php/deconnexion.php">Se déconnecter</a>

<br><br>

<h3>Nouvelle catégorie</h3>
<form method="POST">
	<input type="text" name="nom" placeholder="Nom de la catégorie" required>
	<input type="text" name="slug" size="30" placeholder="Slug de la catégorie (dans l'url)" required>
	<input type="submit" name="categorie" value="Créer la catégorie">
</form>
<?php if($message_categorie) { echo '<p>'.$message_categorie.'</p>'; } ?>

<br><br>

<h3>Rédiger un article</h3>
<form method="POST" enctype="multipart/form-data">
	<select name="categorie_article" required>
		<?php while($o = $categories->fetch(PDO::FETCH_ASSOC)) { ?>
			<option value="<?= $o['categorie_url'] ?>"<?php if(isset($categorie) AND $categorie == $o['categorie_url']) { echo ' selected'; } ?>><?= $o['categorie'] ?></option>
		<?php } ?>
	</select>
	<br>
	<input type="text" name="titre" placeholder="Titre de l'article" <?php if(isset($titre)) { echo 'value="'.$titre.'"'; } ?> required>
	<br>
	<textarea name="contenu" placeholder="Contenu de l'article" style="width:80%;" required><?php if(isset($contenu)) { echo $contenu; } ?></textarea>
	<br>
	<input type="file" name="miniature" id="miniature" required><label for="miniature">Miniature de l'article</label>
	<br>
	<input type="submit" value="Publier l'article" name="article">
</form>
<?php if($message_article) { echo '<p>'.$message_article.'</p>'; } ?>

<br><br>

<h3>Supprimer un article</h3>
<form method="POST">
	<input type="number" name="id_article" placeholder="ID de l'article à supprimer" required>
	<br>
	<input type="submit" name="supprimer_article" value="Supprimer l'article">
</form>
<?php if($message_suppr) { echo '<p>'.$message_suppr.'</p>'; } ?>

<?php include_once 'includes/foot.php' ?>

</body>
</html>