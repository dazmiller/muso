import './blog.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './blog.config';
import BlogController from './blog.controller';

import EN from './i18n/blog.en';

let module = angular.module('Music.blog', [
        MusicCommon
    ])
    .controller('BlogController', BlogController)
    .config(Config)
    .config(EN)
    .name;

export default module;