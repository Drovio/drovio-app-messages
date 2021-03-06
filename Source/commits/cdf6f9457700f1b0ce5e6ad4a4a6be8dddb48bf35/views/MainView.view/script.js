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
      chatListIdPrefix: 'diomsg-chat-message-list-',
      chatMessageAuthorIdFieldName: 'diomsg-author-id',
      chatMessageFieldName: 'diomsg-message',
      chatMessageInputBoxCls: 'diomsg-message-input-field',
      chatMessageInputBoxContainerId: 'diomsg-chat-message-input-box-container',
      chatMessageListContainerCls: 'diomsg-chat-message-list-container',
      chatMessageSendButtonId: 'diomsg-send-chat-message-button',
      chatPollInterval: 1500,
      csdfId: 'diomsg-contact-selection-dialog',
      messagePollInterval: 500,
      myChatMessagesAuthorLabel: 'Me',
      newMessageFormChatIdFieldName: 'diomsg-chat-id',
      newMessageFormId: 'diomsg-new-message-form',
      pollPendingMessagesFormChatIdFieldName: 'diomsg-chat-id',
      pollPendingMessagesFormParticipantIdFieldName: 'diomsg-participant-id',
      pollPendingMessagesFormId: 'diomsg-poll-pending-messages-form',
      userChatListContainerId: 'diomsg-user-chat-list-container',
      userChatListFormId: 'diomsg-get-user-chat-list-form',
      userChatListSelectedChatFieldName: 'diomsg-chat-id'
    }, options);

    /**
     * @var jQuery
     */
    this.activeChatList;

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

    /**
     * @var Number
     */
    this.chatPendingMessagesIntervalId;

    /**
     * @var Number
     */
    this.chatPollIntervalId;
  };

  /**
   * @var String
   */
  MainViewController.prototype.EVENT_CHAT_CLOSED = 'diomsg.chat.closed';

  MainViewController.prototype.EVENT_CHAT_NEW = 'diomsg.chat.new';

  MainViewController.prototype.EVENT_CHAT_OPENED = 'diomsg.chat.opened';

  MainViewController.prototype.EVENT_MESSAGE_NEW = "diomsg.message.new";

  MainViewController.prototype.EVENT_PENDING_MESSAGES_GET = 'diomsg.poll_pending_messages.get';

  MainViewController.prototype.KEY_CODE_ENTER = 13;

  /**
   *
   */
  MainViewController.prototype.addMessageToActiveChat = function(author, message) {
    var messageContainer = this._buildChatMessageContainer(author, message);
    this._getActiveChatList().append(messageContainer);
  };

  MainViewController.prototype.onChatClosed = function(event, response) {
    this._stopChatPendingMessagesPolling();
    this._resetActiveChatList();
    this._getChatMessageInputBoxContainer().empty();
  };

  /**
   * 
   */
  MainViewController.prototype.onChatMessageFieldButtonPressed = function(event) {
    if (event.which !== MainViewController.prototype.KEY_CODE_ENTER) {
      return true;
    }

    this._getChatMessageSendButton().click();
  };

  MainViewController.prototype.onChatOpened = function(event, response) {
    var decodedResponse = $.parseJSON(response),
      chatId = decodedResponse['chat_id'],
      messages = decodedResponse['messages'];
    
    this._activateChat(chatId);

    var controller = this;
    $.each(messages, function(index, message) {
      controller.addMessageToActiveChat(message['author_name'], message['content']);
    });
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
    this._activateChat(decodedResponse['chatId']);
  };

  /**
   *
   */
  MainViewController.prototype.onNewMessage = function(event, response) {
    var f = this._getChatMessageField();

    f.prop('disabled', false).focus()
    f.val('');
  };

  MainViewController.prototype.onPendingMessagesGet = function(event, response) {
    var decodedResponse = $.parseJSON(response);

    var messages = decodedResponse['messages'],
      controller = this;
    $.each(messages, function(messageId, message) {
      controller.addMessageToActiveChat(message['author_name'], message['content']);
    }); 
  };

  /**
   *
   */
  MainViewController.prototype.onUserChatListChatSelection = function(event) {
    var selectedChat = $(event.currentTarget),
      selectedChatId = this._parseUserChatListItemForChatId(selectedChat);

    this._getUserChatListSelectedChatField().val(selectedChatId);
    this._getUserChatListForm().submit();
  };

  MainViewController.prototype.run = function() {
    this._startChatPolling();
  };

  MainViewController.prototype._activateChat = function(id) {
    var chatList = this._getActiveChatList();
    if (chatList == null) {
      chatList = this._getFirstFreeChatList();
      this._setActiveChatList(chatList);
    }

    chatList.attr('id', this.options.chatListIdPrefix + id);
    this._startChatPendingMessagesPolling(id);
  };

  /**
   *
   */
  MainViewController.prototype._buildChatMessageContainer = function(author, message) {
    var container = $('<li/>', {
      'class': 'diomsg-message-container'
    });

    $('<span/>', {
      'class': 'diomsg-message-author',
      text: author + ': '
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
    return this.activeChatList;
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

  /**
   * @return jQuery
   */
  MainViewController.prototype._getFirstFreeChatList = function() {
    return $('.' + this.options.chatListCls + ':not([id])');
  };

  MainViewController.prototype._getNewMessageForm = function() {
    return $('#' + this.options.newMessageFormId);
  };

  MainViewController.prototype._getNewMessageFormChatIdField = function() {
    var f = this._getNewMessageForm();

    return f.find('[name="' + this.options.newMessageFormChatIdFieldName + '"]');
  };
 
  /**
   * @return jQuery
   */
  MainViewController.prototype._getPollPendingMessagesForm = function() {
    return $('#' + this.options.pollPendingMessagesFormId);
  };

  MainViewController.prototype._getPollPendingMessagesFormChatIdField = function() {
    var form = this._getPollPendingMessagesForm();

    return form.find('[name="' + this.options.pollPendingMessagesFormChatIdFieldName + '"]');
  };

  MainViewController.prototype._getPollPendingMessagesFormParticipantIdField = function() {
    var form = this._getPollPendingMessagesForm();

    return form.find('[name="' + this.options.pollPendingMessagesFormParticipantIdFieldName + '"]');
  };

  /**
   *
   */
  MainViewController.prototype._getUserChatListContainer = function() {
    return $('#' + this.options.userChatListContainerId);
  };

  /**
   *
   */
  MainViewController.prototype._getUserChatListForm = function() {
    return $('#' + this.options.userChatListFormId);
  };

  /**
   *
   */
  MainViewController.prototype._getUserChatListSelectedChatField = function() {
    var form = this._getUserChatListForm();

    return form.find('[name="' + this.options.userChatListSelectedChatFieldName + '"]');
  };

  /**
   * @param {jQuery} item
   */
  MainViewController.prototype._parseUserChatListItemForChatId = function(item) {
    var itemIdParts = item.attr('id').split('-'),
      lastIndex = itemIdParts.length - 1;

    return itemIdParts[lastIndex];
  };

  MainViewController.prototype._pollForPendingMessagesOfChat = function(chatId) {
    this._setPollPendingMessagesFormChatId(chatId);
    this._getPollPendingMessagesForm().submit();
  };

  MainViewController.prototype._pollForUserChats = function() {
    this._getUserChatListContainer().trigger('reload');
  };

  /**
   * It does two things:
   *  - removes all the messages from the active chat list and
   *  - removes the "id" attribute's value.
   */
  MainViewController.prototype._resetActiveChatList = function() {
    var list = this._getActiveChatList();
    if (list == null) {
      return;
    }

    list.empty();
    list.attr('id', '');
  };

  /**
   * @param {jQuery}
   */
  MainViewController.prototype._setActiveChatList = function(list) {
    this.activeChatList = list;
  };

  MainViewController.prototype._setPollPendingMessagesFormChatId = function(id) {
    var f = this._getPollPendingMessagesFormChatIdField();

    f.val(id);
  };

  MainViewController.prototype._setPollPendingMessagesFormParticipantId = function(id) {
    var f = this._getPollPendingMessagesFormParticipantIdField();

    f.val(id);
  };

  MainViewController.prototype._startChatPendingMessagesPolling = function(chatId) {
    this.chatPendingMessagesIntervalId = setInterval(
      $.proxy(this._pollForPendingMessagesOfChat, this, chatId),
      this.options.messagePollInterval
    );
  };

  MainViewController.prototype._startChatPolling = function() {
    this.chatPollIntervalId = setInterval(
      $.proxy(this._pollForUserChats, this),
      this.options.chatPollInterval
    );
  };

  MainViewController.prototype._stopChatPendingMessagesPolling = function() {
    clearInterval(this.chatPendingMessagesIntervalId);
  };

  MainViewController.prototype._stopChatPolling = function() {
    clearInterval(this.chatPollIntervalId);
  };

  return MainViewController;
}(jq));

jq(document).ready(function($) {
  var controller = new MainViewController({messagePollInterval: 10000});
  
  $(document).on(MainViewController.prototype.EVENT_CHAT_NEW, $.proxy(controller.onNewChat, controller));
  $(document).on(
    'keyup', 
    '#diomsg-chat-message-input-box-content-container [name="diomsg-message"]',
    $.proxy(controller.onChatMessageFieldButtonPressed, controller)
  );
  $(document).on(MainViewController.prototype.EVENT_MESSAGE_NEW, $.proxy(controller.onNewMessage, controller));
  $(document).on(
    'click',
    '#diomsg-user-chat-list .diomsg-user-chat',
    $.proxy(controller.onUserChatListChatSelection, controller)
  );
  $(document).on(MainViewController.prototype.EVENT_PENDING_MESSAGES_GET, $.proxy(controller.onPendingMessagesGet, controller));
  $(document).on(MainViewController.prototype.EVENT_CHAT_OPENED, $.proxy(controller.onChatOpened, controller));
  $(document).on(MainViewController.prototype.EVENT_CHAT_CLOSED, $.proxy(controller.onChatClosed, controller));

  controller.run();
});