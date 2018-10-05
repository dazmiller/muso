import './genres.tpl.html';
import './genres.sidebar.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('admin.genres',{
            url         : "/genres",
            views       : {
                'main@'    : {
                    templateUrl : 'genres/genres.tpl.html',
                    controller  : 'AdminGenresController',
                    controllerAs: 'genresCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;