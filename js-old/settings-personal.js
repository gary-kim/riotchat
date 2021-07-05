window.addEventListener('DOMContentLoaded', function() {
	var appName = $('#matrixSettings').data('appname');
	function url(path) {
		return OC.generateUrl('/apps/' + appName + path);
	}
	function proccessWhoami(data) {
		$('#matrixSettingsLoginForm').hide();
		$('#matrixSettingsLogoutForm').hide();
		if (!data.logged_in) {
			$('#matrixSettingsLoginForm').show();
		} else {
			$('#matrixSettingsUserId').text(data.user_id);
			$('#matrixSettingsLogoutForm').show();
		}
	}
	$.getJSON(url('/whoami'), proccessWhoami);
	$('#matrixSettingsLoginButton').click(function(e) {
		e.preventDefault();
		var username = $('#matrixSettingsLoginUsername').val();
		var password = $('#matrixSettingsLoginPassword').val();
		$.post(url('/login'), { username, password }, proccessWhoami);
	});
	$('#matrixSettingsLogoutButton').click(function(e) {
		e.preventDefault();
		$.post(url('/logout'), {}, proccessWhoami);
	});
});
