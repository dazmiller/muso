import AbstractController from '../common/controllers/abstract.controller';

class MailboxController extends AbstractController {

  constructor() {
    super(MailboxController.$inject, arguments);
  }

  index() {
    this.current = 0;
    this.total = 0;
    this.compose = {};
    this.isSent = this.$state.current.name === 'members.mailbox.sent';
    this.isCompose = this.$state.current.name === 'members.mailbox.compose';

    if (this.isSent) {
      this.MailBox.sent()
        .then((response) => {
          this.threads = response.threads;
          this.current = this.threads.length;
          this.total = this.threads.length;
        });
    } else if (this.isCompose) {
      const userId = this.$state.params.userId;

      if (userId) {
        this.User.byId(userId)
          .then(({ user }) => {
            this.compose.to = user.id;
            this.to = user;
          });
      }
    } else {
      this.MailBox.inbox()
        .then((response) => {
          this.threads = response.threads;
          this.current = this.threads.length;
          this.total = this.threads.length;
        });
    }
  }

  show() {
    this.MailBox.load(this.$state.params.id)
      .then((response) => {
        this.thread = response.thread;
        this.$rootScope.$emit('mailbox-read', response.thread);
      });
  }

  send(mail) {
    this.MailBox.send(mail)
      .then((response) => {
        this.$state.go('members.mailbox.inbox');
      });
  }
  
  reply(mail) {
    this.MailBox.reply({ threadId: this.thread.id, content: mail.content })
      .then((response) => {
        this.$state.go('members.mailbox.inbox');
      });
  }

  searchUser(keyword) {
    return this.MailBox
      .searchUser(keyword)
      .then(response => Promise.resolve(response.users));
  }

  selectedUserChange(user) {
    if (user) {
      this.compose.to = user.id;
      this.$timeout(() => this.to = user, 100);
    }
  }

  editTo() {
    this.compose.to = null;
    this.to = null;
  }

  expand(message) {
    if (message.read) {
      this.thread.messages = this.thread.messages.map((msg) => {
        if (msg.id === message.id) {
          return {
            ...msg,
            read: false,
          };
        }

        return msg;
      });
    }
  }
}

MailboxController.$inject = ['$state', '$rootScope', '$timeout', 'MailBox', 'User'];

export default MailboxController;