import './styles.scss';

var UsersWidget = () => {
  return {
    restrict: 'E',
    template: [
      '<div class="users-widget white-panel">',
        '<h3>{{title}}</h3>',
        '<div>',
          '<img ',
            'ng-repeat="user in users" ',
            'ng-src="{{user.image}}" ',
            'ui-sref="members.profiles({ id: user.id })" ',
            'alt="{{user.name}}" ',
            'title="{{user.name}}" ',
            'class="users-widget-user" ',
          '/>',
        '</div>',
      '</div>',
    ].join(''),
    scope: {
      title: '=',
      users: '=',
    },
  };
};

UsersWidget.$inject = [];

export default UsersWidget;