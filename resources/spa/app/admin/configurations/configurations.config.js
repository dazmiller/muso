import './configurations.tpl.html';
import './configurations.sidebar.tpl.html';
import './configurations.global.tpl.html';
import './configurations.ga.tpl.html';
import './configurations.facebook.tpl.html';
import './configurations.legal.tpl.html';
import './configurations.theme.tpl.html';

let Config = ($stateProvider) => {

  $stateProvider
    .state('admin.configurations', {
      url: "/configurations",
      views: {
        'main@': {
          templateUrl: 'configurations/configurations.tpl.html',
          controller: 'AdminConfigurationsController',
          controllerAs: 'configCtr'
        }
      }
    });
}

Config.$inject = ['$stateProvider'];

export default Config;