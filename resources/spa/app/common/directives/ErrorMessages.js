
var ErrorMessages = () => {
  return {
    restrict: 'E',
    template: [
      '<div class="error-message" ng-if="errors">',
        '<p class="form-error" ng-repeat="error in errors">{{ error }}</p>',
      '</div>',
    ].join(''),
    controller: ['$scope', function ($scope) {
      // @TODO: Add close button
    }],
    scope: {
      errors: '=',
    },
  };
};

ErrorMessages.$inject = [];

export default ErrorMessages;
