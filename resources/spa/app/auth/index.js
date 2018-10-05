import './auth.scss';

import angular from 'angular';
import uirouter from 'angular-ui-router';
import Satellizer from 'satellizer';

import MusicCommon from '../common';
import Config from './auth.config';
import AuthController from './auth.controller';

import EN from './i18n/auth.en';

let auth = angular.module('Music.auth', [
        uirouter,
        Satellizer,
        MusicCommon
    ])
    .config(Config)
    .config(EN)
    .controller('AuthController', AuthController)
    .name;

export default auth;