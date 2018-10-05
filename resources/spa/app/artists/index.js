import './artists.scss';

import angular from 'angular';
import I18N from 'angular-translate';
import MusicCommon from '../common';

import Config from './artists.config';
import ArtistsController from './artists.controller';
import EN from './i18n/artists.en';

let module = angular.module('Music.artists', [
        MusicCommon
    ])
    .controller('ArtistsController', ArtistsController)
    .config(Config)
    .config(EN)
    .name;

export default module;