import download from 'downloadjs';

const quotesRgx = /["+]/g;

const handleResponse = (response) => {
  const contentDisposition = response.headers.get('Content-Disposition');

  // Check if we need to download a file
  if (contentDisposition && contentDisposition.indexOf('attachment') > -1) {
    return response.blob().then((blob) => {
      const contentType = response.headers.get('content-type');
      const name = contentDisposition.split('filename=').slice(-1)[0] || '';
      
      download(blob, name.replace(quotesRgx, ''), contentType);

      return Promise.resolve({ name });
    });
  }
  
  // For all others try to parse the JSON response
  return parseJsonResponse(response);
}

/**
 * Parse the server response to JSON, for success or failure http responses
 */
const parseJsonResponse = (response) => {
  const json = response.text().then((text) => {
    try {
      return JSON.parse(text);
    } catch (error) {
      // In case the response is not JSON
      return {
        statusCode: response.status,
        ...error,
      };
    }
  });

  if (response.status >= 400) {
    // When the server response contains important JSON data for errors
    return json.then(errors => ({
      data: errors,
      endpoint: response.url,
      status: response.status,
    })).then(Promise.reject.bind(Promise));
  }

  return json;
};

/**
 * This function process the error when the server is down or there's not
 * connectivity available. It also process all other errors, but doesn't do anything special for those.
 */
function handleConnectionErrors(error, { method, url }) {
  return Promise.reject({
    ...error,
    statusCode: error.statusCode || 0,
    method,
    url,
  });
}

// Adding the REST methods to Connection object
const methods = ['delete', 'get', 'post', 'put'];

export default class Connection {
  // default option values
  defaults = {
    host: '/',
    method: 'GET',
    timeout: 20000,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
  };
  // The access token
  token = '';

  constructor(config = {}) {
    methods.forEach((method) => {
      this[method] = (options) => {
        const opts = {
          ...config,
          ...options,
          method: method.toUpperCase(),
        };

        return this.fetchCall(opts);
      };
    });
  }

  fetchCall(options) {
    // Assign the authorization token if available
    const result = {
      ...this.defaults,
      ...options,
      headers: {
        ...this.defaults.headers,
        ...options.headers,
      },
    };

    if (this.token) {
      result.headers.Authorization = `Bearer ${this.token}`;
    } else {
      delete result.headers.Authorization;
    }

    // Stringify params
    if (result.method !== 'POST' && result.method !== 'PUT') {
      if (result.method === 'GET' && result.data) {
        const params = Object.keys(result.data).map(name => `${name}=${result.data[name]}`);
        result.url = `${options.url}${params.length > 0 ? `?${params.join('&')}` : ''}`;
      } else {
        result.params = JSON.stringify(result.data);
      }
    } else if (result.form) {
      const form = new FormData();

      Object.keys(result.form).forEach((key) => {
        if (result.form[key] !== undefined) {
          form.append(key, result.form[key]);
        }
      });

      delete result.form;
      delete result.data;
      delete result.headers['Content-Type'];

      result.body = form;
    } else {
      result.body = JSON.stringify(result.data);
    }

    const conn = fetch(`${result.host}${result.url}`, result);

    // Handle timeout
    return new Promise((resolve, reject) => {
      const timeoutId = setTimeout(() => {
        reject({
          success: false,
          timeout: true,
          message: 'Looks like you don\'t have internet connection.',
        });
      }, result.timeout);

      conn.then(
        (res) => {
          clearTimeout(timeoutId);
          resolve(res);
        },
        (err) => {
          clearTimeout(timeoutId);
          reject(err);
        },
      );
    })
      .then(handleResponse)
      .catch(error => handleConnectionErrors(error, { method: result.method, url: options.url }));
  }

  getToken() {
    return this.token;
  }

  setToken(tkn) {
    this.token = tkn;
  }

  clearToken() {
    this.token = undefined;
  }
}