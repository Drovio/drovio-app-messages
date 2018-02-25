var jq = jQuery.noConflict();

/**
 * Handles any client events targeted to the MainView.
 *
 * Specifically, this controller can handle the following events:
 *  - diomsg.chat.new
 */
var MainViewController = (function($) {
  
  /**
   * 
   * @param {Object} options
   * @constructor
   */
  function MainViewController(options) {
    options = options || {};
    this.options = $.extend({
      csdfId: 'diomsg-contact-selection-dialog'
    }, options);

    /**
     * The Contact Selection Dialog Frame created by the GetContactSelectionList.
     *
     * @var jQuery
     */
    this.csdf = null;
  };

  /**
   * @var String
   */
  MainViewController.prototype.EVENT_CHAT_NEW = 'diomsg.chat.new';

  /**
   * Handles the event where a new chat is supposed to have been created.
   *
   * First of all, the "Contact Selection Dialog Frame" created by the GetContactSelectionList view is removed.
   */
  MainViewController.prototype.onNewChat = function(event, response) {
    this._getContactSelectionDialogFrame().trigger('dispose');
  };

  /**
   *
   */
  MainViewController.prototype._getContactSelectionDialogFrame = function() {
    if (this.csdf === null) {
      this.csdf = $('#' + this.options.csdfId);
    }

    return this.csdf;
  };

  return MainViewController;
}(jq));

jq(document).ready(function($) {
  var controller = new MainViewController();
  
  $(document).on(MainViewController.prototype.EVENT_CHAT_NEW, $.proxy(controller.onNewChat, controller));
});