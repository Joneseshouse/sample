import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';

import * as actionCreators from 'app/actions/actionCreators';
import ValidateTools from 'helpers/ValidateTools';

import FormInput from 'utils/components/FormInput';

import { FIELD_TYPE } from 'app/constants';

class LoginForm extends React.Component {
	constructor(props) {
		super(props);
	}

	render() {
		const { handleSubmit, checkSubmit, submitting, error, reset } = this.props;
		return (
			<form
				onSubmit={handleSubmit(checkSubmit)}
				>

  				<Field name="email" type="text" component={FormInput} label={this.props.labels.email}/>
  				<Field name="password" type="password" component={FormInput} label={this.props.labels.password}/>

				{error && <div className="alert alert-danger" role="alert">{error}</div>}

				<div className="row custom-modal-footer">
					<div className="col-md-6 cancel">
						{this.props.children}
					</div>
					<div className="col-md-6 submit">
						<button className="btn btn-success" disabled={submitting}>
							<span className="glyphicon glyphicon-ok"></span> &nbsp;
							{this.props.submitTitle}
						</button>
					</div>
				</div>


			</form>
		);
	}
}

const rules = {
	email: {
		type: FIELD_TYPE.STRING,
		required: true,
		min: 3,
		max: 90
	},password: {
		type: FIELD_TYPE.STRING,
		required: true,
		min: 6,
		max: 90
	}
}

const validate = values => {
	return ValidateTools.validateInput(values, rules);
}

function mapStateToProps(state){
	return {
		initialValues: state.authReducer.login
	}
}

function mapDispatchToProps(dispatch){
	return bindActionCreators(actionCreators, dispatch);
}

LoginForm.propTypes = {
};

LoginForm.defaultProps = {
};
/*
// Decorate the form component
LoginForm = reduxForm({
	form: 'LoginForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(LoginForm);

LoginForm = connect(
	mapStateToProps,
	mapDispatchToProps
)(LoginForm);
*/
export default connect(
	mapStateToProps,
	mapDispatchToProps
)(
	reduxForm({
		form: 'LoginForm', // a unique name for this form
		enableReinitialize: true,
		validate
	})(LoginForm)
);

// export default LoginForm;
