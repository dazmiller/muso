import './author.dashboard.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('authors.dashboard', {
            url         : "/dashboard",
            views       : {
                'main@'    : {
                    templateUrl : 'dashboard/author.dashboard.tpl.html',
                    controller  : 'DashboardController',
                    controllerAs: 'dashboard'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;