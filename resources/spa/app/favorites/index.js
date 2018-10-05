import './favorites.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './favorites.config';
import EN from './i18n/favorites.en';
import FavoritesController from './favorites.controller';

let module = angular.module('Music.favorites', [
        MusicCommon
    ])
    .controller('FavoritesController', FavoritesController)
    .config(Config)
    .config(EN)
    .name;

export default module;