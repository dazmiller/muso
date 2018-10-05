import './mymusic.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './mymusic.config';
import ListController from './mymusic.list.controller';
import FormController from './mymusic.form.controller';
import EN from './i18n/mymusic.en';

let module = angular.module('Music.mymusic', [
        MusicCommon
    ])
    .controller('ListController', ListController)
    .controller('FormController', FormController)
    .config(Config)
    .config(EN)
    .name;

export default module;