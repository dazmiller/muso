
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        ALBUM_PLAY_ALL: 'Play all',
        ALBUM: 'Album',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;