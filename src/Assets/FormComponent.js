if (module.hot) {
	module.hot.accept();
}

import 'bootstrap/dist/js/bootstrap';
import 'icheck/icheck';
import 'icheck/skins/square/purple.css';
import 'jquery-datetimepicker/build/jquery.datetimepicker.full';
import 'jquery-datetimepicker/build/jquery.datetimepicker.min.css';
import 'select2/dist/js/select2';
import 'select2/dist/css/select2.css';
import {App, BaseComponent, SAGA_CLICK_AJAX_REQUEST_STARTED, SAGA_REDRAW_SNIPPET, AjaxOptions} from "Stage"

class FormComponent extends BaseComponent {

	initial() {
		super.initial();
		this.installPlugins();
		this.createSaga(SAGA_REDRAW_SNIPPET, this.installPlugins);
		this.createSaga(SAGA_CLICK_AJAX_REQUEST_STARTED, this.clickButtonSaga);
		this.createSaga('validate_saga', this.validateSaga);
		this.spanErrorClass = 'help-block';
		this.divErrorClass = 'has-error';
		this.timeout = undefined;
		this.state = {
			validated: false
		}
	}

	installPlugins(action) {

		let target = document;
		if (action) {
			const {content} = action.payload;
			if (content) {
				target = content
			}
		}

		const the = this;

		$(target).find("form").each(function () {
			let form = this;

			// INPUT LISTENER ON EVENTS
			$(form).find(':input').each(function () {
				let input = this;
				if ($.inArray($(this).prop('name'), ['send', '_do']) < 0) {
					// local.addInputListeners(input)
					the.installInputListener(input);
					console.log(input);
				}
			});
		});

		$(target).find('input').not(".ignore-iCheck").iCheck({
			checkboxClass: 'icheckbox_square-purple',
			radioClass: 'iradio_square-purple',
			increaseArea: '20%'
		});

		$(target).find('.datetimepicker').datetimepicker({
			timepicker:false,
			format:'d.m.Y'
		});
		//
		$(target).find('select').not(".ignore-select2, .searchTable").select2({
			width: '100%'
		});
	}

	installInputListener(el)
	{
		const the = this;
		let timeout;
		$(el).on('keydown', function () {
			if (the.state.validated) {
				the.state.validated = false;
				return;
			}
			let fieldName = $(this)[0].name;

			let form = $(this).closest("form");
			if (the.hasHtmlErrors(form, fieldName)) {
				clearTimeout(timeout);
				timeout = setTimeout(function () {
					// FOR ONLY ONE FIELD IN VALIDATION
					the.removeErrors(form, fieldName);
					the.validateForm(form, fieldName);

					the.state.validated = true;
				},1000);
			}
		});

		$(el).on('focusout', function () {
			let form = $(this).closest("form");

			// FOR ONLY ONE FIELD IN VALIDATION
			let fieldName = $(this)[0].name;
			the.removeErrors(form, fieldName);
			the.validateForm(form, fieldName);

			the.state.validated = true;
		})

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
