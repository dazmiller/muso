import React from 'react';

export default function TextField({ label, name, onChange, value, placeholder, help }) {
  return (
    <div className="form-group">
      <label htmlFor={`${name}-id`}>{label}</label>
      <input type="text" className="form-control" name={name} id={`${name}-id`} onChange={onChange} value={value} placeholder={placeholder} />
      <small className="form-text text-muted">
        {help}
      </small>
    </div>
  );
}
