import './albums.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './albums.config';
import EN from './i18n/albums.en';
import AlbumsController from './albums.controller';

let module = angular.module('Music.admin.albums', [
        MusicCommon
    ])
    .controller('AlbumsController', AlbumsController)
    .config(Config)
    .config(EN)
    .name;

export default module;