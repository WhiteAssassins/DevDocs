<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Error de base de datos | DevDocs</title>
	<style>
		body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f6f8fb; color: #1f2933; }
		main { max-width: 720px; margin: 12vh auto; padding: 32px; background: #fff; border: 1px solid #d9e2ec; }
		h1 { margin-top: 0; font-size: 28px; }
		p { line-height: 1.5; }
		code { white-space: pre-wrap; }
		a { color: #0b7285; }
	</style>
</head>
<body>
	<main>
		<h1><?php echo html_escape($heading); ?></h1>
		<p>La aplicación no pudo completar la operación con la base de datos.</p>
		<?php if (ENVIRONMENT !== 'production'): ?>
			<p><code><?php echo html_escape(strip_tags($message)); ?></code></p>
		<?php endif; ?>
		<p>WhiteAssassins · <a href="https://aewhitedevs.com" rel="noopener">aewhitedevs.com</a></p>
	</main>
</body>
</html>
