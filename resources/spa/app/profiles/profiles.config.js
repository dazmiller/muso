import './profiles.tpl.html';
import './profiles.settings.tpl.html';
import './profiles.activities.tpl.html';
import './profiles.songs.tpl.html';
import './profiles.followers.tpl.html';
import './profiles.followings.tpl.html';

let Config = ($stateProvider) => {
    
    $stateProvider
        .state('members.profiles', {
            url         : "/profiles/:id",
            views       : {
                'main@'    : {
                    templateUrl : 'profiles/profiles.tpl.html',
                    controller  : 'ProfilesController',
                    controllerAs: 'profiles'
                }
            }
        })
        .state('members.profiles.settings', {
            url         : "/settings",
            views       : {
                'main@'    : {
                    templateUrl : 'profiles/profiles.tpl.html',
                    controller  : 'ProfilesController',
                    controllerAs: 'profiles'
                }
            }
        });
}

Config.$inject = ['$stateProvider'];

export default Config;