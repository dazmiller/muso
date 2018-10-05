import AbstractController from '../../common/controllers/abstract.controller';

class PostController extends AbstractController{
    constructor(){
        super(PostController.$inject, arguments);

        this.$scope.$watch('postsCtr.post.asset', (newValue, oldValue) => {
            if(newValue && newValue instanceof File){
                var isImage = /^image/;

                if(isImage.test(newValue.type)){
                    this.showImage(newValue)
                }
            }
        });
    }

    index(params){
        this.searching  = false;

        this.Paginator.start();
        this.loadPosts();
    }

    show(params){
        this.Post
            .show(params.id)
            .then(({ post })=>{
                this.post = post;

                if(post.asset){
                    this.post.file = post.asset;
                    this.post.image = post.asset.url;
                }
            });
    }

    add(){
        this.User.requestCurrentUser()
            .then((author)=>{
                this.post = {
                    allow_comments : true,
                    author         : author 
                };
            });
    }

    save(post){
        this.Post.save(post)
            .then(({ post: data })=>{
                const previous = this.post;
                this.post = data;

                if(this.post.asset){
                    this.post.file = this.post.asset;
                    this.post.image = this.post.asset.url;
                } else {
                    this.post.file = previous.file;
                    this.post.image = previous.image;
                }
            });
        
    }

    loadPosts(){
        var params = {
            page    : this.Paginator.getPage(),
            all     : true
        };

        if(this.searching){
            params.search = this.query;
        }

        if(this.$state.current.name === 'admin.posts.drafts'){
            params.drafts = true;
        }

        this.Post.all(params)
            .then(({ posts, meta })=>{
                if(this.Paginator.getPage() > 0 && this.posts){
                    this.posts = [...this.posts, ...posts];
                }else{
                    this.posts = posts;
                }

                this.total  = meta.total;
                this.current= this.posts.length;
            });
    }

    deletePost(post, event){
        event.preventDefault();
        event.stopPropagation();

        var confirm = this.$mdDialog.confirm()
                  .title(this.$translate.instant('ARE_YOU_SURE'))
                  .textContent(this.$translate.instant('POST_DELETE_WARNING'))
                  .ariaLabel('Delete post')
                  .targetEvent(event)
                  .ok(this.$translate.instant('YES'))
                  .cancel(this.$translate.instant('NO'));

        this.$mdDialog.show(confirm)
            .then(() => {
                this.Post.remove(post).then(() => {
                    var index = this.posts.indexOf(post);
                    if (index > -1) this.posts.splice(index, 1);
                });
            });
    }

    nextPage(page){
        this.loadPosts();
    }

    search(){
        this.Paginator.start();
        this.loadPosts();
    }

    toggleSearch(){
        this.searching = !this.searching;
        
        //if closing the search form
        //load all users
        if(!this.searching){
            this.Paginator.start();
            this.query = '';
            this.loadPosts();
        }
    }

    showImage(file){
        let reader = new FileReader();

        this.post.image = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
        this.post.file  = true;

        reader.onload = (event) => {
            let img = document.getElementById('post-image');
            img.src = event.target.result;
        }
        reader.readAsDataURL(file);
    }
}

PostController.$inject = ['$state','$scope','$mdDialog','$translate','Post','User','Paginator'];

export default PostController;