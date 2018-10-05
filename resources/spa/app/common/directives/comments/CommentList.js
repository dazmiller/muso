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

import './comments.tree.tpl.html';
import './comments.scss';

let CommentList = ($parse, $rootScope, Comment) => {
    return {
        restrict: 'E',
        templateUrl: 'comments/comments.tree.tpl.html',
        scope: {
            comments        : '=',
            current         : '=',
            comentableModel : '=',
            comentableId    : '='
        },
        link: function(scope, element, attrs) {
            var index = 0;

            element.bind('click', onClick);

            $rootScope.$on('add-comment',function(event,comment){
                if(comment.parent_id){
                    for(var i=0,len=scope.comments.length;i<len;i++){
                        var parent = scope.comments[i];
                        if(parent.id === comment.parent_id){
                            if(parent.children){
                                parent.children.push(comment);
                            }else{
                                parent.children = [comment];
                            }
                        }    
                    }
                }else{
                    scope.comments.push(comment);
                }
            });

            scope.reply = function(parent,event){
                var form    = angular.element(document.getElementById('messages-reply-form')),
                    previous= document.getElementById('reply-form-' + (index - 1)),
                    target  = angular.element(event.target).parent().parent(),
                    cloned  = form.clone();
                
                cloned.css('display','block');
                cloned.attr('id','reply-form-' + index);
                cloned.attr('name','replyForm');
                cloned.attr('data-parent-id',parent.id);
                target.append(cloned);

                index++;
                if(previous){
                    previous.remove();
                }
            };


            function onClick(event){
                var send    = isClicked(event.path,'comments-send-button'),
                    cancel  = isClicked(event.path,'comments-cancel-button'),
                    form    = document.forms['replyForm'],
                    previous;

                
                if(send){

                    Comment.post({
                        model       : scope.comentableModel,
                        model_id    : scope.comentableId,
                        parent_id   : angular.element(form).attr('data-parent-id'),
                        body        : form.body.value
                    }).then((response)=>{
                        previous = document.getElementById('reply-form-' + (index - 1));
                        previous.remove();

                        $rootScope.$emit('add-comment',response);
                    });

                } else if(cancel){
                    previous= document.getElementById('reply-form-' + (index - 1));
                    if(previous){
                        previous.remove();
                    }
                }
                
            }

            function isClicked(path, cls){
                var el;

                for(var i=0,len=path.length;i<len;i++){
                    el = path[i];
                    if(angular.element(el).hasClass(cls)){
                        return true;
                    }
                }
                return false;
            }

        }
    };
}

CommentList.$inject = ['$parse','$rootScope','Comment'];

export default CommentList;