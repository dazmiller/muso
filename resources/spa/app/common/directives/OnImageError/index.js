/**
 * If image is not found, just set an empty transparent image to the
 * image src.
 * 
 * <img ng-src="some/image/404.png" on-image-error />
 */
const OnImageError = () => {
  return {
    link: function (scope, element, attrs) {
      element.bind('error', function () {
        attrs.$set('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
      });
    }
  }
};

OnImageError.$inject = [];

export default OnImageError;
