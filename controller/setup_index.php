<?php
$action = '';

if(isset($_GET['article']) && isset($_GET['id'])){
	$action = 'article';
	$id = $_GET['id'];
	$article = getArticle($id);
}else if(isset($_GET['connexion'])){
	$action = 'connexion';
}else if(isset($_GET['logout'])){
	session_destroy();
	header('Location: /'); 
}else if(isset($_GET['new'])){
	if(isset($_SESSION['id'])){
		$action = 'new';		
	}else{
		$action = 'home';
	}
}else if(isset($_GET['all_articles'])){
	$action = 'list_article';
}else{
	$action = 'home';
}