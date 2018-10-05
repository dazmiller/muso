
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        USERS_NEW       : 'New User',
        USERS_EDIT      : 'Edit existing user',
        USERS_ALL       : 'All Users',
        USERS_AUTHORS   : 'Authors',
        USERS_SEARCH    : 'Search by name',

        USERS_OPTIONS   : 'Options',
        USERS_NAME      : 'Name',
        USERS_EMAIL     : 'Email',
        USERS_GENDER    : 'Gender',
        USERS_COUNTRY   : 'Country',
        USERS_POSTCODE  : 'Post Code',
        USERS_OCCUPATION: 'Occupation',
        USERS_ABOUT     : 'About',
        USERS_AUTHOR    : 'Author',
        USERS_ADMIN     : 'Administrator',
        USERS_IMAGE     : 'Profile Picture',
        USERS_UPDATE_IMAGE: 'Click here to update the image',
        USERS_UPLOAD_IMAGE: 'Click here to upload a new image',
        USERS_DELETE_CONTENT: 'This user will be removed, are you sure you want to continue?',
        USERS_DELETE_AUTHOR_CONTENT: 'You are trying to remove an author! The user will be removed but the published content will remain. You need to manually remove the published albums by this user.'
    });
}

EN.$inject = ['$translateProvider'];

export default EN;