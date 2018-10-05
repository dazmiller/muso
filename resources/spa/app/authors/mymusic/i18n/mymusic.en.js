
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        ALL_ALBUMS          : 'All Albums',
        CREATE_ALBUM        : 'New Album',
        EDIT_ALBUM          : 'Edit an existing album',
        ALBUM_TITLE         : 'The title of your album',
        ALBUM_DESCRIPTION   : 'A short description about your album',
        ALBUM_OPTIONS       : 'Options',
        ALBUM_IMAGE         : 'Image',
        ALBUM_TRACKS        : 'Tracks',
        ALBUM_PUBLISHED     : 'Published',
        ALBUM_UPLOAD_TRACK  : 'Add Track',
        ALBUM_UPLOAD_IMAGE  : 'Before publishing your album is required to upload an image.',
        ALBUM_UPDATE_IMAGE  : 'Click here to use a different image for your album.',
        ALBUM_EMPTY_TRACKS  : 'Your album is empty! In order to publish this album you need to create a few tracks. Once your tracks are created they will appear here.',
        ALBUM_ADD_TRACK     : 'Upload a new Track',
        ALBUM_EDIT_TRACK    : 'Edit an existing Track',
        ALBUM_SELECT_TRACK  : 'Please select a MP3 file to upload.',
        ALBUMS_EMPTY        : "You don't have any albums yet! If you have music to share with the community, create an album and start adding tracks, once you are ready you can publish the album. Remember that you should be the author of that music or have the rights to share and distribute.",

        ALBUM_DELETE_CONTENT: 'All data will be removed from our servers, The songs in this album will be removed from any user\'s playlist or favorites. Are you sure you want to continue?',
        ALBUM_DELETE_SONG   : 'The song will be removed from this album and from any playlist it\'s already added. Are you sure about that?',

        ALBUM_TRACK_TITLE   : 'A title for your song',
        ALBUM_TRACK_DESCRIPTION : 'Tell us a little about this song.',
        ALBUM_TRACK_LYRIC   : 'You should also provide the lyric of your song (In case people want to learn your lyric).',

        ALBUM_INSTRUCTIONS  : 'Instructions',
        ALBUM_IMAGE_REQUIRED: 'In order to publish the album, is required to assign an image, the image should be a square (500px/500px).',
        ALBUM_SONG_REQUIRED : 'There should be at least one song in the album. You can always add more songs to the album.',
        ALBUM_FILE_REQUIRED : 'For now we are only accepting MP3 files.'


    });
}

EN.$inject = ['$translateProvider'];

export default EN;