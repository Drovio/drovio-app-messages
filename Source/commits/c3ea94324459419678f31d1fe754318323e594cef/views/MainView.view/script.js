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
      chatInitiationMessage: 'diomsg-chat-initiation-message',
      chatListContainerId: 'diomsg-chat-list-container',
      chatListCls: 'diomsg-chat-message-list',
      chatMessageAuthorIdFieldName: 'diomsg-author-id',
      chatMessageFieldName: 'diomsg-message',
      chatMessageInputBoxCls: 'diomsg-message-input-field',
      chatMessageInputBoxContainerId: 'diomsg-chat-message-input-box-container',
      chatMessageListContainerCls: 'diomsg-chat-message-list-container',
      chatMessageSendButtonId: 'diomsg-send-chat-message-button',
      csdfId: 'diomsg-contact-selection-dialog'
    }, options);

    /**
     * @var jQuery
     */
    this.chatMessageInputBox;

    /**
     * @var jQuery
     */
    this.chatMessageList;

    /**
     * @var jQuery
     */
    this.chatMessageSendButton;
  };

  /**
   * @var String
   */
  MainViewController.prototype.EVENT_CHAT_NEW = 'diomsg.chat.new';

  MainViewController.prototype.KEY_CODE_ENTER = 13;

  /**
   *
   */
  MainViewController.prototype.addMessageToActiveChat = function(author, message) {
    if (this.chatList === null) {
      return;
    }

    var messageContainer = this._buildChatMessageContainer(author, message);
    this.chatList.append(messageContainer);
  };

  /**
   * 
   */
  MainViewController.prototype.onChatMessageFieldButtonPressed = function(event) {
    if (event.which !== MainViewController.prototype.KEY_CODE_ENTER) {
      return true;
    }

    var authorIdField = this._getChatMessageAuthorIdField(),
      messageField = this._getChatMessageField();

    this.addMessageToActiveChat(authorIdField.val(), messageField.val());
    this._getChatMessageSendButton().click();
    messageField.val('');
  };

  /**
   * Handles the event where a new chat is supposed to have been created.
   *
   * Firstly, it removes the "Contact Selection Dialog Frame" created by the GetContactSelectionList view.
   *
   * After that, a chat initiation message is displayed.
   *
   * Finally, a new chat message input box is created in order for the owner of the chat to set the first chat message.
   */
  MainViewController.prototype.onNewChat = function(event, response) {
    var decodedResponse = $.parseJSON(response);

    this._getContactSelectionDialogFrame().trigger('dispose');
    this._getChatInitiationMessageContainer().html(decodedResponse['message']);
    this._assignIdToFirstFreeChatList(decodedResponse['chatId']);
  };

  /**
   *
   */
  MainViewController.prototype._assignIdToFirstFreeChatList = function(id) {
    var list = this._getChatListContainer().find('.' + this.options.chatListCls + ':last-child');
    if (list.attr('id') !== undefined) {
      return;
    }
    
    list.attr('id', id);
    this.chatList = list;
  };

  /**
   *
   */
  MainViewController.prototype._buildChatMessageContainer = function(author, message) {
    var container = $('<li/>', {
      'class': 'diomsg-message-container'
    });

    $('<h4/>', {
      'class': 'diomsg-message-author',
      text: author
    }).appendTo(container);

    $('<p/>', {
      'class': 'diomsg-message',
      text: message
    }).appendTo(container);

    return container;
  };

  /**
   *
   */
  MainViewController.prototype._getActiveChatList = function() {
    return this.chatList;
  };

  /**
   *
   */
  MainViewController.prototype._getChatInitiationMessageContainer = function() {
    return $('#' + this.options.chatListContainerId 
        + ' .' + this.options.chatMessageListContainerCls + ':last-child'
        + ' > .' + this.options.chatInitiationMessage);
  };

  /**
   *
   */
  MainViewController.prototype._getChatListContainer = function() {
    return $('#' + this.options.chatListContainerId);
  };

  MainViewController.prototype._getChatMessageAuthorIdField = function() {
    return this._getChatMessageInputBoxContainer().find('[name="' + this.options.chatMessageAuthorIdFieldName + '"]');
  };

  MainViewController.prototype._getChatMessageField = function() {
    return this._getChatMessageInputBoxContainer().find('[name="' + this.options.chatMessageFieldName + '"]');
  };

  /**
   *
   */
  MainViewController.prototype._getChatMessageInputBoxContainer = function() {    
      return $('#' + this.options.chatMessageInputBoxContainerId);
  };

  /**
   *
   */
  MainViewController.prototype._getChatMessageSendButton = function() {
      return $('#' + this.options.chatMessageSendButtonId);
  }

  /**
   *
   */
  MainViewController.prototype._getContactSelectionDialogFrame = function() {
    return $('#' + this.options.csdfId);
  };

  return MainViewController;
}(jq));

jq(document).ready(function($) {
  var controller = new MainViewController();
  
  $(document).on(MainViewController.prototype.EVENT_CHAT_NEW, $.proxy(controller.onNewChat, controller));
  $(document).on(
    'keyup', 
    '#diomsg-chat-message-input-box-content-container [name="diomsg-message"]',
    $.proxy(controller.onChatMessageFieldButtonPressed, controller)
  );
});