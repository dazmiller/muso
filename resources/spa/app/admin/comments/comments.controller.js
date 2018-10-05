import AbstractController from '../../common/controllers/abstract.controller';

class AdminCommentsController extends AbstractController{
    constructor(){
        super(AdminCommentsController.$inject, arguments);
    }

    index(params){
        this.Paginator.start();
        this.loadComments();
    }

    loadComments(){
        var params = {
            page    : this.Paginator.getPage()
        };

        this.Comment.all(params)
            .then(({ comments, meta })=>{
                if(this.Paginator.getPage() > 0 && this.comments){
                    this.comments.push(...comments);
                }else{
                    this.comments = comments;
                    this.Paginator.start();
                }

                this.total  = meta.total;
                this.current= this.comments.length;
            });
    }

    show(params){
        this.Comment
            .show(params.id)
            .then(({ comment })=>{
                this.comment = comment;
            });
    }

    save(comment){
        comment.model = 'song';
        this.Comment.update(comment);
    }

    nextPage(page){
        this.loadComments();
    }

    deleteComment(comment, event){
        event.preventDefault();
        event.stopPropagation();

        var confirm = this.$mdDialog.confirm()
                  .title(this.$translate.instant('ARE_YOU_SURE'))
                  .textContent(this.$translate.instant('COMMENTS_DELETE_WARNING'))
                  .ariaLabel('Delete comment')
                  .targetEvent(event)
                  .ok(this.$translate.instant('YES'))
                  .cancel(this.$translate.instant('NO'));

        this.$mdDialog.show(confirm)
            .then(() => {
                this.Comment.remove(comment)
                    .then(() => {
                        this.comments = this.comments.filter(cmt => cmt.id !== comment.id);
                    });
            });
    }
}

AdminCommentsController.$inject = ['$state','$mdSidenav','$mdDialog','$translate','Comment','Paginator'];

export default AdminCommentsController;