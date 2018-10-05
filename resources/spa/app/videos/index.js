import './videos.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './videos.config';
import VideosController from './videos.controller';

let module = angular.module('Music.videos', [
        MusicCommon
    ])
    .controller('VideosController', VideosController)
    .config(Config)
    .name;

export default module;