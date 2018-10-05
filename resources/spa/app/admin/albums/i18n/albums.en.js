
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        ALBUMS_NEW       : 'New Album',
        ALBUMS_EDIT      : 'Edit existing user',
        ALBUMS_ALL       : 'All Albums',
        ALBUMS_PUBLISHED : 'Published',
        ALBUMS_UNPUBLISHED : 'Unpublished',
        ALBUMS_SEARCH    : 'Search by name',
        ALBUMS_AUTHOR    : 'Author',

        ALBUMS_OPTIONS   : 'Options',
        ALBUMS_NAME      : 'Name',
        ALBUMS_EMAIL     : 'Email',
        ALBUMS_GENDER    : 'Gender',
        ALBUMS_COUNTRY   : 'Country',
        ALBUMS_POSTCODE  : 'ZIP Code',
        ALBUMS_OCCUPATION: 'Occupation',
        ALBUMS_ABOUT     : 'About',
        ALBUMS_ADMIN     : 'Administrator',
        ALBUMS_IMAGE     : 'Profile Picture',
        ALBUMS_ADMIN_EMPTY: 'None of the authors have published an album yet.',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;