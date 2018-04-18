import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import Link from 'react-router/lib/Link';

import * as actionCreators from 'app/actions/actionCreators';
import Tools from 'helpers/Tools';
import {apiUrls, labels} from './_data';


class PasswordConfirm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			confirmSuccess: true,
			confirmMessage: 'Please waiting...',
			type: null
	    };
	}

	componentDidMount(){
		let endPoint = null;
		if(this.props.params.type === 'change'){
			endPoint = apiUrls.changePasswordConfirm;
			this.setState({type: 'Change'});
		}else if(this.props.params.type === 'reset'){
			endPoint = apiUrls.resetPasswordConfirm;
			this.setState({type: 'Reset'});
		}
		try{
			const params = {
				token: this.props.params.token
			};
			Tools.apiCall(endPoint, params).then((result) => {
		    	this.setState({confirmMessage: result.message.common?result.message.common:result.message});
		    });
	    }catch(error){
	    	console.error(error);
		}
	}

	render() {
		return (
			<div className="row">
				<div className="col-md-6 col-md-offset-3">
					<div className="well">
						<p>
							<strong>
								{`${this.state.type} Password Confirm`}
							</strong>
						</p>
						<p>
							{this.state.confirmMessage}
						</p>
						<Link to={Tools.toUrl('login')} className="btn btn-success">
							<span className="glyphicon glyphicon-user"></span>&nbsp;
							Login
						</Link>
					</div>
				</div>
			</div>
		);
	}
}

function mapStateToProps(state){
	return {
	}
}

function mapDispatchToProps(dispatch){
	return {
		...bindActionCreators(actionCreators, dispatch)
	};
}

PasswordConfirm.propTypes = {
};

PasswordConfirm.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(PasswordConfirm);

// export default PasswordConfirm;
