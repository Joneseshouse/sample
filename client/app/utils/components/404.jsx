import React from 'react';
import Tools from 'helpers/Tools';
import Link from 'react-router/lib/Link';

class NotFound extends React.Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<div className="row">
				<div className="col-md-6 col-md-offset-3">

					<div className="well">
						<p>
							<strong>
								Page not found (404)
							</strong>
						</p>
						<p>
							Please go back to home page.
						</p>
						<Link to={Tools.toUrl('login')} className="btn btn-success">
							<span className="glyphicon glyphicon-home"></span>&nbsp;
							Home
						</Link>
					</div>
				</div>
			</div>
		);
	}
}

NotFound.propTypes = {
};

NotFound.defaultProps = {
};

export default NotFound;
