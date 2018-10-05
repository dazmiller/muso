import React from 'react';
import fetchData from '../utils/fetch';

export default class RequirementsStep extends React.Component {
  state = {
    php: {
      minimum: '7.1.0',
      current: '',
      supported: false,
    },
    requirements: {
      JSON: false,
      cURL: false,
      mbstring: false,
      openssl: false,
      pdo: false,
      tokenizer: false,
      mysqli: false,
    },
  };

  componentDidMount() {
    this.props.setLoading(true);
    fetchData('/api/installer/requirements')
      .then(({ php, requirements }) => {
        this.setState({
          php,
          requirements: requirements.php,
        });
        this.props.setLoading(false);
      })
      .catch(() => this.props.setLoading(false));
  }

  render() {
    const { php } = this.state;
    const requirements = Object.keys(this.state.requirements).map(label => ({
      label,
      value: this.state.requirements[label],
    }));

    return (
      <div>
        <h4>Requirements</h4>
        <p>Laravel requires a couple of extentions in your PHP setup, please make sure everything is green before proceeding.</p>
        <ul className="list-group">
          <li className="list-group-item d-flex justify-content-between align-items-center">
            PHP >= {php.minimum}
            {php.supported &&
              <span className="badge badge-success badge-pill">{php.current}</span>
            }
            {!php.supported &&
              <span className="badge badge-danger badge-pill">{php.current}</span>
            }
          </li>
          {
            requirements.map(requirement => (
              <li className="list-group-item d-flex justify-content-between align-items-center" key={requirement.label}>
                {requirement.label}
                {requirement.value &&
                  <span className="badge badge-success badge-pill">YES</span>
                }
                {!requirement.value &&
                  <span className="badge badge-danger badge-pill">NO</span>
                }
              </li>
            ))
          }
        </ul>
      </div>
    );
  }
}
