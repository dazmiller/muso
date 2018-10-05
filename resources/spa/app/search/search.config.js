import './search.tpl.html';

const Config = ($stateProvider) => {

  $stateProvider
    .state('public.search', {
      url: "/search/:query",
      views: {
        'main@': {
          templateUrl: 'search/search.tpl.html',
          controller: 'SearchController',
          controllerAs: 'searchCtl',
        }
      }
    });
}

Config.$inject = ['$stateProvider'];

export default Config;