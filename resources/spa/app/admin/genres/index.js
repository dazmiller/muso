import './genres.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './genres.config';
import EN from './i18n/genres.en';
import AdminGenresController from './genres.controller';

let module = angular.module('Music.admin.genres', [
        MusicCommon
    ])
    .controller('AdminGenresController', AdminGenresController)
    .config(Config)
    .config(EN)
    .name;

export default module;