import './dashboard.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './dashboard.config';
import EN from './i18n/dashboard.en';
import AdminDashboardController from './dashboard.controller';

let module = angular.module('Music.admin.dashboard', [
        MusicCommon
    ])
    .controller('AdminDashboardController', AdminDashboardController)
    .config(Config)
    .config(EN)
    .name;

export default module;