if (module.hot) {
	module.hot.accept();
}

import {App, BaseComponent, SAGA_CLICK_AJAX_REQUEST_STARTED, AjaxOptions} from "Stage"

class FormComponent extends BaseComponent {

	initial() {
		super.initial();
		this.createSaga(SAGA_CLICK_AJAX_REQUEST_STARTED, this.clickButtonSaga);
		this.createSaga('validate_saga', this.validateSaga);
		this.spanErrorClass = 'help-block';
		this.divErrorClass = 'has-error';
		this.timeout = undefined;
	}

	validateSaga(action) {
		const {payload} = action;
		const {Action} = payload;
		const {data} = Action;
		const forms = data.formsValidation;
		if (!forms) {
			return;
		}

		for (let form of forms) {
			let htmlForm = $('form[id = ' + form.id + ']');

			// RENDER FORM ERRORS
			htmlForm.find(".alert.alert-danger").remove();
			if (typeof form.errors !== "undefined") {
				for (let error of form.errors) {
					let errHtml = $("<div>").addClass("alert alert-danger").attr("role", "alert").html(error);
					htmlForm.prepend(errHtml);
				}
			}

			// VALIDATE ALL FIELDS
			for (let field of form.fields) {
				let fieldName = field.htmlName;
				let errors = field.errors;

				// VALIDATE ONLY ONE FIELD
				// if (selectedFieldName) {
				// 	if (selectedFieldName === fieldName) {
				// 		local.validateField(htmlForm, fieldName, errors);
				// 	}
				// } else {
					// VALIDATE ALL FIELDS
					this.validateField(htmlForm, fieldName, errors);
				// }
			}
		}
	}

	/**
	 * @param {jQuery} form
	 * @param {string} field
	 * @param {[]} errors
	 */
	validateField (form, field, errors) {
		let input = form.find('input[name=' + field + '], select[name=' + field + ']').not('input[type="checkbox"]');
		let div = input.closest("div");

		div.addClass(this.divErrorClass);

		for (let error of errors) {
			let spanError = document.createElement('span');
			spanError.setAttribute('class', this.spanErrorClass);
			spanError.innerHTML = error;
			let exist = false;
			$.each(input.parent().find('.help-block'), function (i, block) {
				if ($(block).html() == $(spanError).html()) {
					exist = true;
				}
			});

			if (!exist) {
				input.after($(spanError));
			}
		}
	}

	/**
	 * @param {jQuery} form
	 * @param {string} fieldName
	 */
	removeErrors (form, fieldName) {

		const divErrorClass = this.divErrorClass;
		const spanErrorClass = this.spanErrorClass;

		if (fieldName) {
			let input = form.find('input[name=' + fieldName + ']');
			let div = input.closest('.' + divErrorClass);
			let span = div.find('span.' + spanErrorClass);
			$(div).removeClass(divErrorClass);
			$(span).remove();
			return;
		}
		let divs = form.find('.' + divErrorClass);
		let spans = form.find('span.' + spanErrorClass);

		$.each(divs, function (i, div) {
			$(div).removeClass(divErrorClass);
		});

		$.each(spans, function (i, span) {
			$(span).remove();
		});
	};

	/**
	 * @param {jQuery} form
	 * @param {string} fieldName
	 */
	hasHtmlErrors (form, fieldName) {
		if (fieldName) {
			let input = form.find('input[name=' + fieldName + ']');
			let div = input.closest('.' + this.divErrorClass);
			return div.length > 0;
		}
		let divs = form.find('.' + this.divErrorClass);
		return divs.length > 0;
	};



	clickButtonSaga(action) {
		const {element, event}  = action.payload;
		if (!element.is("button")) {
			return
		}
		const form = element.closest("form");

		if (form.length === 0) {
			return;
		}
		event.preventDefault();

		this.removeErrors(form);
		this.validateForm(form);

		const the = this;

		clearTimeout(this.timeout);
		this.timeout = setTimeout(function () {
			if (!the.hasHtmlErrors(form)) {
				const formData = new FormData(form[0]);
				formData.append(element[0].getAttribute('name'),'');

				let defaultOption = AjaxOptions({
					url: form[0].action,
					type: 'POST',
					data: formData,
				});

				$.ajax(defaultOption);
			}
		},200);

	}

	validateForm(form) {
		if ($(form).hasClass("novalidate")) {
			return;
		}

		const formData = new FormData(form[0]);
		formData.append('saga','validate_saga');
		formData.append('_validate','1');

		let defaultOption = AjaxOptions({
			type: 'POST',
			url: form[0].action ,
			data: formData,
		});

		$.ajax(defaultOption);
	}


}

App.addComponent("FormComponent", FormComponent);
