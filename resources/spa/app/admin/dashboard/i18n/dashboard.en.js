
let EN = ($translateProvider) => {
    $translateProvider.translations('en', {
        DASHBOARD_PLAYS: 'Plays',
    });
}

EN.$inject = ['$translateProvider'];

export default EN;