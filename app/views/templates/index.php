<!DOCTYPE html>
<html lang="<?=$_SESSION['language']?>">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title><?=CONFIG['name']?></title>
	
	<meta name="author" content="ODE">
	
	<link rel="icon" href="<?= HTTP_ASSETS ?>/favicon/favicon.ico" type="image/x-icon">

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,100,300,500">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/yellow/pace-theme-minimal.min.css">
</head>

<body>
<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
	<a class="navbar-brand" href="/" title="" -data-oa></a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="<?=_('Toggle navigation')?>"><span class="navbar-toggler-icon"></span></button>

	<div class="collapse navbar-collapse" id="navbarMain">
		<ul class="navbar-nav mr-auto">
			<li>
				<a class="nav-link" href="https://github.com/oleksavyshnivky/l10n/" target="_blank">
					GitHub &gt;&gt;
				</a>
			</li>
		</ul>
		<ul class="navbar-nav my-2 my-lg-0">
			<?php foreach (SITELANGS as $key => $params): ?>
			<li class="nav-item">
			<?php if ($key == $_SESSION['language']): ?>
				<a class="nav-link btn language" href="javascript:void(0)" title="<?= html_escape($params['name']) ?>">
					<?=$key?>
				</a>
			<?php else: ?>
				<a class="nav-link btn hollow language hvr-float" href="?hl=<?= $key ?>" title="<?= html_escape($params['name']) ?>">
					<?=$key?>
				</a>
			<?php endif ?>
			</li>
			<?php endforeach ?>
		</ul>
	</div>
</nav>

<main id="main" class="container-fluid pt-2 pb-5" data-oa-main><?=$data['content']?></main>

<!-- Javascript files-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>

