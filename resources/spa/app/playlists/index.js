import './playlists.scss';
import './playlists.form.tpl.html';

import angular from 'angular';
import MusicCommon from '../common';

import EN from './i18n/playlists.en';
import Config from './playlists.config';
import PlaylistsController from './playlists.controller';

let module = angular.module('Music.playlists', [
        MusicCommon
    ])
    .controller('PlaylistsController', PlaylistsController)
    .config(Config)
    .config(EN)
    .name;

export default module;