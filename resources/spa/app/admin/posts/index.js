import './posts.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './posts.config';
import EN from './i18n/posts.en';
import postsController from './posts.controller';

let module = angular.module('Music.admin.posts', [
        // MediumEditor,
        MusicCommon
    ])
    .controller('PostController', postsController)
    .config(Config)
    .config(EN)
    .name;

export default module;