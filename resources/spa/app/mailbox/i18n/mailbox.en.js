
let EN = ($translateProvider) => {
  $translateProvider.translations('en', {
    MAILBOX_COMPOSE: 'Compose',
    MAILBOX_INBOX: 'Inbox',
    MAILBOX_SENT: 'Sent',
    MAILBOX_DELETED: 'Deleted',
    MAILBOX_EMPTY: 'You don\'t have any mail in your inbox!',
    MAILBOX_TO: 'To',
    MAILBOX_SUBJECT: 'Subject',
    MAILBOX_CONTENT: 'Type your message here...',
  });
}

EN.$inject = ['$translateProvider'];

export default EN;