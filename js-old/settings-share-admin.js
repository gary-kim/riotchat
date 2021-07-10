window.addEventListener('DOMContentLoaded', function() {
	var appName = $('#matrixSharingSettings').data('appname');

	$('#matrixSharingSettings input').change(function() {
		OCP.AppConfig.setValue(appName, $(this).attr('name'), this.value);
	});

});
