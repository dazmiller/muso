import './dashboard.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './dashboard.config';
import DashboardController from './dashboard.controller';

let module = angular.module('Music.dashboard', [
        MusicCommon
    ])
    .controller('DashboardController', DashboardController)
    .config(Config)
    .name;

export default module;