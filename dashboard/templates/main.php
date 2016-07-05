<?php

// JS Scripts
\OCP\Util::addScript('dashboard', 'script');

// CSS Styles
\OCP\Util::addStyle('dashboard', 'widgets');
\OCP\Util::addStyle('dashboard', 'control');

/*
$widgets = $_['enabledWidgets'];
$widgetsJson = OC_JSON::encode($widgets);
*/

?>

<div id="app">
	<div id="app-content">
		<?php print_unescaped($this->inc('part.content')); ?>
		<?php print_unescaped($this->inc('part.control')); ?>
	</div>
</div>
