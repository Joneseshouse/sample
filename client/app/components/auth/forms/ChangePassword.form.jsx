import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';

import * as actionCreators from 'app/actions/actionCreators';

import ValidateTools from 'helpers/ValidateTools';


import FormInput from 'utils/components/FormInput';

import { FIELD_TYPE } from 'app/constants';

class ChangePasswordForm extends React.Component {
	constructor(props) {
		super(props);
	}

	render() {
		const { handleSubmit, checkSubmit, submitting, error, reset } = this.props;
		return (
			<form
				onSubmit={handleSubmit(checkSubmit)}
				>

      			<Field
      				name="password"
      				type="password"
      				component={FormInput}
      				label={this.props.labels.password}/>
      			<Field
      				name="newPassword"
      				type="password"
      				component={FormInput}
      				label={this.props.labels.newPassword}/>

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
	password: {
		type: FIELD_TYPE.STRING,
		required: true
	},newPassword: {
		type: FIELD_TYPE.STRING,
		required: true
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

ChangePasswordForm.propTypes = {
};

ChangePasswordForm.defaultProps = {
};

// Decorate the form component
export default reduxForm({
	form: 'ChangePasswordForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(ChangePasswordForm);

// export default ChangePasswordForm;
