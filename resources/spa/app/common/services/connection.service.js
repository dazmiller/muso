import Connection from '../utils/connection';
import { noop } from '../utils/fn';

const ConnectionService = ($auth, $rootScope, $mdToast, $translate, $timeout) => {
  const connection = new Connection({
    host: `${process.env.SERVER_API_URL}/${process.env.API_VERSION}`,
  });

  // Setting the token if available
  connection.setToken($auth.getToken());

  function showErrorMessage(messages) {
    $mdToast.show({
      template: ['<md-toast class="md-toast error"><i class="flaticon-letterx5"></i> ', messages.join('<br/>'), '</md-toast>'].join(''),
      hideDelay: 6000,
      position: 'top right'
    });
  }

  function getQueryParams(params = {}) {
    return Object.keys(params).map(query => `${query}=${params[query]}`);
  }

  const service = {
    request(method, options) {
      $rootScope.$emit('loading', true);
      return connection[method](options)
        .then((response) => {
          $rootScope.$emit('loading', false);
          // This is required to force the digest
          $timeout(noop, 100);

          if (response.message) {
            $mdToast.show({
              template: ['<md-toast class="md-toast success"><i class="flaticon-checked21"></i> ', response.message, '</md-toast>'].join(''),
              hideDelay: 6000,
              position: 'top right'
            });
          }

          return Promise.resolve(response);
        })
        .catch((response) => {
          let messages = [$translate.instant('ERROR_SOMETHING_WRONG')];
          const { data } = response;

          $rootScope.$emit('loading', false);
          // This is required to force the digest
          $timeout(noop, 50);

          if (data && data.errors) {
            messages = data.errors;
          }

          //validation errors
          if (response.status === 400) {
            if (data.error === "token_not_provided") {
              showErrorMessage([$translate.instant('ERROR_NOT_LOGGED')]);
              return Promise.reject(response.data);
            }
            
            if (response.error === "token_invalid") {
              connection.clearToken();
              return Promise.reject(response.data);
            }

            showErrorMessage(messages);
            return Promise.reject(response.data);
          }

          //authorization errors
          if (response.status === 403) {
            showErrorMessage(messages);

            //@TODO: redirect somewhere else

            return Promise.reject(response.data);
          }

          // 500 error O.o
          if (response.status === 500) {
            showErrorMessage(messages);

            return Promise.reject(response.data);
          }

          return Promise.reject(response);
        });
    },

    setToken(token) {
      connection.setToken(token);
    }
  };

  const methods = ['get', 'post', 'put', 'delete']
  
  methods.forEach((method) => {
    service[method] = (options) => service.request(method, options);
  });

  service.resource = (url) => {
    const result = {};
    let cache = {};

    result.clearCache = () => {
      cache = {};
    };

    result.all = (data = {}) => {
      const params = getQueryParams(data);
      const cacheKey = `${url}-${params.join('-')}`;

      if (cache[cacheKey]) {
        $timeout(noop, 10);
        return Promise.resolve(cache[cacheKey]);
      }

      return service.get({ url, data })
        .then((response) => {
          cache[cacheKey] = response;

          return Promise.resolve(response);
        });
    };

    result.show = (id, data) => {
      const params = getQueryParams(data);
      const full = `${url}/${id}`;
      const cacheKey = `${full}-${params.join('-')}`;

      if (cache[cacheKey]) {
        $timeout(noop, 10);
        return Promise.resolve(cache[cacheKey]);
      }

      return service.get({
          url: full,
          data,
        })
        .then((response) => {
          cache[cacheKey] = response;
          return Promise.resolve(response);
        });
    };

    result.store = (data) => {
      result.clearCache();
      return service.post({
        url,
        data,
      });
    };

    result.update = (data) => {
      result.clearCache();
      return service.put({
        url: `${url}/${data.id}`,
        data,
      });
    };

    result.remove = (data) => {
      result.clearCache();
      return service.delete({
        url: `${url}/${data.id}`,
      });
    };

    return result;
  };

  return service;
}

ConnectionService.$inject = ['$auth', '$rootScope', '$mdToast', '$translate', '$timeout'];

export default ConnectionService;
