
const Options = {
    SERVER_API_URL  : process.env.SERVER_API_URL,

    API_VERSION     : process.env.API_VERSION,

    DEFAULT_LOCALE  : process.env.DEFAULT_LOCALE,

    DEFAULT_ROUTE   : process.env.DEFAULT_ROUTE,

    FACEBOOK_APP_ID: Configurations.APP_FACEBOOK_APP_ID,
};

let Config = ($authProvider, $urlRouterProvider, $stateProvider, $httpProvider, $translateProvider) => {

    //Setting the default language
    $translateProvider.preferredLanguage(Options.DEFAULT_LOCALE);
    $translateProvider.useSanitizeValueStrategy('sanitize');

    //The URL where the server is located
    $authProvider.baseUrl = [Options.SERVER_API_URL,'/',Options.API_VERSION].join('');

    //The default route
    $urlRouterProvider.otherwise(Options.DEFAULT_ROUTE);

    //Enable cross domain calls
    $httpProvider.defaults.useXDomain = true;

    $authProvider.facebook({
        clientId: Options.FACEBOOK_APP_ID,
        authorizationEndpoint: 'https://www.facebook.com/v2.12/dialog/oauth',
    });

    //Defining the two abstract routes with authentication, all children
    //of these two routes will require the user to be authenticated.
    $stateProvider
            .state('admin', {
                url         : '/admin',
                abstract    : true,
                resolve     : {
                    security: ['$auth','$q', function($auth,$q){
                        if(!$auth.user || !$auth.user.admin){
                            return $q.reject('ERROR_ACCESS_DENIED');
                        }
                    }],
                    auth    : ['$auth',function($auth) {
                        return $auth.isAuthenticated();
                    }]
                }
            })
            .state('authors', {
                url         : '/authors',
                abstract    : true,
                resolve     : {
                    security: ['$auth','$q', function($auth,$q){
                        if (!$auth.user || (!$auth.user.author && !$auth.user.admin)){
                            return $q.reject('ERROR_ACCESS_DENIED');
                        }
                    }],
                    auth    : ['$auth',function($auth) {
                        return $auth.isAuthenticated();
                    }]
                }
            })
            .state('members', {
                url         : '/members',
                abstract    : true,
                resolve     : {
                    auth    : ['$auth',function($auth) {
                        return $auth.isAuthenticated();
                    }]
                }
            })
            .state('public', {
                url         : '/public',
                abstract    : true
            });
}

Config.$inject = ['$authProvider', '$urlRouterProvider', '$stateProvider', '$httpProvider', '$translateProvider'];

export default Config;
