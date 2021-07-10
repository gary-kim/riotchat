window.addEventListener('DOMContentLoaded', () => {
	var appName = 'matrix_integration';
	function url(path) {
		return OC.generateUrl('/apps/' + appName + path);
	}
	function roomPicker(title, callback) {
		var $el = $('<div id="matrix-integration-room-picker" class="popover">');
		$el.append($('<img class="loading icon-loading">'));
		$('body').append($el);

		console.log('WAAAAAAAAAAAAAA');
		console.log(url('/room_summary'));
		$.getJSON(url('/room_summary'), function(data) {
			var rooms = [];
			for (var d of data) {
				rooms.push($('<li>').text(d.display_name));
			}
			$el.empty().append(
				$('<div class="popover__wrapper">').append(
					$('<div class="popover__inner">').append(
						$('<h4>').text(title),
						$('<ul>').append(rooms),
					),
				),
			);
		});
	}

	if (OCA.Sharing && OCA.Sharing.ExternalLinkActions) {
		OCA.Sharing.ExternalLinkActions.registerAction({
			url: link => `matrixshare:${link}`,
			name: t('socialsharing_matrix', 'Share to Matrix'),
			icon: 'icon-matrix'
		});
		$(document).on('click', 'a[href^="matrixshare:"]', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var shareUrl = $(this).attr('href').substr('matrixshare:'.length);
			roomPicker('Select a room to share into', function(roomId) {
				alert(roomId);
				alert('matrix share ' + shareUrl);
			});
		});
	}
});
