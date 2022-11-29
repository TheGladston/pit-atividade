<?php if (!defined('ABSPATH')) {exit;}
function xfgmc_run_sandbox() { 
	$x = 1; // установите 0, чтобы использовать песочницу и вернуть исключение
	if ($x === 0) { echo __('The sandbox is working. The result will appear below', 'xfgmc').':<br/>'; }
	/* вставьте ваш код ниже */
	

	/* дальше не редактируем */
	if (!$x) {
		echo '<br/>';
		throw new Exception( __('The sandbox is working correctly', 'xfgmc') );
	}
	echo 1/$x;
}
?>