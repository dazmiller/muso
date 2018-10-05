import './blog.tpl.html';
import './blog.show.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('public.blog', {
            url         : "/blog",
            views       : {
                'main@'    : {
                    templateUrl : 'blog/blog.tpl.html',
                    controller  : 'BlogController',
                    controllerAs: 'blogCtr'
                }
            }
        })
        .state('public.blog.show', {
            url: "/show/:id",
            views: {
                'main@': {
                    templateUrl: 'blog/blog.show.tpl.html',
                    controller: 'BlogController',
                    controllerAs: 'blogCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;