

let GoogleTagManager = ($window) => {
  
  // Adds a new event to the google tag manager
  const push = (data) => {
    if ($window.Configurations.APP_GA_ENABLED) {
      $window.dataLayer.push(data);
    }
  };

  // Tracks a page
  const page = (url) => {
    if ($window.Configurations.APP_GA_ENABLED) {
      $window.gtag('config', Configurations.APP_GA_ID, { page_path: url });
    }
  };

  // tracks an event
  const event = (config) => {
    if ($window.Configurations.APP_GA_ENABLED && config.name) {
      const data = {}

      if (config.category) {
        data.event_category = config.category;
      }

      if (config.label) {
        data.event_label = config.label;
      }

      if (config.value) {
        data.value = config.value;
      }

      $window.gtag('event', config.name, data);
    }
  };
              
  return {
    event,
    page,
    push,
  };
}

GoogleTagManager.$inject = ['$window'];

export default GoogleTagManager;
