import './styles.scss';

/**
 * Generates a button to share the giving link
 */
const ShareButton = () => {
  return {
    restrict: 'E',
    template: [
      '<md-menu md-position-mode="target-right target">',
        '<md-button ng-class="{\'md-raised\':!icon, \'md-icon-button\': icon }" ng-click="openMenu($mdOpenMenu, $event)" aria-label="Share song">',
          '<i class="social-flaticon-social-1" md-menu-origin></i> <span ng-if="!icon" ng-bind="label"></span>',
        '</md-button>',

        '<md-menu-content width="4">',
          '<md-menu-item>',
            '<md-button',
              'socialshare',
              'socialshare-provider="facebook"',
              'socialshare-text="{{text}}"',
              'socialshare-hashtags="{{tags}}"',
              'socialshare-url="{{url}}">',
              '<i class="social-share-button social-flaticon-facebook-app-logo" md-menu-align-target></i>',
              'Facebook',
            '</md-button>',
          '</md-menu-item>',

          '<md-menu-item>',
            '<md-button',
              'socialshare',
              'socialshare-provider="twitter"',
              'socialshare-text="{{text}}"',
              'socialshare-hashtags="{{tags}}"',
              'socialshare-url="{{url}}">',
              '<i class="social-share-button social-flaticon-twitter-logo"></i>',
              'Twitter',
            '</md-button>',
          '</md-menu-item>',

          '<md-menu-item>',
            '<md-button',
              'socialshare',
              'socialshare-provider="linkedin"',
              'socialshare-text="{{text}}"',
              'socialshare-hashtags="{{tags}}"',
              'socialshare-url="{{url}}">',
              '<i class="social-share-button social-flaticon-linkedin-logo"></i>',
              'Linkedin',
            '</md-button>',
          '</md-menu-item>',

          '<md-menu-item>',
            '<md-button',
              'socialshare',
              'socialshare-provider="pinterest"',
              'socialshare-text="{{text}}"',
              'socialshare-hashtags="{{tags}}"',
              'socialshare-url="{{url}}"',
              'socialshare-media="{{image}}">',
              '<i class="social-share-button social-flaticon-pinterest-logo"></i>',
              'Pinterest',
            '</md-button>',
          '</md-menu-item>',

          '<md-menu-item>',
            '<md-button',
              'socialshare',
              'socialshare-provider="tumblr"',
              'socialshare-text="{{text}}"',
              'socialshare-hashtags="{{tags}}"',
              'socialshare-url="{{url}}">',
              '<i class="social-share-button social-flaticon-tumblr-logo"></i>',
              'Tumblr',
            '</md-button>',
          '</md-menu-item>',

        '</md-menu-content>',
      '</md-menu>',
    ].join(' '),
    controller: ['$scope', function ($scope) {
      $scope.openMenu = function ($mdOpenMenu, $event) {
        $mdOpenMenu($event);
      };
    }],
    scope: {
      label: '=',
      url: '=',
      text: '=',
      tags: '=',
      icon: '=',
      image: '=',
    }
  };
};

ShareButton.$inject = [];

export default ShareButton;