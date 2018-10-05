
let ES = ($translateProvider) => {
    $translateProvider.translations('es', {
        TITLE_APP   : 'MUSIC APP',
        SEARCH      : 'Buscar canciones...',
        DISCOVER    : 'Descubre',
        TRENDING    : 'Tendencia',
        GENRES      : 'Géneros',
        ARTISTS     : 'Artístas',
        VIDEOS      : 'Videos',
        BLOG        : 'Blog',
        YOUR_MUSIC  : 'Tu Música',
        FAVORITES   : 'Favoritos',
        HISTORY     : 'Historial'
    });
}

ES.$inject = ['$translateProvider'];

export default ES;