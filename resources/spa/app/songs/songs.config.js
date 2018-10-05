import './songs.tpl.html';

const Config = ($stateProvider) => {
    
  $stateProvider
    .state('public.songs', {
      url         : "/songs/:id",
      views       : {
        'main@'    : {
          templateUrl : 'songs/songs.tpl.html',
          controller  : 'SongsController',
          controllerAs: 'songs',
        }
      }
    });
}

Config.$inject = ['$stateProvider'];

export default Config;