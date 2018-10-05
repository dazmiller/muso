import './discover.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('public.discover', {
            url         : "/discover",
            views       : {
                'main@'    : {
                    templateUrl : 'discover/discover.tpl.html',
                    controller  : 'DiscoverController',
                    controllerAs: 'discover'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;