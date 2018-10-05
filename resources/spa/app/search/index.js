import './search.scss';

import angular from 'angular';
import MusicCommon from '../common';

import Config from './search.config';
import EN from './i18n/search.en';
import SearchController from './search.controller';

let module = angular.module('Music.search', [
  MusicCommon
])
  .controller('SearchController', SearchController)
  .config(Config)
  .config(EN)
  .name;

export default module;