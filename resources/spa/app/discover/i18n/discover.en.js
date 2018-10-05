
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        FOR_YOU     : 'Recommendations',
        TOP_PLAYS   : 'Top Ten',
        LATEST      : 'Latests Uploads',
        TOP_ARTISTS : 'Top Artists',
        FOLLOWERS   : 'Followers',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;