
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        POSTS_NEW       : 'New post',
        POSTS_ALL       : 'All posts',
        POSTS_DRAFTS    : 'Drafts',
        POSTS_EDIT      : 'Edit post',

        POSTS_TITLE     : 'Title',
        POSTS_CONTENT   : 'Content',
        POSTS_OPTIONS   : 'Options',
        POSTS_PUBLISHED : 'Published',
        POSTS_ALLOW_COMMENTS: 'Allow comments',
        POSTS_IMAGE     : 'Post Image',
        POSTS_AUTHOR    : 'Author',
        POSTS_EMPTY     : "You don't have any drafts yet, go ahead and start writing to your audience!",
        POST_DELETE_WARNING : "Are you sure you want to delete this post? All data will be completly removed.",
        POSTS_UPLOAD_IMAGE  : "Before publising your post, make sure to use a nice image.",
        POSTS_UPDATE_IMAGE  : "Click here to change the current image."
    });
}

EN.$inject = ['$translateProvider'];

export default EN;