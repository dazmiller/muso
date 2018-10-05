import './discover.scss';

import angular from 'angular';
import MusicCommon from '../common';
import I18N from 'angular-translate';

import Config from './discover.config';
import DiscoverController from './discover.controller';
import EN from './i18n/discover.en';
import ES from './i18n/discover.es';

let module = angular.module('Music.discover', [
        I18N,
        MusicCommon
    ])
    .controller('DiscoverController', DiscoverController)
    .config(Config)
    .config(EN)
    .config(ES)
    .name;

export default module;