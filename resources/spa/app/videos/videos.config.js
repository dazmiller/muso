import './videos.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('public.videos', {
            url         : "/videos",
            views       : {
                'main@'    : {
                    templateUrl : 'videos/videos.tpl.html',
                    controller  : 'VideosController',
                    controllerAs: 'videos'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;