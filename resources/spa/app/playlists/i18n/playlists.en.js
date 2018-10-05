
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        ADD_PLAYLIST    : 'Create new Playlist',
        USERS_TITLE     : 'Title',
        PLAYLIST_PUBLIC : 'Public',
        PLAYLIST_EMPTY  : 'This playlist is currently empty, go ahead and add some songs!',
        PLAYLIST_DELETE_WARNING: 'This playlist and all the songs will be removed, are you sure you want to do that?',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;