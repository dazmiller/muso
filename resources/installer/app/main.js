import React from 'react';
import ReactDOM from 'react-dom';
import TextField from './components/TextField';
import WelcomeStep from './steps/WelcomeStep';
import RequirementsStep from './steps/RequirementsStep';
import PermissionsStep from './steps/PermissionsStep';
import UnsupportedStep from './steps/UnsupportedStep';
import InstallStep from './steps/InstallStep';
import AppConfigStep from './steps/AppConfigStep';
import AdminUserStep from './steps/AdminUserStep';
import fetchData from './utils/fetch';
import { generateEnvContent } from './utils/environment';
import './main.scss';


function AmazonS3Form({ onEnvChange, env }) {
  return (
    <React.Fragment>
      <TextField
        name="AWS_ACCESS_KEY_ID"
        label="Key ID"
        help="The Amazon S3 key"
        value={env.AWS_ACCESS_KEY_ID}
        onChange={onEnvChange}
      />
      <TextField
        name="AWS_SECRET_ACCESS_KEY"
        label="Secret Key"
        help="The Amazon S3 secret key"
        value={env.AWS_SECRET_ACCESS_KEY}
        onChange={onEnvChange}
      />
      <TextField
        name="AWS_BUCKET"
        label="Bucket"
        help="The Amazon S3 bucket to store the files"
        value={env.AWS_BUCKET}
        onChange={onEnvChange}
      />
      <TextField
        name="AWS_DEFAULT_REGION"
        label="Region"
        help="The Amazon S3 region"
        value={env.AWS_DEFAULT_REGION}
        onChange={onEnvChange}
      />
    </React.Fragment>
  );
}

function EnvironmentStep(props) {
  const { onEnvChange, env } = props;
  return (
    <React.Fragment>
      <h4>Environment</h4>
      <p>General configurations.</p>
      <form>
        <div className="form-group">
          <label htmlFor="APP_NAME">App Name</label>
          <input type="text" className="form-control" id="APP_NAME" name="APP_NAME" placeholder="MusicApp" onChange={onEnvChange} value={env.APP_NAME} />
          <small className="form-text text-muted">
            The internal name of this app, don't use blank spaces, just a single word. (Users won't see this)
          </small>
        </div>
        
        <div className="form-group">
          <label htmlFor="APP_URL">API URL</label>
          <input type="text" className="form-control" name="APP_URL" id="APP_URL" onChange={onEnvChange} value={env.APP_URL} placeholder="https://api.example.com" />
          <small className="form-text text-muted">
            The domain where the Laravel server is running.
          </small>
        </div>

        <div className="form-group">
          <label htmlFor="APP_FRONTEND_URL">Frontend URL</label>
          <input type="text" className="form-control" name="APP_FRONTEND_URL" id="APP_FRONTEND_URL" onChange={onEnvChange} value={env.APP_FRONTEND_URL} placeholder="https://example.com" />
          <small className="form-text text-muted">
            The domain where the client is running. (Client and Server can run in the same domain). <strong>For development use: http://localhost:8080</strong>
          </small>
        </div>


        <div className="form-group">
          <label>App Environment</label>
          <div>
            <div className="form-check form-check-inline">
              <input className="form-check-input" type="radio" name="APP_ENV" id="app-env-local" value="local" onChange={onEnvChange} checked={env.APP_ENV === 'local'}  />
              <label className="form-check-label" htmlFor="app-env-local">local</label>
            </div>
            <div className="form-check form-check-inline">
              <input className="form-check-input" type="radio" name="APP_ENV" id="app-env-prod" value="production" onChange={onEnvChange} checked={env.APP_ENV === 'production'} />
                <label className="form-check-label" htmlFor="app-env-prod">production</label>
            </div>
          </div>
        </div>


        <div className="form-group">
          <label>App Debug</label>
          <div>
            <div className="form-check form-check-inline">
              <input className="form-check-input" type="radio" name="APP_DEBUG" id="app-debug-yes" value="true" onChange={onEnvChange} checked={env.APP_DEBUG === true} />
              <label className="form-check-label" htmlFor="app-debug-yes">yes</label>
            </div>
            <div className="form-check form-check-inline">
              <input className="form-check-input" type="radio" name="APP_DEBUG" id="app-debug-no" value="false" onChange={onEnvChange} checked={env.APP_DEBUG === false} />
              <label className="form-check-label" htmlFor="app-debug-no">no</label>
            </div>
          </div>
        </div>
        
        <div className="form-group">
          <label>Filesystem Driver</label>
          <div>
            <div className="form-check form-check-inline">
              <input className="form-check-input" type="radio" name="FILESYSTEM_DRIVER" id="app-file-local" value="local" onChange={onEnvChange} checked={env.FILESYSTEM_DRIVER === 'local'} />
              <label className="form-check-label" htmlFor="app-file-local">Local Disk</label>
            </div>
            <div className="form-check form-check-inline">
              <input className="form-check-input" type="radio" name="FILESYSTEM_DRIVER" id="app-file-s3" value="s3" onChange={onEnvChange} checked={env.FILESYSTEM_DRIVER === 's3'} />
              <label className="form-check-label" htmlFor="app-file-s3">Amazon S3</label>
            </div>
          </div>
        </div>

        { env.FILESYSTEM_DRIVER === 's3' &&
          <AmazonS3Form {...props} />
        }
      </form>
    </React.Fragment>
  );
}

