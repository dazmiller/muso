import './mailbox.inbox.tpl.html';
import './mailbox.show.tpl.html';
import './mailbox.compose.tpl.html';
import './mailbox.sidebar.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('members.mailbox', {
            url: "/mailbox",
            abstract: true
        })
        .state('members.mailbox.compose', {
            url: "/compose/:userId",
            views: {
                'main@': {
                    templateUrl: 'mailbox/mailbox.compose.tpl.html',
                    controller: 'MailboxController',
                    controllerAs: 'mailboxCtl'
                }
            }
        })
        .state('members.mailbox.show', {
            url: "/show/:id",
            views: {
                'main@': {
                    templateUrl: 'mailbox/mailbox.show.tpl.html',
                    controller: 'MailboxController',
                    controllerAs: 'mailboxCtl'
                }
            }
        })
        .state('members.mailbox.inbox', {
            url         : "/inbox",
            views       : {
                'main@'    : {
                    templateUrl : 'mailbox/mailbox.inbox.tpl.html',
                    controller  : 'MailboxController',
                    controllerAs: 'mailboxCtl'
                }
            }
        })
        .state('members.mailbox.sent', {
            url: "/sent",
            views: {
                'main@': {
                    templateUrl: 'mailbox/mailbox.inbox.tpl.html',
                    controller: 'MailboxController',
                    controllerAs: 'mailboxCtl'
                }
            }
        })
        .state('members.mailbox.deleted', {
            url: "/deleted",
            views: {
                'main@': {
                    templateUrl: 'mailbox/mailbox.inbox.tpl.html',
                    controller: 'MailboxController',
                    controllerAs: 'mailboxCtl'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;