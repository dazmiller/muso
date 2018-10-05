import './dashboard.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('admin.dashboard',{
            url         : "/dashboard",
            views       : {
                'main@'    : {
                    templateUrl : 'dashboard/dashboard.tpl.html',
                    controller: 'AdminDashboardController',
                    controllerAs: 'adminDashboardCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;