class DatabaseStep extends React.Component {
  state = {
    status: {},
  };

  testConnection = () => {
    const body = new FormData();
    const fields = [
      'DB_HOST',
      'DB_PORT',
      'DB_DATABASE',
      'DB_USERNAME',
      'DB_PASSWORD',
    ];
    fields.forEach((key) => {
      body.append(key, this.props.env[key]);
    })

    this.props.setLoading(true);
    fetch('/api/installer/database', {
      method: 'POST',
      body,
    })
      .then((response) => response.json())
      .then((status) => {
        this.setState({
          status,
        });
        this.props.setLoading(false);
      })
      .catch(() => this.props.setLoading(false));
  }

  render() {
    const { status } = this.state;
    const { onEnvChange, env } = this.props;

    return (
      <div>
        <h4>Database</h4>
        <p>Configurations for MySQL server.</p>
        <form>
          { status.success &&
            <div className="alert alert-success" role="alert">
              {status.message}
            </div>
          }
          {status.success === false &&
            <div className="alert alert-danger" role="alert">
              {status.message}
            </div>
          }
          <div className="form-group">
            <label htmlFor="DB_HOST">Host</label>
            <input type="text" className="form-control" id="DB_HOST" name="DB_HOST" value={env.DB_HOST} onChange={onEnvChange} placeholder="127.0.0.1" />
            <small className="form-text text-muted">
              The host where the database server is located.
            </small>
          </div>

          <div className="form-group">
            <label htmlFor="DB_PORT">Port</label>
            <input type="text" className="form-control" id="DB_PORT" name="DB_PORT" value={env.DB_PORT} onChange={onEnvChange} placeholder="3306" />
            <small className="form-text text-muted">
              The port used to connect to the database server
            </small>
          </div>

          <div className="form-group">
            <label htmlFor="DB_DATABASE">Database Name</label>
            <input type="text" className="form-control" id="DB_DATABASE" name="DB_DATABASE" value={env.DB_DATABASE} onChange={onEnvChange} />
            <small className="form-text text-muted">
              The name of the database to use for this app.
            </small>
          </div>

          <div className="form-group">
            <label htmlFor="DB_USERNAME">User Name</label>
            <input type="text" className="form-control" id="DB_USERNAME" name="DB_USERNAME" value={env.DB_USERNAME} onChange={onEnvChange} />
            <small className="form-text text-muted">
              Username to connect to the database server.
            </small>
          </div>

          <div className="form-group">
            <label htmlFor="DB_PASSWORD">Password</label>
            <input type="text" className="form-control" id="DB_PASSWORD" name="DB_PASSWORD" value={env.DB_PASSWORD} onChange={onEnvChange} />
            <small className="form-text text-muted">
              Password to connect to the database server.
            </small>
          </div>
        </form>
        <button onClick={this.testConnection} className="btn btn-dark">Test Connection</button>
      </div>
    );
  }
}

