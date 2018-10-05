import './styles.scss';

var MiniPlayer = () => {
  return {
    restrict: 'E',
    template: [
      '<div layout="row" class="mini-player">',
        '<img ng-src="{{song.album.image}}" ui-sref="public.albums({ id: song.album.id })"/>',
        '<div flex class="mini-player-content">',
          // Song info
          '<div layout="row">',
            '<i ng-click="play(song)" ng-if="song.id != current.id" class="flaticon-media23 mini-player-control"></i>',
            '<i ng-click="pause()" ng-if="current.id == song.id && api.currentState == \'play\'" class="flaticon-pause49 mini-player-control mini-player-current"></i>',
            '<i ng-click="play(song)" ng-if="song.id == current.id && api.currentState != \'play\'" class="flaticon-media23 mini-player-control mini-player-current"></i>',
            '<div flex>',
              '<p ui-sref="members.profiles({ id: song.author.id })" ng-bind="song.author.name" class="mini-player-author"></p>',
              '<p ui-sref="public.songs({id:song.id})" ng-bind="song.title" class="mini-player-title"></p>',
            '</div>',
            '<div class="mini-player-info" hide-xs>',
              '<p ng-bind="song.time.date | timeAgo" class="mini-player-date"></p>',
              '<p class="tags">',
                '<a ng-repeat="tag in song.tags" ui-sref="public.explore.tag({tag:tag.name})">{{tag.name}}</a>',
              '</p>',
            '</div>',
          '</div>',
          // Progress
          '<div class="mini-player-progress-wrapper">',
            '<div ng-if="progress" class="mini-player-progress" style="width: {{progress}}%"></div>',
          '</div>',
          // Actions and metadata
          '<div layout="row">',
            '<div flex class="mini-player-stats">',
              '<span><i class="flaticon-pulse2"></i> {{song.plays}}</span>',
              '<span><i class="flaticon-favorite21"></i> {{song.favorites}}</span>',
              '<span><i class="flaticon-comments16"></i> {{song.comments}}</span>',
            '</div>',
            '<div class="mini-player-share">',
              '<share-button',
                'label="\'SONG_SHARE\' | translate"',
                'url="song.id | setFullSongUrl"',
                'text="song.title"',
                'tags="getSongTags(song)"',
                'icon="true"',
                'image="song.album.image"',
                '> ',
              '</share-button>',
            '</div>',
          '</div>',
        '</div>',
      '</div>',
    ].join(' '),
    controller: ['$scope', '$rootScope', '$timeout', '$interval', function ($scope, $rootScope, $timeout, $interval) {
      let timerId;

      $scope.getSongTags = (song) => {
        if (song.tags) {
          return song.tags.map(tag => tag.name).join(',');
        }

        return '';
      };

      $scope.play = (song) => {
        if (!$scope.current || $scope.song.id !== $scope.current.id) {
          $rootScope.$emit('play-song', song, $scope.playlist);
        } else {
          $timeout($scope.api.play.bind($scope.api), 100);
        }

        $scope.startProgress();
      }
      
      $scope.pause = () => {
        $timeout($scope.api.pause.bind($scope.api), 100);
        $scope.stopProgress();
      }

      $scope.startProgress = () => {
        if (!timerId) {
          timerId = $interval(() => {
            if ($scope.current.id === $scope.song.id) {
              $scope.progress = calculateProgress($scope);
            } else {
              $scope.stopProgress();
            }
          }, 500);
        }
      }

      $scope.stopProgress = () => {
        if (timerId) {
          $interval.cancel(timerId);
          timerId = undefined;
        }
      }

      $scope.$on('$destroy', function () {
        // Make sure that the interval is destroyed too
        $scope.stopProgress();
      });

      if ($scope.current && $scope.current.id === $scope.song.id) {
        if ($scope.api.currentState == 'play') {
          $scope.startProgress();
        } else {
          $scope.progress = calculateProgress($scope);
        }
      }
    }],
    scope: {
      api: '=',
      current: '=',
      playlist: '=',
      song: '=',
    },
  };
};

function calculateProgress($scope) {
  return $scope.api.currentTime * 100 / $scope.api.totalTime;
}

MiniPlayer.$inject = [];

export default MiniPlayer;
