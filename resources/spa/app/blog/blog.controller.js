import AbstractController from '../common/controllers/abstract.controller';

class BlogController extends AbstractController {
    constructor() {
        super(BlogController.$inject, arguments);

        if (this.$auth.isAuthenticated()) {
            this.User.requestCurrentUser()
                .then((data) => {
                    this.currentUser = data;
                });
        }
    }

    index(params) {
        this.Paginator.start();
        this.loadPosts();
    }

    show(params) {
        this.Post.published(params.id)
            .then(({ blog }) => {
                this.post = blog;
            });
    }

    loadPosts() {
        var params = {
            page: this.Paginator.getPage(),
        };

        this.Post.latestPublished(params)
            .then(({ blog: posts, meta }) => {
                if (this.Paginator.getPage() > 0 && this.posts) {
                    this.posts = [...this.posts, ...posts];
                } else {
                    this.posts = posts;
                }

                this.total = meta.total;
                this.current = this.posts.length;
            });
    }

    nextPage() {
        this.loadPosts();
    }
}

BlogController.$inject = ['$state', '$auth', 'Post', 'Paginator', 'User'];

export default BlogController;