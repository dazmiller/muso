import React from 'react';

export default function UnsupportedStep({ php }) {
  return (
    <React.Fragment>
      <h4>Unsupported</h4>
      <p>It looks like you have an old version of PHP.</p>
      <p>In order to run this app, you need at least <strong>7.1.0</strong>. But, you currently have <strong>{php.version}</strong>, make sure to update it in order to continue the installation.</p>
    </React.Fragment>
  );
}
