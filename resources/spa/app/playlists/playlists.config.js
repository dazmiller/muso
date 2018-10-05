import './playlists.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('members.playlists', {
            url         : "/playlists/:id",
            views       : {
                'main@'    : {
                    templateUrl : 'playlists/playlists.tpl.html',
                    controller  : 'PlaylistsController',
                    controllerAs: 'playlistsCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;