import './comments.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './comments.config';
import EN from './i18n/comments.en';
import AdminCommentsController from './comments.controller';

let module = angular.module('Music.admin.comments', [
        MusicCommon
    ])
    .controller('AdminCommentsController', AdminCommentsController)
    .config(Config)
    .config(EN)
    .name;

export default module;