import './album.tpl.html';

const Config = ($stateProvider) => {
    
  $stateProvider
    .state('public.albums', {
      url         : "/albums/:id",
      views       : {
        'main@'    : {
          templateUrl : 'albums/album.tpl.html',
          controller: 'AlbumsPublicController',
          controllerAs: 'albumsPublicCtl',
        }
      }
    });
}

Config.$inject = ['$stateProvider'];

export default Config;