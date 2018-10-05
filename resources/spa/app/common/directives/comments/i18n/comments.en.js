
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        COMMENTS_BODY   : 'Your comment here...',
        COMMENT_SEND    : 'Send',
        COMMENTS_LEAVE  : 'Leave a message',
        COMMENTS_SUBJECT: 'Subject'
    });
}

EN.$inject = ['$translateProvider'];

export default EN;