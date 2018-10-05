
let ES = ($translateProvider) => {
    $translateProvider.translations('es', {
        FOR_YOU     : 'Música para ti',
        TOP_PLAYS   : 'Más Tocadas'
    });
}

ES.$inject = ['$translateProvider'];

export default ES;