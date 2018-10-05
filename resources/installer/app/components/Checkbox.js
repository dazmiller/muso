import React from 'react';

export default function Checkbox({ label, checked, name, onChange, value }) {
  return (
    <div className="form-check" style={{marginBottom: 20}}>
      <input className="form-check-input" type="checkbox" value={value} checked={checked} name={name} id={`${name}-id`} onChange={onChange} />
      <label className="form-check-label" htmlFor={`${name}-id`}>
        {label}
      </label>
    </div>
  );
}
