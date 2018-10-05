import './mailbox.scss';

import angular from 'angular';
import EN from './i18n/mailbox.en';
import MusicCommon from '../common';

import Config from './mailbox.config';
import MailboxController from './mailbox.controller';

let module = angular.module('Music.mailbox', [
        MusicCommon
    ])
    .controller('MailboxController', MailboxController)
    .config(Config)
    .config(EN)
    .name;

export default module;