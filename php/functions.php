<?php

function getNomCategorie($slug) {
	global $bdd;
	$categorie = $bdd->prepare('SELECT categorie FROM categories WHERE categorie_url = ?');
	$categorie->execute([$slug]);

	$categorie = $categorie->fetchColumn();
	return $categorie;
}