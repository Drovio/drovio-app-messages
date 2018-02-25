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

    this._getForm().submit();
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