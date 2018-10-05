import React from 'react';
import TextField from '../components/TextField';

export default function AppConfigStep({ onEnvChange, env }) {
  return (
    <React.Fragment>
      <h4>App Configurations</h4>
      <form>
        <TextField
          name="APP_TITLE"
          label="App Title"
          help="This is the text in the main header"
          value={env.APP_TITLE}
          onChange={onEnvChange}
        />
        <TextField
          name="FACEBOOK_APP_ID"
          label="Facebook App ID"
          help="To enable login with facebook"
          value={env.FACEBOOK_APP_ID}
          onChange={onEnvChange}
        />
        <TextField
          name="JWT_FACEBOOK_SECRET"
          label="Facebook Secret Key"
          help="Facebook secret key for your app"
          value={env.JWT_FACEBOOK_SECRET}
          onChange={onEnvChange}
        />

        <div className="form-group">
          <label>Allow new users to create albums and songs?</label>
          <div>
            <div className="form-check form-check-inline">
              <input className="form-check-input" type="radio" name="APP_IS_NEW_USER_AUTHOR" id="app-debug-yes" value="true" onChange={onEnvChange} checked={env.APP_IS_NEW_USER_AUTHOR === true} />
              <label className="form-check-label" htmlFor="app-debug-yes">yes</label>
            </div>
            <div className="form-check form-check-inline">
              <input className="form-check-input" type="radio" name="APP_IS_NEW_USER_AUTHOR" id="app-debug-no" value="false" onChange={onEnvChange} checked={env.APP_IS_NEW_USER_AUTHOR === false} />
              <label className="form-check-label" htmlFor="app-debug-no">no</label>
            </div>
          </div>
        </div>

      </form>
    </React.Fragment>
  );
}
