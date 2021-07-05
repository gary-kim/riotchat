<?php
/** @var \OCP\IL10N $l */
/** @var array $_ */
script($_['appName'], 'settings-personal');
style($_['appName'], 'settings-personal');
?>

<div id="matrixSettings" class="section" data-appname="<?php p($appName) ?>">
	<h2>
		<?php p($l->t('Matrix Account')); ?>
	</h2>
	<p id="matrixSettingsLoginForm" style="display:none;">
		<?php p($l->t('Log into your matrix account')); ?>:<br />
		<?php p($l->t('Username')); ?>: <input type="text" id="matrixSettingsLoginUsername" /><br />
		<?php p($l->t('Password')); ?>: <input type="password" id="matrixSettingsLoginPassword" /><br />
		<input id="matrixSettingsLoginButton" type="submit" value="Login" />
	</p>
	<p id="matrixSettingsLogoutForm" style="display:none;">
		<?php p($l->t('Currently logged in as')); ?> <strong id="matrixSettingsUserId"></strong>.<br />
		<input id="matrixSettingsLogoutButton" type="submit" value="Logout" />
	</p>
</div>
