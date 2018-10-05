
let Run = ($mdToast, $rootScope, $state, $translate, $location, $window, GoogleTagManager) => {

    // GA sending to google on page change
    if ($window.Configurations.APP_GA_ENABLED) {
        $rootScope.$on('$stateChangeSuccess', function () {
            GoogleTagManager.page($location.path());
        });
    }

    $rootScope.$on('$stateChangeError', function(e, toState, toParams, fromState, fromParams, error){
        if(error === 'ERROR_ACCESS_DENIED'){
            showErrorMessage([$translate.instant('ERROR_ACCESS_DENIED')]);
        }
    });

    function showErrorMessage(messages){
        $mdToast.show({
            template: ['<md-toast class="md-toast error"><i class="flaticon-letterx5"></i> ', messages.join('<br/>'), '</md-toast>'].join(''),
            hideDelay: 6000,
            position: 'top right'
        });
    }
}

Run.$inject = ['$mdToast', '$rootScope', '$state', '$translate', '$location', '$window', 'GoogleTagManager'];

export default Run;