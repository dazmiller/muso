import React from 'react';

export default function ModalWindow({ title, content, successLabel, cancelLabel, onSuccessClick, onCancelClick }) {
  return (
    <div className="modal" tabIndex="-1" role="dialog" style={{ display: 'block', backgroundColor: 'rgba(0,0,0,0.5)' }}>
      <div className="modal-dialog" role="document">
        <div className="modal-content">
          <div className="modal-header">
            <h5 className="modal-title">{title}</h5>
          </div>
          <div className="modal-body">
            <p>{content}</p>
          </div>
          <div className="modal-footer">
            {successLabel &&
              <button type="button" onClick={onSuccessClick} className="btn btn-primary">{successLabel}</button>
            }
            {cancelLabel &&
              <button type="button" onClick={onCancelClick} className="btn btn-secondary" data-dismiss="modal">{cancelLabel}</button>
            }
          </div>
        </div>
      </div>
    </div>
  );
}
