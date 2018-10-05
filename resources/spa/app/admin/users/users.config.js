import './users.tpl.html';
import './users.form.tpl.html';
import './users.sidebar.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('admin.users',{
            url         : "/users",
            abstract    : true
        })
        .state('admin.users.all', {
            url         : "/all",
            views       : {
                'main@'    : {
                    templateUrl : 'users/users.tpl.html',
                    controller  : 'UsersController',
                    controllerAs: 'usersCtr'
                }
            }
        })
        .state('admin.users.authors', {
            url         : "/authors",
            views       : {
                'main@'    : {
                    templateUrl : 'users/users.tpl.html',
                    controller  : 'UsersController',
                    controllerAs: 'usersCtr'
                }
            }
        })
        .state('admin.users.show', {
            url         : "/show/:id",
            views       : {
                'main@'    : {
                    templateUrl : 'users/users.form.tpl.html',
                    controller  : 'UsersController',
                    controllerAs: 'usersCtr'
                }
            }
        });;
}

Config.$inject = ['$stateProvider'];

export default Config;