function DriverSelect({ onEnvChange, env }) {
  return (
    <div className="form-group">
      <label htmlFor="MAIL_DRIVER">Email Driver</label>
      <select id="MAIL_DRIVER" className="form-control" onChange={onEnvChange} value={env.MAIL_DRIVER} name="MAIL_DRIVER">
        <option value="smtp">SMTP</option>
        <option value="mailgun">Mailgun</option>
        <option value="ses">Amazon SES</option>
        <option value="sparkpost">Sparkpost</option>
      </select>
      <small className="form-text text-muted">
        The driver to use when sending emails.
        </small>
    </div>
  );
}

function SparkpostStep({ onEnvChange, env }) {
  return (
    <form>
      <h4>Sparkpost</h4>
      <p>These configuration are neded to send email to your users, if you don't set
        anything here email won't work.
      </p>

      <DriverSelect onEnvChange={onEnvChange} env={env} />

      <div className="form-group">
        <label htmlFor="SPARKPOST_SECRET">Secret Key</label>
        <input type="text" className="form-control" id="SPARKPOST_SECRET" name="SPARKPOST_SECRET" value={env.SPARKPOST_SECRET} onChange={onEnvChange} />
        <small className="form-text text-muted">
          Your sparkpost secret key.
        </small>
      </div>
    </form>
  );
}

function SESStep({ onEnvChange, env }) {
  return (
    <form>
      <h4>Amazon Simple Email Service</h4>
      <p>These configuration are neded to send email to your users, if you don't set
        anything here email won't work.
      </p>

      <DriverSelect onEnvChange={onEnvChange} env={env} />

      <div className="form-group">
        <label htmlFor="SES_KEY">Key</label>
        <input type="text" className="form-control" id="SES_KEY" name="SES_KEY" value={env.SES_KEY} onChange={onEnvChange} />
        <small className="form-text text-muted">
          Your Amazon SES key.
        </small>
      </div>

      <div className="form-group">
        <label htmlFor="SES_SECRET">Secret Key</label>
        <input type="text" className="form-control" id="SES_SECRET" name="SES_SECRET" value={env.SES_SECRET} onChange={onEnvChange} />
        <small className="form-text text-muted">
          Your Amazon SES secret key.
        </small>
      </div>
    </form>
  );
}

function MailgunStep({ onEnvChange, env }) {
  return (
    <form>
      <h4>Mailgun</h4>
      <p>These configuration are neded to send email to your users, if you don't set
        anything here email won't work.
      </p>

      <DriverSelect onEnvChange={onEnvChange} env={env} />

      <div className="form-group">
        <label htmlFor="MAILGUN_DOMAIN">Domain</label>
        <input type="text" className="form-control" id="MAILGUN_DOMAIN" name="MAILGUN_DOMAIN" value={env.MAILGUN_DOMAIN} onChange={onEnvChange} />
        <small className="form-text text-muted">
          The domain you registered in mailgun.
        </small>
      </div>

      <div className="form-group">
        <label htmlFor="MAILGUN_SECRET">Secret Key</label>
        <input type="text" className="form-control" id="MAILGUN_SECRET" name="MAILGUN_SECRET" value={env.MAILGUN_SECRET} onChange={onEnvChange} />
        <small className="form-text text-muted">
          Your mailgun secret key.
        </small>
      </div>
    </form>
  );
}

function SMTPStep({ onEnvChange, env }) {
  return (
    <form>
      <h4>SMTP</h4>
      <p>These configuration are neded to send email to your users, if you don't set
        anything here email won't work.
      </p>

      <DriverSelect onEnvChange={onEnvChange} env={env} />

      <div className="form-group">
        <label htmlFor="MAIL_HOST">SMTP Host</label>
        <input type="text" className="form-control" id="MAIL_HOST" name="MAIL_HOST" placeholder="127.0.0.1" value={env.MAIL_HOST} onChange={onEnvChange} />
        <small className="form-text text-muted">
          The host where the SMTP server is located
        </small>
      </div>

      <div className="form-group">
        <label htmlFor="MAIL_PORT">SMTP Port</label>
        <input type="text" className="form-control" id="MAIL_PORT" name="MAIL_PORT" placeholder="1025" value={env.MAIL_PORT} onChange={onEnvChange} />
        <small className="form-text text-muted">
          The port to connect to the SMTP server.
        </small>
      </div>

      <div className="form-group">
        <label htmlFor="MAIL_USERNAME">SMTP User Name</label>
        <input type="text" className="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME" value={env.MAIL_USERNAME} onChange={onEnvChange} />
        <small className="form-text text-muted">
          The username to connect to the SMTP server.
        </small>
      </div>

      <div className="form-group">
        <label htmlFor="MAIL_PASSWORD">SMTP Password</label>
        <input type="text" className="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD" value={env.MAIL_PASSWORD} onChange={onEnvChange} />
        <small className="form-text text-muted">
        The password to connect to the SMTP server.
        </small>
      </div>
      
      <div className="form-group">
        <label htmlFor="MAIL_ENCRYPTION">SMTP Encription</label>
        <input type="text" className="form-control" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION" value={env.MAIL_ENCRYPTION} onChange={onEnvChange} />
        <small className="form-text text-muted">
        The encription used by the SMTP server.
        </small>
      </div>
    </form>
  );
}

