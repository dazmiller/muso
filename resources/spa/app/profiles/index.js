import './profiles.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './profiles.config';
import EN from './i18n/profiles.en';
import ProfilesController from './profiles.controller';

let module = angular.module('Music.profiles', [
        MusicCommon
    ])
    .controller('ProfilesController', ProfilesController)
    .config(Config)
    .config(EN)
    .name;

export default module;