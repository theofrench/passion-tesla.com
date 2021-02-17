<?php
require_once 'php/config.php';
require_once 'php/functions.php';

$info_utilisateur = '';
$aucun_resultat = false;

if(isset($_GET['categorie']) AND empty($_GET['categorie'])) {
	header('Location: /');
	exit();
}

if(!empty($_GET['categorie'])) {

	$get_categorie = htmlspecialchars($_GET['categorie']);
	$nom_categorie = getNomCategorie($get_categorie);

	$info_utilisateur = "Catégorie ".$nom_categorie;

	if(!$nom_categorie) {
		header('Location: /');
		exit();
	}

	$articles = $bdd->prepare('SELECT *, DATE_FORMAT(datetime_post, "%d %M %Y") date_formatee FROM articles WHERE categorie = ? ORDER BY datetime_post DESC');
	$articles->execute([$get_categorie]);

} elseif(!empty($_GET['q'])) {

	$query = htmlspecialchars($_GET['q']);

	$info_utilisateur = 'Recherche "'.$query.'"';
	
	$articles = $bdd->prepare('SELECT *, DATE_FORMAT(datetime_post, "%d %M %Y") date_formatee FROM articles WHERE titre LIKE ? ORDER BY datetime_post DESC');
	$articles->execute(['%'.$query.'%']);

	if(!$articles->rowCount()) {
		$aucun_resultat = true;
	}

} else {
	$articles = $bdd->query('SELECT *, DATE_FORMAT(datetime_post, "%d %M %Y") date_formatee FROM articles ORDER BY datetime_post DESC');
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php if($info_utilisateur) { echo $info_utilisateur.' - '; } ?>Blog</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<?php include_once 'includes/head.php' ?>

<?php if($info_utilisateur) { ?>
<h2><?= $info_utilisateur ?></h2>
<?php } ?>

<?php if($aucun_resultat) { ?>
<h3>Aucun résultat ne correspond à votre recherche...</h3>
<?php } ?>

<?php while($a = $articles->fetch(PDO::FETCH_ASSOC)) { ?>
<div class="article">
	<div class="image-wrapper">
		<a href="article.php?id=<?= $a['id'] ?>">
			<img src="images/miniatures/<?= $a['id'] ?>.jpg" alt="<?= $a['titre'] ?>">
		</a>
	</div>
	<h3><a href="article.php?id=<?= $a['id'] ?>"><?= $a['titre'] ?></a></h3>
	<span class="categorie"><?= getNomCategorie($a['categorie']) ?></span> - <span class="date"><?= $a['date_formatee'] ?></span>
	<p><?= substr(strip_tags(htmlspecialchars_decode($a['contenu'])), 0, 120).'...' ?></p>
</div>
<?php } ?>

<?php include_once 'includes/foot.php' ?>

</body>
</html>