function EmailStep(props) {
  if (props.env.MAIL_DRIVER === 'mailgun') {
    return <MailgunStep {...props} />;
  }

  if (props.env.MAIL_DRIVER === 'ses') {
    return <SESStep {...props} />;
  }
  
  if (props.env.MAIL_DRIVER === 'sparkpost') {
    return <SparkpostStep {...props} />;
  }

  return <SMTPStep {...props} />;
}

function EmailSenderStep({ onEnvChange, env }) {
  return (
    <form>
      <h4>Email sender</h4>
      <p>When sending email, we need to set the name/email of who's sending, please define those values here.</p>

      <div className="form-group">
        <label htmlFor="MAIL_FROM_ADDRESS">Email From</label>
        <input type="text" className="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS" placeholder="contact@example.com" value={env.MAIL_FROM_ADDRESS} onChange={onEnvChange} />
        <small className="form-text text-muted">
          The email to set as the sender when sending automated mail to users.
        </small>
      </div>

      <div className="form-group">
        <label htmlFor="MAIL_FROM_NAME">Name From</label>
        <input type="text" className="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME" placeholder="John Doe" value={env.MAIL_FROM_NAME} onChange={onEnvChange} />
        <small className="form-text text-muted">
          The name to set as the sender when sending automated email to users.
        </small>
      </div>

      <div className="form-group">
        <label htmlFor="MAIL_DEFAULT_DOMAIN">Default domain for mails</label>
        <input type="text" className="form-control" id="MAIL_DEFAULT_DOMAIN" name="MAIL_DEFAULT_DOMAIN" placeholder="example.com" value={env.MAIL_DEFAULT_DOMAIN} onChange={onEnvChange} />
        <small className="form-text text-muted">
          Some facebook users disable sharing email, in this case this domain will be used to create fake email address.
        </small>
      </div>
    </form>
  );
}

const steps = [
  WelcomeStep,
  RequirementsStep,
  PermissionsStep,
  EnvironmentStep,
  DatabaseStep,
  EmailStep,
  EmailSenderStep,
  AppConfigStep,
  AdminUserStep,
  InstallStep,
];

class Installer extends React.Component {
  state = {
    step: 0,
    loading: false,
    env: getDefaultEnv(),
    text: generateEnvContent(getDefaultEnv()),
    user: {
      ignore: false,
      name: 'Administrator',
      email: `admin@${window.location.hostname}`,
      password: 'admin123',
    },
  };

  onPrevClick = () => {
    this.setState((prev) => ({
      step: prev.step - 1,
    }));
  };
  
  onNextClick = () => {
    this.setState((prev) => ({
      step: prev.step + 1,
    }));
  };

  onEnvChange = (event) => {
    let { name, value } = event.target;

    if (name === 'text') {
      // Users can edit final result
      this.setState({
        text: value,
      });
    } else {
      // For bolean values
      if (value === 'true' || value === 'false') {
        value = value === 'true';
      }

      const env = {
        ...this.state.env,
        [name]: value,
      };
      const text = generateEnvContent(env);

      // if APP_URL, we need to update SERVER_API_URL as well
      if (name === 'APP_URL') {
        env.SERVER_API_URL = `${value}/api`;
      }

      this.setState({
        env,
        text,
      });
    }
  }

  onUserChange = (event) => {
    const { name, value } = event.target;

    this.setState({
      user: {
        ...this.state.user,
        [name]: value,
      },
    });
  }

