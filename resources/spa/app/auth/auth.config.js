import './auth.login.tpl.html';
import './auth.signup.tpl.html';
import './auth.recovery.tpl.html';
import './auth.forgot.tpl.html';

let Config = ($stateProvider) => {

    $stateProvider
        .state('public.auth', {
            url         : "/auth",
            views       : {
                'main@'    : {
                    templateUrl : 'auth/auth.login.tpl.html',
                    controller  : 'AuthController',
                    controllerAs: 'auth'
                }
            }
        })
        .state('public.auth.signup', {
            url         : "/signup",
            views       : {
                'main@'    : {
                    templateUrl : 'auth/auth.signup.tpl.html',
                    controller  : 'AuthController',
                    controllerAs: 'auth'
                }
            }
        })
        .state('public.auth.recovery', {
            url         : "/recovery/:token",
            views       : {
                'main@'    : {
                    templateUrl: 'auth/auth.recovery.tpl.html',
                    controller  : 'AuthController',
                    controllerAs: 'auth'
                }
            }
        })
        .state('public.auth.forgot', {
            url         : "/forgot",
            views       : {
                'main@'    : {
                    templateUrl: 'auth/auth.forgot.tpl.html',
                    controller  : 'AuthController',
                    controllerAs: 'auth'
                }
            }
        });
}

Config.$inject = ['$stateProvider','$authProvider'];

export default Config;