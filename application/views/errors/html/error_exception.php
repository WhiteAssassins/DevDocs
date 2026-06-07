<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Excepción | DevDocs</title>
	<style>
		body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f6f8fb; color: #1f2933; }
		main { max-width: 920px; margin: 8vh auto; padding: 32px; background: #fff; border: 1px solid #d9e2ec; }
		h1 { margin-top: 0; font-size: 28px; }
		pre { overflow: auto; background: #f1f5f9; padding: 16px; }
		a { color: #0b7285; }
	</style>
</head>
<body>
	<main>
		<h1><?php echo html_escape($heading); ?></h1>
		<p><?php echo html_escape($message); ?></p>
		<?php if (ENVIRONMENT !== 'production'): ?>
			<pre><?php echo html_escape($filepath.' : '.$line); ?></pre>
		<?php endif; ?>
		<p>WhiteAssassins · <a href="https://aewhitedevs.com" rel="noopener">aewhitedevs.com</a></p>
	</main>
</body>
</html>
