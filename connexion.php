<?php
session_start();

require_once 'php/config.php';

if(isset($_SESSION['admin']) AND $_SESSION['admin']) {
	header('Location: /administration.php');
}

$erreur = '';
if(isset($_POST['connexion'])) {
	if(isset($_POST['pseudo'], $_POST['mdp'])) {
		$pseudo = htmlspecialchars($_POST['pseudo']);
		$mdp = htmlspecialchars($_POST['mdp']);

		if(!empty($pseudo) AND !empty($mdp)) {

			if(($pseudo == 'theo' AND $mdp == 'Lagalave65') OR ($pseudo == 'admin' AND $mdp == 'azerty')) {
				$_SESSION['admin'] = true;
				header('Location: /administration.php');
			} else {
				$erreur = 'Les identifiants que vous avez saisi sont invalides';
			}

		} else {
			$erreur = 'Veuillez saisir votre nom d\'utilisateur et votre mot de passe';
		}
	} else {
		$erreur = 'Veuillez saisir votre nom d\'utilisateur et votre mot de passe';
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Connexion</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<?php include_once 'includes/head.php' ?>

<h2>Connexion</h2>
<form method="POST">
	<input type="text" placeholder="Nom d'utilisateur" name="pseudo" <?php if(isset($pseudo)) { ?> value="<?= $pseudo ?>"<?php } ?>>
	<br>
	<input type="password" placeholder="Mot de passe" name="mdp" <?php if(isset($mdp)) { ?> value="<?= $mdp ?>"<?php } ?>>
	<br>
	<input type="submit" name="connexion" value="Se connecter">
</form>
<?php if($erreur) { ?>
<p style="color: red;"><?= $erreur ?></p>
<?php } ?>

<?php include_once 'includes/foot.php' ?>

</body>
</html>