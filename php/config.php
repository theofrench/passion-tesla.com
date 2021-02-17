<?php

try {
	$bdd = new PDO('mysql:host=127.0.0.1;dbname=blog;charset=utf8', 'root', '');
	$bdd->query('SET lc_time_names = "fr_FR"');
} catch (Exception $e) {
	exit('Erreur: '.$e->getMessage());
}

$side_categories = $bdd->query('SELECT * FROM categories ORDER BY categorie');

$side_commentaires = $bdd->query('SELECT * FROM commentaires ORDER BY id DESC LIMIT 5');