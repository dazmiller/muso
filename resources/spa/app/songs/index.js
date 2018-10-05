import './songs.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './songs.config';
import EN from './i18n/songs.en';
import SongsController from './songs.controller';

let module = angular.module('Music.songs', [
        MusicCommon
    ])
    .controller('SongsController', SongsController)
    .config(Config)
    .config(EN)
    .name;

export default module;