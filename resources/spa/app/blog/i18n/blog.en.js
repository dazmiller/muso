
let EN = ($translateProvider) => {
  $translateProvider.translations('en', {
    LATEST_POSTS: 'Latest posts',
    READ_MORE: 'Read more...',
    POSTS_EMPTY_PUBLISHED: 'Ask the admin to start writing something awesome!',
  });
}

EN.$inject = ['$translateProvider'];

export default EN;