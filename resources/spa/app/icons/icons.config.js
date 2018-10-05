import './icons.tpl.html';

let Config = ($stateProvider) => {

    $stateProvider
        .state('public.icons', {
            url: "/icons",
            views       : {
                'main@'    : {
                    templateUrl: 'icons/icons.tpl.html',
                    controller: 'IconsController',
                    controllerAs: 'iconsCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider','$authProvider'];

export default Config;