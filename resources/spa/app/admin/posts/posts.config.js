import './posts.list.tpl.html';
import './posts.form.tpl.html';
import './posts.sidebar.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('admin.posts',{
            url         : "/posts",
            abstract    : true
        })
        .state('admin.posts.all', {
            url         : "/all",
            views       : {
                'main@'    : {
                    templateUrl : 'posts/posts.list.tpl.html',
                    controller  : 'PostController',
                    controllerAs: 'postsCtr'
                }
            }
        })
        .state('admin.posts.drafts', {
            url         : "/drafts",
            views       : {
                'main@'    : {
                    templateUrl : 'posts/posts.list.tpl.html',
                    controller  : 'PostController',
                    controllerAs: 'postsCtr'
                }
            }
        })
        .state('admin.posts.add', {
            url         : "/add",
            views       : {
                'main@'    : {
                    templateUrl : 'posts/posts.form.tpl.html',
                    controller  : 'PostController',
                    controllerAs: 'postsCtr'
                }
            }
        })
        .state('admin.posts.show', {
            url         : "/show/:id",
            views       : {
                'main@'    : {
                    templateUrl : 'posts/posts.form.tpl.html',
                    controller  : 'PostController',
                    controllerAs: 'postsCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;