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
      closeChatButtonId: 'diomsg-close-chat-button',
      chatIdFieldName: 'diomsg-chat-id',
      chatInitiationMessage: 'diomsg-chat-initiation-message',
      chatListContainerId: 'diomsg-chat-list-container',
      chatListCls: 'diomsg-chat-message-list',
      chatListIdPrefix: 'diomsg-chat-message-list-',
      chatMessageAuthorIdFieldName: 'diomsg-author-id',
      chatMessageContainerCls: 'diomsg-message-container',
      chatMessageFieldName: 'diomsg-message',
      chatMessageInputBoxCls: 'diomsg-message-input-field',
      chatMessageInputBoxContainerId: 'diomsg-chat-message-input-box-container',
      chatMessageListContainerCls: 'diomsg-chat-message-list-container',
      chatMessageSendButtonId: 'diomsg-send-chat-message-button',
// TODO Revert the "chatPollInterval" parameter back to: 60000.
      chatPollInterval: 60000,
      csdfId: 'diomsg-contact-selection-dialog',
// TODO Revert the "messagePollInterval" parameter back to: 5000.
      messagePollInterval: 5000,
      myChatMessagesAuthorLabel: 'Me',
      newMessageFormChatIdFieldName: 'diomsg-chat-id',
      newMessageFormFirstMessageFieldName: 'diomsg-first-message',
      newMessageFormId: 'diomsg-new-message-form',
      pollPendingMessagesFormChatIdFieldName: 'diomsg-chat-id',
      pollPendingMessagesFormParticipantIdFieldName: 'diomsg-participant-id',
      pollPendingMessagesFormId: 'diomsg-poll-pending-messages-form',
      pollPendingMessagesViewContainerId: 'diomsg-poll-pending-messages-view-container',
      userChatListContainerId: 'diomsg-user-chat-list-container',
      userChatListFormId: 'diomsg-get-user-chat-list-form',
      userChatListSelectedChatFieldName: 'diomsg-chat-id',
      userChatListToggleButtonId: 'diomsg-toggle-user-chat-list-button',
      presentation: {
        displayNoneCls: 'diomsg-util-DsN',
        floatRightCls: 'diomsg-util-FlR',
        notMyMessageCls: 'diomsg-message-not-mine'
      }
    }, options);

    /**
     * @var jQuery
     */
    this.activeChatList;

    /**
     * @var jQuery
     */
    this.activeChatListContainer = null;

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
    this.chatPendingMessagesIntervalId = null;

    /**
     * @var Number
     */
    this.chatPollIntervalId = null;

    this.shouldPollForNewChatMessages = false;

    this.shouldPollForNewChats = false;
  };

  /**
   * @var String
   */
  MainViewController.prototype.EVENT_CHAT_CLOSED = 'diomsg.chat.closed';

  MainViewController.prototype.EVENT_CHAT_DELETED = 'diomsg.chat.deleted';

  MainViewController.prototype.EVENT_CHAT_NEW = 'diomsg.chat.new';

  MainViewController.prototype.EVENT_CHAT_OPENED = 'diomsg.chat.opened';

  MainViewController.prototype.EVENT_MESSAGE_NEW = "diomsg.message.new";

  MainViewController.prototype.EVENT_PENDING_MESSAGES_GET = 'diomsg.poll_pending_messages.get';

  MainViewController.prototype.KEY_CODE_ENTER = 13;

  MainViewController.prototype.KEY_CODE_LINE_FEED = 10;

  /**
   *
   */
  MainViewController.prototype.addMessageToActiveChat = function(author, message, mine, createdAt) {
    var messageContainer = this._buildChatMessageContainer(author, message, mine, createdAt);
    var activeChatList = this._getActiveChatList();

    activeChatList.append(messageContainer);
  };

  MainViewController.prototype.onChatClosed = function(event, response) {
    var decodedResponse = $.parseJSON(response),
      chatId = decodedResponse['diomsg-chat-id'],
      activeChatId = this._getActiveChatId();

    if (chatId !== null
        && chatId !== activeChatId) {
      return;
    }

    this._stopChatPendingMessagesPolling();
    this._resetActiveChatList();

    var completeClose = decodedResponse['complete_close'];
    if (!completeClose) {
      return;
    }
    this._getChatMessageInputBoxContainer().empty();
  };

  MainViewController.prototype.onChatDeleted = function(event, response) {
    var decodedResponse = $.parseJSON(response);

    this._refreshChatPolling();
  };

  /**
   * 
   */
  MainViewController.prototype.onChatMessageFieldButtonPressed = function(event) {
    if (event.which !== MainViewController.prototype.KEY_CODE_LINE_FEED
        && event.which !== MainViewController.prototype.KEY_CODE_ENTER) {
      return true;
    }

    if (this._isChatMessageFieldValid()) {
      this._getChatMessageSendButton().click();
    }
  };

  MainViewController.prototype.onChatOpened = function(event, response) {
    var decodedResponse = $.parseJSON(response),
      chatId = decodedResponse['chat_id'],
      messages = decodedResponse['messages'];
    
    this._activateChat(chatId);

    var controller = this;
    $.each(messages, function(index, message) {
      controller.addMessageToActiveChat(message['author_name'], message['content'], message['mine'], message['created_at']);
    });

    this._scrollActiveChatListToLastMessage();
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
    this._refreshChatPolling();
  };

  /**
   *
   */
  MainViewController.prototype.onNewMessage = function(event, response) {
    var decodedResponse = $.parseJSON(response),
      chatId = decodedResponse['chat_id'],
      firstMessage = decodedResponse['first_message'];


    this._clearChatMessageField();
    this._refreshChatPendingMessagesPolling(chatId);
    this._scrollActiveChatListToLastMessage();
    this._refreshChatPolling();

    if (!firstMessage) {
      return;
    }
    this._setNewMessageFormFirstMessageField(0);
  };

  MainViewController.prototype.onPendingMessagesGet = function(event, response) {
    var decodedResponse = $.parseJSON(response),
      messages = decodedResponse['messages'],
      controller = this;

    $.each(messages, function(messageId, message) {
      controller.addMessageToActiveChat(message['author_name'], message['content'], message['mine'], message['created_at']);
    });
    this._refreshChatPolling();

    // If "messages" is an array, then that means that we received no messages from the poll.
    if (!$.isArray(messages)) {
      this._scrollActiveChatListToLastMessage();
    }
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

  MainViewController.prototype.onUserChatListToggleButton = function(event) {
    this._toggleChatPolling();
    this._getUserChatListContainer().toggle();
  };

  MainViewController.prototype.run = function() {
    this._startChatPolling();
  };

  MainViewController.prototype._activateChat = function(id) {
    var chatList = this._getFirstFreeChatList();

    chatList.attr('id', this.options.chatListIdPrefix + id);
    this._startChatPendingMessagesPolling(id);

    // Recollect the chat list via jQuery under an "id" selector.
    //
    // This allows us to use the chat list as a context in DOM traversing via jQuery.
    chatList = $('#' + chatList.attr('id'));
    this._setActiveChatList(chatList);
    
    var container = this._getActiveChatListContainer();
    this._setElementVisible(container, true);
  };

  /**
   *
   */
  MainViewController.prototype._buildChatMessageContainer = function(author, message, mine, createdAt) {
//    var containerCls = !mine
////      ? this.options.presentation.floatRightCls + ' ' + this.options.presentation.notMyMessageCls
//      ? this.options.presentation.floatRightCls
//      : '';
//    containerCls += ' ' + this.options.chatMessageContainerCls;
    containerCls = this.options.chatMessageContainerCls;

    var container = $('<li/>', {
      'class': containerCls
    });

    var amc = $('<div/>').appendTo(container);

    $('<span/>', {
      'class': 'diomsg-message-author',
      text: author + ': '
    }).appendTo(amc);

    $('<p/>', {
      'class': 'diomsg-message',
      text: message
    }).appendTo(amc);

    $('<div/>', {
      'class': this.options.presentation.floatRightCls + ' diomsg-user-chat-list-item-message-created-at',
      text: createdAt
    }).appendTo(container);

    return container;
  };

  MainViewController.prototype._clearChatMessageField = function() {
    var f = this._getChatMessageField();

    f.prop('disabled', false).focus();
    f.val('');
  };

  MainViewController.prototype._getActiveChatId = function() {
    return this._getChatMessageInputBoxContainer().find('[name="' + this.options.chatIdFieldName + '"]').val();
  };

  /**
   *
   */
  MainViewController.prototype._getActiveChatList = function() {
//    if (this.activeChatList == null) {
//      this._setActiveChatList(this._getFirstFreeChatList());
//    }

    return this.activeChatList;
  };

  MainViewController.prototype._getActiveChatListContainer = function() {
    if (this.activeChatListContainer !== null) {
      return this.activeChatListContainer;
    }

    var list = this._getActiveChatList();
    this._setActiveChatListContainer(list.parent('.' + this.options.chatMessageListContainerCls));

    return this.activeChatListContainer;
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

  MainViewController.prototype._getChatMessageFieldValue = function() {
    this._normalizeChatMessage();

    return this._getChatMessageField().val();
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

  MainViewController.prototype._getCloseChatButton = function() {
    return $('#' + this.options.closeChatButtonId);
  };

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

  MainViewController.prototype._getLastMessageOfActiveChatList = function() {
    return this._getActiveChatList().find('.' + this.options.chatMessageContainerCls + ':last-child');
  };

  MainViewController.prototype._getNewMessageForm = function() {
    return $('#' + this.options.newMessageFormId);
  };

  MainViewController.prototype._getNewMessageFormChatIdField = function() {
    var f = this._getNewMessageForm();

    return f.find('[name="' + this.options.newMessageFormChatIdFieldName + '"]');
  };

  MainViewController.prototype._getNewMessageFormFirstMessageField = function() {
    var f = this._getNewMessageForm();

    return f.find('[name="' + this.options.newMessageFormFirstMessageFieldName + '"]');
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

  MainViewController.prototype._getPollPendingMessagesViewContainer = function() {
    return $('#' + this.options.pollPendingMessagesViewContainerId);
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

  MainViewController.prototype._isChatMessageFieldValid = function() {
    return this._getChatMessageFieldValue() !== '';
  };

  /**
   * Removes the Enter key character from the end of a chat message.
   *
   *
   * Preconditions
   *
   * This method assumes that the last character of the current chat message is the Enter key character.
   *
   *
   * Use
   *
   * Due to its preconditions, this method should be called after having established that the Enter key
   * is indeed the last character of the chat message.
   */
  MainViewController.prototype._normalizeChatMessage = function() {
    var messageField = this._getChatMessageField();
    var normalMessage = $.trim(messageField.val());

    messageField.val(normalMessage);
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
    if (!this.shouldPollForNewChatMessages) {
      this._stopChatPendingMessagesPolling();

      return;
    }

//    this._setPollPendingMessagesFormChatId(chatId);
//    this._getPollPendingMessagesForm().submit();
    this._getPollPendingMessagesViewContainer().trigger('reload');

    this.chatPendingMessagesIntervalId = setTimeout(
      $.proxy(this._pollForPendingMessagesOfChat, this, chatId),
      this.options.messagePollInterval
    );
  };

  MainViewController.prototype._pollForUserChats = function() {
    if (!this.shouldPollForNewChats) {
      stop._stopChatPolling();

      return;
    }

    this._getUserChatListContainer().trigger('reload');

    this.chatPollIntervalId = setTimeout(
      $.proxy(this._pollForUserChats, this),
      this.options.chatPollInterval
    );
  };

  MainViewController.prototype._refreshChatPendingMessagesPolling = function(chatId) {
    this._stopChatPendingMessagesPolling();
    this._startChatPendingMessagesPolling(chatId);
  };

  MainViewController.prototype._refreshChatPolling = function() {
    this._stopChatPolling();
    this._startChatPolling();
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

    var container = this._getActiveChatListContainer();
    this._setElementVisible(container, false);
    this._setActiveChatListContainer(null);

    list.empty();
    list.removeAttr('id');
    this._setActiveChatList(null);
  };

  MainViewController.prototype._scrollActiveChatListToLastMessage = function() {
    var container = this._getActiveChatListContainer()
      , chatList = this._getActiveChatList();
//      , lastChatMessage = this._getLastMessageOfActiveChatList();

    container.animate({
      scrollTop: container.offset().top + chatList.outerHeight(true)
    }, 1200);
  };

  /**
   * @param {jQuery}
   */
  MainViewController.prototype._setActiveChatList = function(list) {
    this.activeChatList = list;
  };

  /**
   * @param {jQuery}
   */
  MainViewController.prototype._setActiveChatListContainer = function(container) {
    this.activeChatListContainer = (container != null) && (container.length > 0)
      ? container
      : null;
  };

  /**
   * @param {jQuery} element
   * @param {Boolean} visible
   */
  MainViewController.prototype._setElementVisible = function(element, visible) {
    visible
      ? element.removeClass(this.options.presentation.displayNoneCls)
      : element.addClass(this.options.presentation.displayNoneCls);
  };

  MainViewController.prototype._setNewMessageFormFirstMessageField = function(value) {
    var f = this._getNewMessageFormFirstMessageField();

    f.val(value);
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
    this.shouldPollForNewChatMessages = true;
    this._pollForPendingMessagesOfChat(chatId);
  };

  MainViewController.prototype._startChatPolling = function() {
    this.shouldPollForNewChats = true;
    this._pollForUserChats();
  };

  MainViewController.prototype._stopChatPendingMessagesPolling = function() {
    this.shouldPollForNewChatMessages = false;
    clearTimeout(this.chatPendingMessagesIntervalId);
    this.chatPendingMessagesIntervalId = null;
  };

  MainViewController.prototype._stopChatPolling = function() {
    this.shouldPollForNewChats = false;
    clearTimeout(this.chatPollIntervalId);
    this.chatPollIntervalId = null;
  };

  MainViewController.prototype._toggleChatPolling = function() {
    this.chatPollIntervalId === null
      ? this._startChatPolling()
      : this._stopChatPolling();
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
  $(document).on(MainViewController.prototype.EVENT_MESSAGE_NEW, $.proxy(controller.onNewMessage, controller));
//  $(document).on(
//    'click',
//    '#diomsg-user-chat-list .diomsg-user-chat',
//    $.proxy(controller.onUserChatListChatSelection, controller)
//  );
  $(document).on(MainViewController.prototype.EVENT_PENDING_MESSAGES_GET, $.proxy(controller.onPendingMessagesGet, controller));
  $(document).on(MainViewController.prototype.EVENT_CHAT_OPENED, $.proxy(controller.onChatOpened, controller));
  $(document).on(MainViewController.prototype.EVENT_CHAT_CLOSED, $.proxy(controller.onChatClosed, controller));
  $(document).on(
    'click',
    '#' + controller.options.userChatListToggleButtonId,
    $.proxy(controller.onUserChatListToggleButton, controller)
  );
  $(document).on(MainViewController.prototype.EVENT_CHAT_DELETED, $.proxy(controller.onChatDeleted, controller));

  controller.run();
});