/**
 *  Comment List directive.
 *  This directive render the comments in a list, we can use it in any module that accept comments.
 *  Support replies, 
 *
        <comment-list 
            comments="controller.comments" 
            current="controller.currentUser" 
            model="'song'"
            model-id="songs.song.id">
        </comment-list>
 *
 */

import './comments.add.tpl.html';

let CommentForm = ($parse, $rootScope, Comment) => {
    return {
        restrict: 'E',
        templateUrl: 'comments/comments.add.tpl.html',
        scope: {
            current         : '=',
            comentableModel : '=',
            comentableId    : '=',
            loading         : '=',
        },
        link: function(scope, element, attrs) {
            scope.comment = {};

            scope.send = function(){
                Comment.store({
                    model   : scope.comentableModel,
                    model_id: scope.comentableId,
                    title   : scope.comment.title,
                    body    : scope.comment.body,
                }).then((response)=>{
                    console.log(response);
                    $rootScope.$emit('add-comment', response.comment);
                    scope.comment = {};
                });
            };
        }
    };
}

CommentForm.$inject = ['$parse','$rootScope','Comment'];

export default CommentForm;