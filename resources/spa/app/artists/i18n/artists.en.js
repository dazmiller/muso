
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        WHO_TO_FOLLOW     : 'Who to follow',
        AUTHORS_EMPTY     : 'There are not authors yet, start uploading some music!',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;