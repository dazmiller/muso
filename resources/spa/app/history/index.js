import './history.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './history.config';
import EN from './i18n/history.en';
import HistoryController from './history.controller';

let module = angular.module('Music.history', [
        MusicCommon
    ])
    .controller('HistoryController', HistoryController)
    .config(Config)
    .config(EN)
    .name;

export default module;