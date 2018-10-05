import './explore.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('public.explore', {
            url         : "/explore",
            abstract    : true
        })
        .state('public.explore.genre', {
            url         : "/genre/:genre",
            views       : {
                'main@'    : {
                    templateUrl : 'explore/explore.tpl.html',
                    controller: 'ExploreController',
                    controllerAs: 'exploreCtr'
                }
            }
        })
        .state('public.explore.tag', {
            url: "/tag/:tag",
            views: {
                'main@': {
                    templateUrl: 'explore/explore.tpl.html',
                    controller: 'ExploreController',
                    controllerAs: 'exploreCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;