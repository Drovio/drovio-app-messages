var jq = jQuery.noConflict();

var GetContactSelectionListViewController = (function($) {

  function Controller(options) {
    options = options || {};
    this.options = $.extend({
      contactSelectionDialogId: 'diomsg-contact-selection-dialog'
      ,firstMessageFieldName: 'diomsg-message'
    }, options);
  };

  Controller.prototype.onFirstMessageFieldEnterPressed = function(event) {
    if (event.which !== 10
        && event.which !== 13) {
      return true;
    }

    this._normalizeFirstMessageFieldValue();
    if (!this._isFormValid()) {
//      this._getForm().find(':submit').click();
      return true;
    }
//    this._isFormValid();

    this._getForm().find(':submit').click();
  };

  Controller.prototype._getContactSelectionDialog = function() {
    return $('#' + this.options.contactSelectionDialogId);
  };

  Controller.prototype._getForm = function() {
    return this._getContactSelectionDialog().find('form');
  };

  Controller.prototype._getFirstMessageField = function() {
    return this._getContactSelectionDialog().find('[name="' + this.options.firstMessageFieldName + '"]');
  };

  /**
   * Validates the form via the "Constraint Validation API" (CV API) if it is available with a fallback to custom validation logic.
   *
   * In case the CV API is available, the return value will be "true". That is in spite of the value returned by the validation
   * performed by the API.
   *
   * In other words, this method returns "false" if and only if the custom validation logic fails.
   *
   * This enables the caller to cancel the form submission only in the case where the custom validation logic fails. In any other
   * case the form can be submitted without any problem.
   *
   * The logic behind the above is this:
   * 
   *  - if the CV API is available, the submission of the form will trigger the display of the native errors in case the CV API has
   *    found any and
   *  - if the CV API is not available, the submission should be cancelled by the caller since this method will return "false" in order
   *    to indicate an invalid form.
   *
   * @return {Boolean} "true" if either native validation took place or if the custom validation logic passed.
   * @see http://www.html5rocks.com/en/tutorials/forms/constraintvalidation/
   */
  Controller.prototype._isFormValid = function() {
    var form = this._getForm().get(0);
    if (typeof form.checkValidity === 'function') {
      form.checkValidity();

      return true;
    }

    var fmf = this._getFirstMessageField();

    return $.trim(fmf.val()) !== '';
  };

  Controller.prototype._normalizeFirstMessageFieldValue = function() {
    var f = this._getFirstMessageField();
    var normalValue = $.trim(f.val());

    f.val(normalValue);
  };

  return Controller;
}(jq));

jq(document).ready(function($) {
  var options = {
    firstMessageFieldName: 'diomsg-message'
  };

  var c = new GetContactSelectionListViewController(options)
    ,d = $(document);

  d.on(
    'keyup',
    '#diomsg-contact-selection-dialog [name="' + options.firstMessageFieldName + '"]',
    $.proxy(c.onFirstMessageFieldEnterPressed, c)
  );
});