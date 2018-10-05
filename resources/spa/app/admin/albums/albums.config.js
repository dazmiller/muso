import './albums.tpl.html';
import './albums.sidebar.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('admin.albums',{
            url         : "/albums",
            abstract    : true
        })
        .state('admin.albums.all', {
            url         : "/all",
            views       : {
                'main@'    : {
                    templateUrl : 'albums/albums.tpl.html',
                    controller  : 'AlbumsController',
                    controllerAs: 'albumsCtr'
                }
            }
        })
        .state('admin.albums.published', {
            url         : "/published",
            views       : {
                'main@'    : {
                    templateUrl : 'albums/albums.tpl.html',
                    controller  : 'AlbumsController',
                    controllerAs: 'albumsCtr'
                }
            }
        })
        .state('admin.albums.unpublished', {
            url         : "/unpublished",
            views       : {
                'main@'    : {
                    templateUrl : 'albums/albums.tpl.html',
                    controller  : 'AlbumsController',
                    controllerAs: 'albumsCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;