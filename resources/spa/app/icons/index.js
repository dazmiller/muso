import './icons.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './icons.config';
import IconsController from './icons.controller';

let module = angular.module('Music.icons', [
    MusicCommon
])
    .controller('IconsController', IconsController)
    .config(Config)
    .name;

export default module;