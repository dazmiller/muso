import './configurations.scss';

import angular from 'angular';
import MusicCommon from '../../common';

import Config from './configurations.config';
import EN from './i18n/configurations.en';
import AdminConfigurationsController from './configurations.controller';

let module = angular.module('Music.admin.configurations', [
  MusicCommon
])
  .controller('AdminConfigurationsController', AdminConfigurationsController)
  .config(Config)
  .config(EN)
  .name;

export default module;