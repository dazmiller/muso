import './app.main.scss';
import './icons/flaticon.css';
import './app.sidenav.tpl.html';
import './app.content.tpl.html';

import angular from 'angular';
import I18N from 'angular-translate';
import Sanitizer from 'angular-sanitize';
import Videogular from 'videogular';
import VideogularControls from 'videogular-controls';
import MusicCommon from '../common';


import AppController from './app.controller';
import EN from './i18n/main.en';
import ES from './i18n/main.es';

let main = angular.module('Music.main', [
        I18N,
        MusicCommon,
        Sanitizer,
        Videogular,
        VideogularControls
    ])
    .controller('AppController', AppController)
    .config(EN)
    .config(ES)
    .name;

export default main;