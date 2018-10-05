import './comments.tpl.html';
import './comments.form.tpl.html';
import './comments.sidebar.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('admin.comments',{
            url         : "/comments",
            views       : {
                'main@'    : {
                    templateUrl : 'comments/comments.tpl.html',
                    controller  : 'AdminCommentsController',
                    controllerAs: 'commentsCtr'
                }
            }
        })
        // .state('admin.comments.published',{
        //     url         : "/published",
        //     views       : {
        //         'main@'    : {
        //             templateUrl : 'comments/comments.tpl.html',
        //             controller  : 'AdminCommentsController',
        //             controllerAs: 'commentsCtr'
        //         }
        //     }
        // })
        .state('admin.comments.show',{
            url         : "/show/:id",
            views       : {
                'main@'    : {
                    templateUrl : 'comments/comments.form.tpl.html',
                    controller  : 'AdminCommentsController',
                    controllerAs: 'commentsCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;