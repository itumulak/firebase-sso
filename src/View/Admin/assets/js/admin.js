/**
 * Admin JS.
 *
 * @since 1.0.0
 */

document.addEventListener('DOMContentLoaded', () => {
	const navTabsWrapperClassName = 'nav-tab-wrapper';
	const tabContentWrapperClassName = 'tabs-holder';
	const activeTabClassName = 'nav-tab-active';
	
	document.querySelectorAll(`.${tabContentWrapperClassName} > div`).forEach((element) => {
		element.style.display = 'none';
	});

	if (location.hash.substring(1)) {
		document.getElementById(location.hash.substring(1)).classList.add(activeTabClassName);
		document.getElementById(`${location.hash.substring(1)}-tab`).style.display = 'block';
	}
	else {
		document.querySelector(`.${navTabsWrapperClassName} a:first-child`).classList.add(activeTabClassName);
		document.querySelector(`.${tabContentWrapperClassName} #${document.querySelector(`.${navTabsWrapperClassName} a:first-child`).getAttribute('id')}-tab`).style.display = 'block';
	}

	document.querySelectorAll(`.${navTabsWrapperClassName} a`).forEach((element) => {
		element.addEventListener('click', () => {
			document.querySelectorAll(`.${navTabsWrapperClassName} a`).forEach((element) => {
				element.classList.remove(activeTabClassName);
			});
			element.classList.add(activeTabClassName);

			document.querySelectorAll(`.${tabContentWrapperClassName} > div`).forEach((element) => {
				element.style.display = 'none';
			});
			document.querySelector(`.${tabContentWrapperClassName} > #${element.getAttribute('id')}-tab`).style.display = 'block';
		});
	});

	document.getElementById('configuration-fields').addEventListener('submit', (event) => {
		event.preventDefault();

		const formData = new FormData();
		const formElements = event.target.elements;

		Array.from(formElements).forEach((element) => {
			if (element.tagName === 'INPUT') {
				formData.append(element.id, element.value);	
			}
		});

		formData.append('action', sso_object.config_action);
		formData.append('nonce', sso_object.nonce);

		postSettings(formData, 'Config Update.');
	});

	document.getElementById('sign-in-providers-form').addEventListener('submit', (event) => {
		event.preventDefault();

		const formData = new FormData();
		const formElements = event.target.elements;

		Array.from(formElements).forEach((element) => {
			if (element.checked) {
				formData.append('enabled_providers[]', element.id);
			}
		});

		formData.append('action', sso_object.provider_action);
		formData.append('nonce', sso_object.nonce);

		postSettings(formData, 'Sign-in providers updated.');
	});
});

async function postSettings(data, successText = 'Updated.') {
	await fetch(sso_object.ajaxurl, {
		method: "POST",
		body: data,
		credentials: "same-origin",
	}).then((response) => {
		return response.json();
	}).then((data) => {
		if (data.success) {
			jQuery.toast({heading: "Success", text: successText, showHideTransition: "slide", icon: "success", position: { top: 40, right: 80, }});
		}
	});
}
