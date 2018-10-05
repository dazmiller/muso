
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        COMMENTS_ALL        : 'All Comments',
        COMMENTS_EMPTY      : 'Nobody has left any comment, did you publish some content already?',
        COMMENTS_UNPUBLISHED: 'Unpublished',
        COMMENTS_EDIT       : 'Edit comment',
        COMMENTS_TITLE      : 'Title',
        COMMENTS_CONTENT    : 'Comment',
        COMMENTS_PUBLISHED  : 'Published',
        COMMENTS_DELETE_WARNING : 'Are you sure about removing this comment?',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;