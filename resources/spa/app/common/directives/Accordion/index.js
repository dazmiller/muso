import './styles.scss';

const Accordion = () => {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      title: '@',
      description: '@',
      expanded: '=?',
    },
    template: `
      <div class="accordion">
        <div ng-click="toggle()" class="accordion-header">
          <h5>{{title}}</h5>
          <p>{{description}}</p>
        </div>
        <div class="accordion-content">
          <div ng-if="expanded" ng-transclude></div>
        </div>
      </div>
    `,
    controller: ['$scope', function ($scope) {
      // Collapsed by default
      if ($scope.expanded === undefined) {
        $scope.expanded = false;
      }

      $scope.toggle = function() {
        $scope.expanded = !$scope.expanded;
      }
    }],
  };
};

Accordion.$inject = [];

export default Accordion;
