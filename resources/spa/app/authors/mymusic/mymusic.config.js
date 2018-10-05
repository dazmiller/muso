import './mymusic.tpl.html';
import './mymusic.sidebar.tpl.html';
import './mymusic.song.tpl.html';
import './mymusic.form.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('authors.mymusic',{
            url         : "/mymusic",
            abstract    : true
        })
        .state('authors.mymusic.all', {
            url         : "/all",
            views       : {
                'main@'    : {
                    templateUrl : 'mymusic/mymusic.tpl.html',
                    controller  : 'ListController',
                    controllerAs: 'mymusic'
                }
            }
        })
        .state('authors.mymusic.published', {
            url         : "/published",
            views       : {
                'main@'    : {
                    templateUrl : 'mymusic/mymusic.tpl.html',
                    controller  : 'ListController',
                    controllerAs: 'mymusic'
                }
            }
        })
        .state('authors.mymusic.unpublished', {
            url: "/unpublished",
            views       : {
                'main@'    : {
                    templateUrl : 'mymusic/mymusic.tpl.html',
                    controller  : 'ListController',
                    controllerAs: 'mymusic'
                }
            }
        })
        .state('authors.mymusic.add', {
            url         : "/add",
            views       : {
                'main@'    : {
                    templateUrl : 'mymusic/mymusic.form.tpl.html',
                    controller  : 'FormController',
                    controllerAs: 'mymusic'
                }
            }
        })
        .state('authors.mymusic.show', {
            url: "/show/:id/:toAdmin",
            views       : {
                'main@'    : {
                    templateUrl : 'mymusic/mymusic.form.tpl.html',
                    controller  : 'FormController',
                    controllerAs: 'mymusic'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;