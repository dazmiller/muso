
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        SONGS: 'Songs',
        SONG_ABOUT      : 'About this song',
        SONG_PUBLISHED  : 'Published',
        SONG_PLAY       : 'Play',
        SONG_FAVORITES  : 'Favorite',
        SONG_PLAYLIST   : 'Add to Playlist',
        SONG_LYRIC      : 'Lyric',
        SONG_DESCRIPTION: 'Description',
        SONG_OTHER_SONGS: 'Other songs in this album',
        SONGS_STATS: 'Stats',
        SONGS_SIMILAR: 'Similar songs',
        SONGS_LIKED_BY: 'Liked by',
        SONG_SHARE: 'Share song',
        SONG_DOWNLOAD: 'Download',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;