  setLoading = (loading) => {
    this.setState({
      loading,
    });
  }

  render() {
    const { env, step, text, loading, user } = this.state;
    const { supported, php } = window.env;
    const current = step;
    const width = 100 * current / (steps.length - 1);
    const css = ['progress-bar',' progress-bar-striped'];
    let StepComponent = steps[step];

    if (!supported) {
      StepComponent = UnsupportedStep;
    }

    if (loading) {
      css.push('progress-bar-animated');
    }

    return (
      <div>
        <h1>Installer</h1>
        <div className="progress">
          {supported &&
            <div className={css.join(' ')} role="progressbar" style={{ width: width + '%' }}></div>
          }
        </div>
        <div className="main-content">
          <StepComponent
            onEnvChange={this.onEnvChange}
            onUserChange={this.onUserChange}
            env={env} text={text}
            setLoading={this.setLoading}
            loading={loading}
            user={user}
            php={php}
          />
        </div>
        <div className="footer-bar">
          { supported && step > 0 &&
            <button onClick={this.onPrevClick} type="button" className="btn btn-light">Prev</button>
          }
          {supported && (step < (steps.length - 1)) &&
            <button onClick={this.onNextClick} type="button" className="btn btn-primary">Next</button>
          }
        </div>
      </div>
    );
  }
}

ReactDOM.render(
  <Installer />,
  document.getElementById('root')
);

function getDefaultEnv() {
  return {
    APP_NAME: 'MusicApp',
    APP_ENV: 'production',
    APP_KEY: 'base64:Y7deEy4wa++pZDEbsJT3UZaPaROFtWO/eaD5RnsGHXs=',
    APP_DEBUG: false,
    APP_URL: window.location.origin,
    APP_FRONTEND_URL: window.location.origin,
    APP_IS_NEW_USER_AUTHOR: true,

    FILESYSTEM_DRIVER: 'local',
    FILESYSTEM_CLOUD: 's3',

    LOG_CHANNEL: 'stack',

    DB_CONNECTION: 'mysql',
    DB_PORT: 3306,
    DB_HOST: '127.0.0.1',
    DB_DATABASE: '',
    DB_USERNAME: '',
    DB_PASSWORD: '',


    BROADCAST_DRIVER: 'log',
    CACHE_DRIVER: 'file',
    SESSION_DRIVER: 'file',
    SESSION_LIFETIME: 120,
    QUEUE_DRIVER: 'sync',

    REDIS_HOST: '127.0.0.1',
    REDIS_PASSWORD: '',
    REDIS_PORT: 6379,

    MAIL_DEFAULT_DOMAIN: window.location.hostname,
    MAIL_FROM_ADDRESS: `contact@${window.location.hostname}`,
    MAIL_FROM_NAME: "John Doe",
    MAIL_DRIVER: 'smtp',
    MAIL_HOST: '127.0.0.1',
    MAIL_PORT: 1025,
    MAIL_USERNAME: '',
    MAIL_PASSWORD: '',
    MAIL_ENCRYPTION: '',

    MAILGUN_DOMAIN: '',
    MAILGUN_SECRET: '',
    
    SPARKPOST_SECRET: '',

    PUSHER_APP_ID: '',
    PUSHER_APP_KEY: '',
    PUSHER_APP_SECRET: '',
    PUSHER_APP_CLUSTER: 'mt1',

    MIX_PUSHER_APP_KEY: "${PUSHER_APP_KEY}",
    MIX_PUSHER_APP_CLUSTER: "${PUSHER_APP_CLUSTER}",

    JWT_SECRET: '',
    JWT_FACEBOOK_SECRET: '',
    JWT_TWITTER_KEY: '',
    JWT_TWITTER_SECRET: '',

    AWS_ACCESS_KEY_ID: '',
    AWS_SECRET_ACCESS_KEY: '',
    AWS_BUCKET: '',
    AWS_DEFAULT_REGION: 'us-east-1',

    APP_TITLE: "Music App",
    SERVER_API_URL: window.location.origin + '/api',
    API_VERSION: 'v1',
    DEFAULT_LOCALE: 'en',
    DEFAULT_ROUTE: '/public/discover',
    FACEBOOK_APP_ID: '',
  };
}
