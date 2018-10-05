import './artists.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('public.artists', {
            url         : "/artists",
            views       : {
                'main@'    : {
                    templateUrl : 'artists/artists.tpl.html',
                    controller  : 'ArtistsController',
                    controllerAs: 'artists'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;