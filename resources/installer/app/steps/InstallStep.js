import React from 'react';
import ModalWindow from '../components/ModalWindow';
import fetchData from '../utils/fetch';

export default class InstallStep extends React.Component {

  state = {
    env: 'pending',
    appkey: 'pending',
    jwtkey: 'pending',
    geoip: 'pending',
    storagelink: 'pending',
    migrations: 'pending',
    admin: 'pending',
    configs: 'pending',
    lock: 'pending',
    completed: false,
    errors: {},
    messages: {},
  };

  updateState(field, response) {
    this.setState({
      [field]: response.success ? 'ok' : 'error',
      errors: {
        ...this.state.errors,
        [field]: !response.success,
      },
      messages: {
        ...this.state.messages,
        [field]: response.message,
      },
    });
  }

  onSuccessClick = () => {
    document.location = this.props.env.APP_FRONTEND_URL;
  }

  onInstallClick = () => {
    const { env, text, user } = this.props;
    const body = new FormData();
    const database = new FormData();

    body.append('ENV_CONTENT', text);

    database.append('DB_HOST', env.DB_HOST);
    database.append('DB_PORT', env.DB_PORT);
    database.append('DB_DATABASE', env.DB_DATABASE);
    database.append('DB_USERNAME', env.DB_USERNAME);
    database.append('DB_PASSWORD', env.DB_PASSWORD || '');

    this.props.setLoading(true);
    fetchData('/api/installer/environment', {
      method: 'POST',
      body,
    })
      .then((response) => {
        this.updateState('env', response);
        
        return response.success
          ? Promise.resolve()
          : Promise.reject(response);
      })
      .then(() => fetchData('/api/installer/appkey'))
      .then((response) => {
        this.updateState('appkey', response);

        return response.success
          ? Promise.resolve()
          : Promise.reject(response);
      })
      .then(() => fetchData('/api/installer/jwtkey'))
      .then((response) => {
        this.updateState('jwtkey', response);

        return response.success
          ? Promise.resolve()
          : Promise.reject(response);
      })
      .then(() => fetchData('/api/installer/geoip'))
      .then((response) => {
        this.updateState('geoip', response);

        return response.success
          ? Promise.resolve()
          : Promise.reject(response);
      })
      .then(() => fetchData('/api/installer/link'))
      .then((response) => {
        this.updateState('storagelink', response);

        return response.success
          ? Promise.resolve()
          : Promise.reject(response);
      })
      .then(() => fetchData('/api/installer/migrate', {
        method: 'POST',
        body: database,
      }))
      .then((response) => {
        this.updateState('migrations', response);

        return response.success
          ? Promise.resolve()
          : Promise.reject(response);
      })
      .then(() => {
        if (user.ignore) {
          return Promise.resolve({ success: true });
        }
        
        return fetchData('/api/installer/admin', {
          method: 'POST',
          body: JSON.stringify(user),
          headers: {
            'content-type': 'application/json'
          },
        });
      })
      .then((response) => {
        this.updateState('admin', response);
        
        return response.success
          ? Promise.resolve()
          : Promise.reject(response);
      })
      .then(() => fetchData('/api/installer/config', {
        method: 'post',
        body: JSON.stringify({
          configurations: [
            { key: 'APP_TITLE', value: env.APP_TITLE },
            { key: 'APP_FACEBOOK_ENABLED', value: !!env.FACEBOOK_APP_ID },
            { key: 'APP_FACEBOOK_APP_ID', value: env.FACEBOOK_APP_ID ? env.FACEBOOK_APP_ID : false },
            { key: 'APP_IS_NEW_USER_AUTHOR', value: env.APP_IS_NEW_USER_AUTHOR },
          ],
        }),
        headers: {
          'content-type': 'application/json',
        },
      }))
      .then((response) => {
        this.updateState('configs', response);

        return response.success
          ? Promise.resolve()
          : Promise.reject(response);
      })
      .then(() => fetchData('/api/installer/remove', {
        method: 'delete',
      }))
      .then((response) => {
        this.updateState('lock', response);
        this.setState({
          completed: true,
        });
        this.props.setLoading(false);
      })
      .catch((response) => {
        console.log(response);
        this.props.setLoading(false);
      });
  }

  renderAlert(field) {
    const { messages, errors } = this.state;

    if (messages[field]) {
      const cls = errors[field] ? 'alert alert-danger' : 'alert alert-success';

      return (
        <div className={cls} role="alert">
          {messages[field]}
        </div>
      );
    }

    return undefined;
  }

  renderStepStatus(field, text) {
    const status = this.state[field];
    return (
      <div className="final-installer-step">
        <div className="final-installer-status">
          <p>{text}</p>
          {status === 'pending' &&
            <span className="badge badge-success badge-warning">PENDING</span>
          }
          {status === 'ok' &&
            <span className="badge badge-success badge-pill">OK</span>
          }
          {status === 'error' &&
            <span className="badge badge-success badge-danger">ERROR</span>
          }
        </div>
        <div className="final-installer-response">
          {this.renderAlert(field)}
        </div>
      </div>
    );
  }

  render() {
    const { loading, user } = this.props;

    return (
      <React.Fragment>
        <h4>Final Step</h4>
        <p>Based on the previous configurations you set, it's time to complete the installation!</p>
        <p className="run-installer">
          <button onClick={this.onInstallClick} type="button" className="btn btn-warning btn-lg" disabled={loading}>Run Installer!</button>
        </p>
        <ul className="list-group">
          <li className="list-group-item d-flex justify-content-between align-items-center">
            {this.renderStepStatus('env', 'Create .env file')}
          </li>
          <li className="list-group-item d-flex justify-content-between align-items-center">
            {this.renderStepStatus('appkey', 'Generate new APP_KEY')}
          </li>
          <li className="list-group-item d-flex justify-content-between align-items-center">
            {this.renderStepStatus('jwtkey', 'Generate new JWT_KEY')}
          </li>
          <li className="list-group-item d-flex justify-content-between align-items-center">
            {this.renderStepStatus('geoip', 'Download geoip database')}
          </li>
          <li className="list-group-item d-flex justify-content-between align-items-center">
            {this.renderStepStatus('storagelink', 'Create storage link')}
          </li>
          <li className="list-group-item d-flex justify-content-between align-items-center">
            {this.renderStepStatus('migrations', 'Run migrations')}
          </li>
          { !user.ignore &&
            <li className="list-group-item d-flex justify-content-between align-items-center">
              {this.renderStepStatus('admin', `Create admin user (${user.email})`)}
            </li>
          }
          <li className="list-group-item d-flex justify-content-between align-items-center">
            {this.renderStepStatus('configs', 'Setup initial configurations')}
          </li>
          <li className="list-group-item d-flex justify-content-between align-items-center">
            {this.renderStepStatus('lock', 'Remove the installer')}
          </li>
        </ul>
        {this.state.completed &&
          <ModalWindow
            title="Completed!"
            content="The installation is complete 🎉. You can start uploading new albums and songs!"
            successLabel="Go to home"
            onSuccessClick={this.onSuccessClick}
          />
        }
      </React.Fragment>
    );
  }
}
