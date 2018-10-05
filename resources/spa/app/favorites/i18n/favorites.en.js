
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        FAVORITES_EMPTY     : 'Please select a song from the list on the left.'
    });
}

EN.$inject = ['$translateProvider'];

export default EN;