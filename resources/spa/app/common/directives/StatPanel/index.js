import './styles.scss';

var StatsPanel = () => {
  return {
    restrict: 'E',
    transclude: true,
    scope: {
      title: '@',
      icon: '@',
      total: '=',
      lastMonth: '=',
      year: '=',
      theme: '@',
    },
    template: `
      <div class="stat-panel {{theme}}">
        <div layout="row" class="stat-panel-info">
          <i class="{{icon}}"></i>
          <div flex>
            <span>{{title}}</span>
            <h3>{{total.toLocaleString()}}</h3>
          </div>
        </div>
        <div layout="row" class="stat-panel-footer">
          <div flex>
            <span>Last Month</span>
            <h5>{{lastMonth.toLocaleString()}}</h5>
          </div>
          <div flex>
            <span>This year</span>
            <h5>{{year.toLocaleString()}}</h5>
          </div>
        </div>
      </div>
    `,
  };
};

StatsPanel.$inject = [];

export default StatsPanel;