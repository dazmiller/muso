import './favorites.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('members.favorites', {
            url         : "/favorites",
            views       : {
                'main@'    : {
                    templateUrl : 'favorites/favorites.tpl.html',
                    controller  : 'FavoritesController',
                    controllerAs: 'favoritesCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;