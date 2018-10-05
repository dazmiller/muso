import './explore.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './explore.config';
import ExploreController from './explore.controller';

let module = angular.module('Music.explore', [
        MusicCommon
    ])
    .controller('ExploreController', ExploreController)
    .config(Config)
    .name;

export default module;