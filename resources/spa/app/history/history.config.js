import './history.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('members.history', {
            url: '/history',
            views: {
                'main@': {
                    templateUrl: 'history/history.tpl.html',
                    controller: 'HistoryController',
                    controllerAs: 'historyCtr'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;