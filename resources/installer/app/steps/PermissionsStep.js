import React from 'react';
import fetchData from '../utils/fetch';

export default class PermissionsStep extends React.Component {
  state = {
    folders: [
      { folder: 'storage/framework/', permission: '755', isSet: false },
      { folder: 'storage/logs/', permission: '755', isSet: false },
      { folder: 'bootstrap/cache/', permission: '755', isSet: false },
    ],
  };

  componentDidMount() {
    this.props.setLoading(true);
    fetchData('/api/installer/permissions')
      .then(({ permissions }) => {
        this.setState({
          folders: permissions,
        });
        this.props.setLoading(false);
      })
      .catch(() => this.props.setLoading(false));
  }

  render() {
    const { folders } = this.state;

    return (
      <div>
        <h4>Permissions</h4>
        <p>There are a couple of folders that need to have the correct permissions. Before proceeding to the next step
          please make sure you have 755 on the following folders.
        </p>
        <ul className="list-group">
          {folders.map(folder => (
            <li className="list-group-item d-flex justify-content-between align-items-center" key={folder.folder}>
              {folder.folder}
              {folder.isSet &&
                <span className="badge badge-success badge-pill">{folder.permission}</span>
              }
              {!folder.isSet &&
                <span className="badge badge-danger badge-pill">{folder.permission}</span>
              }
            </li>
          ))
          }
        </ul>
      </div>
    );
  }
}
