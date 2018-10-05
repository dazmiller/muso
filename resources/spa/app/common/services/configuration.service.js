
let Configuration = (Connection) => {
  const service = Connection.resource('/configurations');

  service.applyTheme = (css) => {
    return Connection.post({
      url: '/configurations/theme/apply',
      data: {
        css,
      },
    });
  };
  
  service.clearTheme = () => {
    return Connection.post({
      url: '/configurations/theme/clear',
    });
  };

  return service;
}

Configuration.$inject = ['Connection'];

export default Configuration;
