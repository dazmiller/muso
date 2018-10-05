
var AddToPlaylist = () =>{
    return {
        restrict: 'E',
        template: [
            '<md-menu md-position-mode="target-right target" >',
                '<md-button class="md-raised" aria-label="Open playlists menu" ng-click="$mdOpenMenu($event)">',
                    '<i class="flaticon-song"></i>',
                    '{{\'SONG_PLAYLIST\' | translate}}',
                '</md-button>',
                '<md-menu-content width="4">',

                    '<md-menu-item>',
                        '<md-button ng-click="showPlaylistForm($event)" aria-label="New playlist">',
                            '<div layout="row">',
                                '<p flex translate="ADD_PLAYLIST"></p>',
                                '<i md-menu-align-target class="flaticon-add200"></i>',
                            '</div>',
                        '</md-button>',
                    '</md-menu-item>',

                    '<md-menu-item ng-repeat="playlist in playlists">',
                        '<md-button ng-click="addToPlaylist(playlist)" aria-label="Add to playlist">',
                            '<div layout="row">',
                                '<p flex ng-bind="playlist.title"></p>',
                                '<i md-menu-align-target class="flaticon-song"></i>',
                            '</div>',
                        '</md-button>',
                    '</md-menu-item>',

                '</md-menu-content>',
            '</md-menu>'
        ].join(''),
        controller : ['$scope', 'Playlist', function($scope,Playlist){

            $scope.showPlaylistForm = function(event){
                $scope.add.call($scope.app, event, {}, $scope.song);
            };

            $scope.addToPlaylist = function(playlist){
                Playlist.addSong({
                    playlistId: playlist.id,
                    songId: $scope.song.id
                });
            };
        }],
        scope: {
            app: '=',
            add : '=',
            playlists : '=',
            song      : '='
        }
    };
};

AddToPlaylist.$inject = [];

export default AddToPlaylist;