import React from 'react';
import Checkbox from '../components/Checkbox';
import TextField from '../components/TextField';

export default function AdminUserStep({ onUserChange, user }) {
  return (
    <React.Fragment>
      <h4>Admin User</h4>
      <p>Please create the admin user. If you already have one created you might not want to create one.</p>
      <Checkbox
        label="Don't create an admin."
        onChange={() => onUserChange({ target: { name: 'ignore', value: !user.ignore } })}
        checked={user.ignore}
      />
      {!user.ignore &&
        <form>
          <TextField
            name="name"
            label="Name"
            help="Your public name, you can change it later."
            value={user.name}
            onChange={onUserChange}
          />
          <TextField
            name="email"
            label="Email"
            help="This email will be used when login."
            value={user.email}
            onChange={onUserChange}
          />
          <TextField
            name="password"
            label="Password"
            help="The password to login."
            value={user.password}
            onChange={onUserChange}
          />
        </form>
      }
    </React.Fragment>
  );
}
