
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        GENRE           : 'Genre',
        GENRES_NEW      : 'New Genre',
        GENRES_ALL      : 'All Genres',
        GENRES_ALBUMS   : 'Albums in this genre',
        GENRES_ADD      : 'Add a new genre',
        GENRES_EDIT     : 'Edit an existing genre',
        GENRES_EMPTY    : 'You don\'t have genres yet, go ahead and create some.',
        GENRES_DELETE_WARNING   : 'If you remove this genre, all albums under this genre will have an empty genre.',

        GENRES_NAME     : 'Name'
    });
}

EN.$inject = ['$translateProvider'];

export default EN;