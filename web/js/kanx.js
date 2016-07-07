var notificationElement = document.getElementById('notification');

function showNotification(message) {
	var length = message.length;
	if (message.substring(length - 4, length) != '<br>') {
		message = message + '<br>';
	}
	notificationElement.innerHTML = message + "<button onclick='closeNotification()'>Close</button>";
	notificationElement.setAttribute('style', 'display: block;');
}

function getJSON(action, success) {
	var xhr = new XMLHttpRequest();
	xhr.addEventListener('load', function() {
		try {
			var json = JSON.parse(this.responseText);
			if (json.error) {
				throw json.error;
			}

			success(json);
		} catch (e) {
			showNotification(e);
		}
	});

	xhr.open('GET', action);
	xhr.send();
}

if (notifications) {
	function getNotifications() {
		getJSON('/get-notifications', function(json) {
			var notification = '';
			for (var i in json) {
				notification = notification + json[i] + '<br>';
			}
			
			showNotification(notification);

			setTimeout(getNotifications, 500);
		});
	}

	getNotifications();
}

function closeNotification() {
	window.location.reload();
}
