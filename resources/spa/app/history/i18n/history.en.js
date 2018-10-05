
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        HISTORY_EMPTY     : 'You haven\'t played a song yet! Go ahead and find something you like'
    });
}

EN.$inject = ['$translateProvider'];

export default EN;