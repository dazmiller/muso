import angular from 'angular';
import Chartist from 'chartist';

class ChartistCtrl {
  constructor($scope, $element) {
    this.data = $scope.data;
    this.chartType = $scope.chartType;

    this.options = $scope.chartOptions() || null;
    this.events = $scope.events() || {};
    this.responsiveOptions = $scope.responsiveOptions() || null;

    this.element = $element[0];

    this.renderChart();

    $scope.$watch(
      () => {
        return {
          data: $scope.data,
          chartType: $scope.chartType,
          chartOptions: $scope.chartOptions() || null,
          responsiveOptions: $scope.responsiveOptions() || null,
          events: $scope.events() || {}
        };
      },
      this.update.bind(this),
      true
    );

    $scope.$on('$destroy', () => {
      if (this.chart) {
        this.chart.detach();
      }
    });
  }

  bindEvents() {
    Object.keys(this.events).forEach(eventName => {
      this.chart.on(eventName, this.events[eventName]);
    });
  }

  unbindEvents(events) {
    Object.keys(events).forEach(eventName => {
      this.chart.off(eventName, events[eventName]);
    });
  }

  renderChart() {
    // ensure that the chart does not get created without data
    if (this.data) {
      this.chart = Chartist[this.chartType](this.element, this.data, this.options, this.responsiveOptions);

      this.bindEvents();

      return this.chart;
    }
  }

  update(newConfig, oldConfig) {
    // Update controller with new configuration
    this.chartType = newConfig.chartType;
    this.data = newConfig.data;
    this.options = newConfig.chartOptions;
    this.responsiveOptions = newConfig.responsiveOptions;
    this.events = newConfig.events;

    // If chart type changed we need to recreate whole chart, otherwise we can update
    if (!this.chart || newConfig.chartType !== oldConfig.chartType) {
      this.renderChart();
    } else {
      if (!angular.equals(newConfig.events, oldConfig.events)) {
        this.unbindEvents(oldConfig.events);
        this.bindEvents();
      }
      this.chart.update(this.data, this.options);
    }
  }
}

const ChartistDirective = () => {
  return {
    restrict: 'EA',
    scope: {
      // mandatory
      data: '=chartistData',
      chartType: '@chartistChartType',
      // optional
      events: '&chartistEvents',
      chartOptions: '&chartistChartOptions',
      responsiveOptions: '&chartistResponsiveOptions'
    },
    controller: ['$scope', '$element', ChartistCtrl]
  };
};

export default ChartistDirective;