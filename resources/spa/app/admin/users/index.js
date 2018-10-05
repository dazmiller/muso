import './users.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './users.config';
import EN from './i18n/users.en';
import UsersController from './users.controller';

let module = angular.module('Music.admin.users', [
        MusicCommon
    ])
    .controller('UsersController', UsersController)
    .config(Config)
    .config(EN)
    .name;

export default module;