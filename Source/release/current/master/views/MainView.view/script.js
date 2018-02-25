var jq = jQuery.noConflict();

/**
 * Handles any client events targeted to the MainView.
 *
 * All supported events are defined via EVENT constants. See the documentation on each of these constants for further
 * information.
 *
 * Moreover, a handling method is defined for each event. The naming convention of these methods is to prefix the word "on" to the
 * event's name. For example, the "onChatClosed" method handles the EVENT_CHAT_CLOSED event.
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
      chatPollInterval: 60000,
      csdfId: 'diomsg-contact-selection-dialog',
      deleteChatConfirmationMessage: 'Are you sure you want to delete this chat?',
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
        floatLeftCls: 'diomsg-util-FlL',
        floatRightCls: 'diomsg-util-FlR',
        noAuthorAvatarCls: 'diomsg-avatar-empty',
        notMyMessageCls: 'diomsg-message-not-mine'
      }
    }, options);

    /**
     * @type jQuery
     */
    this.activeChatList;

    /**
     * @type jQuery
     */
    this.activeChatListContainer = null;

    /**
     * The box a new message is typed into.
     *
     * @type jQuery
     */
    this.chatMessageInputBox;

    /**
     * @type jQuery
     */
    this.chatMessageList;

    /**
     * @type jQuery
     */
    this.chatMessageSendButton;

    /**
     * @type Number
     */
    this.chatPendingMessagesIntervalId = null;

    /**
     * @typeNumber
     */
    this.chatPollIntervalId = null;

    /**
     * @type Boolean
     */
    this.shouldPollForNewChatMessages = false;

    /**
     * @type Boolean
     */
    this.shouldPollForNewChats = false;
  };

  /**#@+
   * The events this controller can handle.
   *
   * @type String
   */

  /**
   * Indicates that a chat has been closed.
   */
  MainViewController.prototype.EVENT_CHAT_CLOSED = 'diomsg.chat.closed';

  /**
   * Indicates that a chat has been deleted.
   */
  MainViewController.prototype.EVENT_CHAT_DELETED = 'diomsg.chat.deleted';

  /**
   * Indicates that a chat has been created.
   */
  MainViewController.prototype.EVENT_CHAT_NEW = 'diomsg.chat.new';

  /**
   * Indicates that a chat has been opened.
   */
  MainViewController.prototype.EVENT_CHAT_OPENED = 'diomsg.chat.opened';

  /**
   * Indicates that a message been created.
   */
  MainViewController.prototype.EVENT_MESSAGE_NEW = "diomsg.message.new";

  /**
   * Indicates that a message poll has been completed.
   */
  MainViewController.prototype.EVENT_PENDING_MESSAGES_GET = 'diomsg.poll_pending_messages.get';
  /**#@-*/

  /**#@+
   * The codes of various keys this controller is interested in.
   *
   * @type Number
   * @see https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/which
   * @see https://api.jquery.com/keyup/
   */
  MainViewController.prototype.KEY_CODE_ENTER = 13;

  MainViewController.prototype.KEY_CODE_LINE_FEED = 10;
  /**#@-*/

  /**
   * @param {Object} message
   */
  MainViewController.prototype.addMessageToActiveChat = function(message) {
    var messageContainer = this._buildChatMessageContainer(
      message['author_name'],
      message['author_avatar'], 
      message['content'], 
      message['mine'], 
      message['created_at']);
    var activeChatList = this._getActiveChatList();

    activeChatList.append(messageContainer);
  };

  /**
   * @param {jQuery.Event}
   * @param {Object} response
   * @see https://api.jquery.com/category/events/event-object/
   */
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
    this._refreshChatPolling();

    var completeClose = decodedResponse['complete_close'];
    if (!completeClose) {
      return;
    }
    this._getChatMessageInputBoxContainer().empty();
  };

  /**
   * @param {jQuery.Event}
   * @param {Object} response
   * @see https://api.jquery.com/category/events/event-object/
   */
  MainViewController.prototype.onChatDeleted = function(event, response) {
    var decodedResponse = $.parseJSON(response);

    this._refreshChatPolling();
  };

  /**
   * Handles the "click" event of the Message Send Button.
   *
   * @param {jQuery.Event}
   * @see https://api.jquery.com/category/events/event-object/
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

  /**
   * @param {jQuery.Event}
   * @param {Object} response
   * @see https://api.jquery.com/category/events/event-object/
   */
  MainViewController.prototype.onChatOpened = function(event, response) {
    var decodedResponse = $.parseJSON(response),
      chatId = decodedResponse['chat_id'],
      messages = decodedResponse['messages'];
    
    this._activateChat(chatId);

    var controller = this;
    $.each(messages, function(index, message) {
      controller.addMessageToActiveChat(message);
    });

    this._scrollActiveChatListToLastMessage();
  };

  /**
   * @param {jQuery.Event}
   * @see https://api.jquery.com/category/events/event-object/
   */
  MainViewController.prototype.onDeleteChatButtonClick = function(event) {
    var canDelete = confirm(this.options.deleteChatConfirmationMessage);
    if (canDelete) {
      return true;
    }

    event.preventDefault();
    event.stopPropagation();

    return false;
  };

  /**
   * @param {jQuery.Event}
   * @param {Object} response
   * @see https://api.jquery.com/category/events/event-object/
   */
  MainViewController.prototype.onNewChat = function(event, response) {
    var decodedResponse = $.parseJSON(response);

    this._getContactSelectionDialogFrame().trigger('dispose');
    this._activateChat(decodedResponse['chatId']);
    this._refreshChatPolling();
  };

  /**
   * @param {jQuery.Event}
   * @param {Object} response
   * @see https://api.jquery.com/category/events/event-object/
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

  /**
   * @param {jQuery.Event}
   * @param {Object} response
   * @see https://api.jquery.com/category/events/event-object/
   */
  MainViewController.prototype.onPendingMessagesGet = function(event, response) {
    var decodedResponse = $.parseJSON(response),
      messages = decodedResponse['messages'],
      controller = this;

    $.each(messages, function(messageId, message) {
      controller.addMessageToActiveChat(message);
    });
    this._refreshChatPolling();

    // If "messages" is an array, then that means that we received no messages from the poll.
    if (!$.isArray(messages)) {
      this._scrollActiveChatListToLastMessage();
    }
  };

  /**
   * Handles the event where a user clicks on a chat in the "User Chat List".
   *
   * @param {jQuery.Event}
   * @see https://api.jquery.com/category/events/event-object/
   */
  MainViewController.prototype.onUserChatListChatSelection = function(event) {
    var selectedChat = $(event.currentTarget),
      selectedChatId = this._parseUserChatListItemForChatId(selectedChat);

    this._getUserChatListSelectedChatField().val(selectedChatId);
    this._getUserChatListForm().submit();
  };

  /**
   * @param {jQuery.Event}
   * @see https://api.jquery.com/category/events/event-object/
   * @deprecated
   */
  MainViewController.prototype.onUserChatListToggleButton = function(event) {
    this._toggleChatPolling();
    this._getUserChatListContainer().toggle();
  };

  /**
   * Runs actions that are optional to this controller's initialization process.
   *
   * In particular, this method starts the chat polling process. Should the user does not wish to start this process
   * this method should not be called.
   */
  MainViewController.prototype.run = function() {
    this._startChatPolling();
  };

  /**
   * @param {String} id
   */
  MainViewController.prototype._activateChat = function(id) {
    var chatList = this._getFirstFreeChatList();

    chatList.attr('id', this.options.chatListIdPrefix + id);
    this._startChatPendingMessagesPolling(id);

    // Recollect the chat list via jQuery under an "id" selector. This allows us to use the chat list as a context in
    // DOM traversing via jQuery.
    //
    // We may have the "chatList" from above, but its "id" attribute has changed since we retrieved it. By retrieving it again
    // we perform a sort of "refresh" to its "id". If we don't do this recollection the "chatList" will not be possible to be used
    // in expressions such as: $('.child', chatList).
    chatList = $('#' + chatList.attr('id'));
    this._setActiveChatList(chatList);
    
    var container = this._getActiveChatListContainer();
    this._setElementVisible(container, true);
  };

  /**
   * Creates a box that contains the details of a message.
   *
   * @param {String} author The author's name.
   * @param {String} authorAvatar The author avatar's URL.
   * @param {String} message
   * @param {Boolean} mine A value of "true" denotes that the message's author is the currently logged-in user.
   * @param {String} createdAt 
   *   The creation time of the message.
   *   
   *   The format is: MM/DD/YYYY HH:MM:SS. For example, a value of 01/31/2015 06:30:00 means January 31st, 2015 at 6:30pm.
   * @return jQuery
   */
  MainViewController.prototype._buildChatMessageContainer = function(author, authorAvatar, message, mine, createdAt) {
    containerCls = this.options.chatMessageContainerCls;

    var container = $('<li/>', {
      'class': containerCls
    });

    var c1 = $('<div/>').appendTo(container);
    $('<span/>', {
      'class': this.options.presentation.floatRightCls + ' diomsg-user-chat-list-item-message-created-at',
      text: createdAt
    }).appendTo(c1);

    var authorAvatarCls = 'diomsg-avatar-icon';
    if (authorAvatar !== '') {
      $('<img/>', {
        'class': authorAvatarCls + ' diomsg-chat-message-author-avatar',
        src: authorAvatar
      }).appendTo(c1);
    } else {
      $('<span/>', {
        'class': authorAvatarCls + ' ' + this.options.presentation.noAuthorAvatarCls
      }).appendTo(c1);
    }

    $('<span/>', {
      'class': 'diomsg-message-author',
      text: author
    }).appendTo(c1);

    var c2 = $('<div/>').appendTo(container);
    $('<p/>', {
      'class': 'diomsg-message',
      text: message
    }).appendTo(c2);

    return container;
  };

  MainViewController.prototype._clearChatMessageField = function() {
    var f = this._getChatMessageField();

    f.prop('disabled', false).focus();
    f.val('');
  };

  /**
   * @return String
   */
  MainViewController.prototype._getActiveChatId = function() {
    return this._getChatMessageInputBoxContainer().find('[name="' + this.options.chatIdFieldName + '"]').val();
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getActiveChatList = function() {
    return this.activeChatList;
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getActiveChatListContainer = function() {
    if (this.activeChatListContainer !== null) {
      return this.activeChatListContainer;
    }

    var list = this._getActiveChatList();
    this._setActiveChatListContainer(list.parent('.' + this.options.chatMessageListContainerCls));

    return this.activeChatListContainer;
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getChatInitiationMessageContainer = function() {
    return $('#' + this.options.chatListContainerId 
        + ' .' + this.options.chatMessageListContainerCls + ':last-child'
        + ' > .' + this.options.chatInitiationMessage);
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getChatListContainer = function() {
    return $('#' + this.options.chatListContainerId);
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getChatMessageAuthorIdField = function() {
    return this._getChatMessageInputBoxContainer().find('[name="' + this.options.chatMessageAuthorIdFieldName + '"]');
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getChatMessageField = function() {
    return this._getChatMessageInputBoxContainer().find('[name="' + this.options.chatMessageFieldName + '"]');
  };

  /**
   * @return String
   */
  MainViewController.prototype._getChatMessageFieldValue = function() {
    this._normalizeChatMessage();

    return this._getChatMessageField().val();
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getChatMessageInputBoxContainer = function() {    
      return $('#' + this.options.chatMessageInputBoxContainerId);
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getChatMessageSendButton = function() {
      return $('#' + this.options.chatMessageSendButtonId);
  }

  /**
   * @return jQuery
   */
  MainViewController.prototype._getCloseChatButton = function() {
    return $('#' + this.options.closeChatButtonId);
  };

  /**
   * @return jQuery
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

  /**
   * @return jQuery
   */
  MainViewController.prototype._getLastMessageOfActiveChatList = function() {
    return this._getActiveChatList().find('.' + this.options.chatMessageContainerCls + ':last-child');
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getNewMessageForm = function() {
    return $('#' + this.options.newMessageFormId);
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getNewMessageFormChatIdField = function() {
    var f = this._getNewMessageForm();

    return f.find('[name="' + this.options.newMessageFormChatIdFieldName + '"]');
  };

  /**
   * @return jQuery
   */
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

  /**
   * @return jQuery
   */
  MainViewController.prototype._getPollPendingMessagesFormChatIdField = function() {
    var form = this._getPollPendingMessagesForm();

    return form.find('[name="' + this.options.pollPendingMessagesFormChatIdFieldName + '"]');
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getPollPendingMessagesFormParticipantIdField = function() {
    var form = this._getPollPendingMessagesForm();

    return form.find('[name="' + this.options.pollPendingMessagesFormParticipantIdFieldName + '"]');
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getPollPendingMessagesViewContainer = function() {
    return $('#' + this.options.pollPendingMessagesViewContainerId);
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getUserChatListContainer = function() {
    return $('#' + this.options.userChatListContainerId);
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getUserChatListForm = function() {
    return $('#' + this.options.userChatListFormId);
  };

  /**
   * @return jQuery
   */
  MainViewController.prototype._getUserChatListSelectedChatField = function() {
    var form = this._getUserChatListForm();

    return form.find('[name="' + this.options.userChatListSelectedChatFieldName + '"]');
  };

  /**
   * A message field is valid if it is not empty.
   *
   * We could have chosen the name "_isChatMessageFieldEmpty". We preferred the use of the word "valid" since this method is
   * part of the validation process of a new message.
   *
   * @return Boolean
   */
  MainViewController.prototype._isChatMessageFieldValid = function() {
    return this._getChatMessageFieldValue() !== '';
  };

  /**
   * The normalized version of a message has all the white-space characters trimmed from both sides.
   *
   * @see https://api.jquery.com/jQuery.trim/
   */
  MainViewController.prototype._normalizeChatMessage = function() {
    var messageField = this._getChatMessageField();
    var normalMessage = $.trim(messageField.val());

    messageField.val(normalMessage);
  };

  /**
   * The items of the "User Chat List" are assigned an id which indicates the chat they represent. This id value has the
   * following format:
   *
   *     diomsg-user-chat-<chat id>
   *
   * where <chat id> is the id of the chat the item represents.
   *
   * This method parses this "id" attribute and returns the chat's id. That is, the last part of the above format.
   * 
   * @param {jQuery} item
   * @return String The id of the chat that the "item" indicates.
   */
  MainViewController.prototype._parseUserChatListItemForChatId = function(item) {
    var itemIdParts = item.attr('id').split('-'),
      lastIndex = itemIdParts.length - 1;

    return itemIdParts[lastIndex];
  };

  /**
   * @param {String} chatId
   */
  MainViewController.prototype._pollForPendingMessagesOfChat = function(chatId) {
    if (!this.shouldPollForNewChatMessages) {
      this._stopChatPendingMessagesPolling();

      return;
    }
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

  /**
   * @param {String} chatId
   */
  MainViewController.prototype._refreshChatPendingMessagesPolling = function(chatId) {
    this._stopChatPendingMessagesPolling();
    this._startChatPendingMessagesPolling(chatId);
  };

  MainViewController.prototype._refreshChatPolling = function() {
    this._stopChatPolling();
    this._startChatPolling();
  };

  /**
   * This method does two things:
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

    container.animate({
      scrollTop: container.offset().top + chatList.outerHeight(true)
    }, 1200);
  };

  /**
   * @param {jQuery} list
   */
  MainViewController.prototype._setActiveChatList = function(list) {
    this.activeChatList = list;
  };

  /**
   * @param {jQuery} container
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

  /**
   * @param {String} value
   */
  MainViewController.prototype._setNewMessageFormFirstMessageField = function(value) {
    var f = this._getNewMessageFormFirstMessageField();

    f.val(value);
  };

  /**
   * @param {String} id
   */
  MainViewController.prototype._setPollPendingMessagesFormChatId = function(id) {
    var f = this._getPollPendingMessagesFormChatIdField();

    f.val(id);
  };

  /**
   * @param {String} id
   */
  MainViewController.prototype._setPollPendingMessagesFormParticipantId = function(id) {
    var f = this._getPollPendingMessagesFormParticipantIdField();

    f.val(id);
  };

  /**
   * @param {String} chatId
   */
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

/**
 * Sets up the controller's event handlers.
 */
jq(document).ready(function($) {
  var controller = new MainViewController();
  
  $(document).on(MainViewController.prototype.EVENT_CHAT_NEW, $.proxy(controller.onNewChat, controller));
  $(document).on(
    'keyup', 
    '#diomsg-chat-message-input-box-content-container [name="diomsg-message"]',
    $.proxy(controller.onChatMessageFieldButtonPressed, controller)
  );
  $(document).on(MainViewController.prototype.EVENT_MESSAGE_NEW, $.proxy(controller.onNewMessage, controller));
  $(document).on(MainViewController.prototype.EVENT_PENDING_MESSAGES_GET, $.proxy(controller.onPendingMessagesGet, controller));
  $(document).on(MainViewController.prototype.EVENT_CHAT_OPENED, $.proxy(controller.onChatOpened, controller));
  $(document).on(MainViewController.prototype.EVENT_CHAT_CLOSED, $.proxy(controller.onChatClosed, controller));
  $(document).on(
    'click',
    '#' + controller.options.userChatListToggleButtonId,
    $.proxy(controller.onUserChatListToggleButton, controller)
  );
  $(document).on(MainViewController.prototype.EVENT_CHAT_DELETED, $.proxy(controller.onChatDeleted, controller));

  $(document).on(
    'click', 
    '[id^="diomsg-user-chat-list-delete-chat-button-"]',
    $.proxy(controller.onDeleteChatButtonClick, controller)
  );

  controller.run();
});