

let MailBox = ($auth, Connection) => {
  let service = Connection.resource('/mailbox/messages');

  service.inbox = () => {
    return service.show('received');
  };

  service.unread = (params) => {
    return service.show('unread', params);
  };
  
  service.sent = () => {
    return service.show('sent');
  };

  service.send = (message) => {
    return service.store({
      title: message.title,
      content: message.content,
      recipients: [{ id: message.to }],
    });
  };

  service.reply = (message) => {
    return service.update({
      id: message.threadId,
      content: message.content,
    });
  };

  service.load = (id) => {
    return service.show(id);
  };
  
  service.searchUser = (search) => {
    return Connection.get({
      url: '/mailbox/users',
      data: { search }
    });
  };

  return service;
}

MailBox.$inject = ['$auth', 'Connection'];

export default MailBox;
