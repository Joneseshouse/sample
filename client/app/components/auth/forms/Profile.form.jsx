import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';

import Tools from 'helpers/Tools';
import * as actionCreators from 'app/actions/actionCreators';
import ValidateTools from 'helpers/ValidateTools';
import FormInput from 'utils/components/FormInput';

import { ADMIN_ROLES, FIELD_TYPE } from 'app/constants';

class ProfileForm extends React.Component {
	constructor(props) {
		super(props);
		this._renderForm = this._renderForm.bind(this);
		this._renderForAdmin = this._renderForAdmin.bind(this);
		this._renderForUser = this._renderForUser.bind(this);
	}

	_renderForAdmin(){
		return (
			<div>
				<Field
			    	name="email"
			    	type="text"
			    	component={FormInput}
			    	label={this.props.labels.email}/>
				<Field
					name="first_name"
					type="text"
					component={FormInput}
					label={this.props.labels.first_name}/>
				<Field
					name="last_name"
					type="text" component={FormInput}
					label={this.props.labels.last_name}/>
			</div>
		);
	}

	_renderForUser(){
		return (
			<div>
				<Field
			    	name="email"
			    	type="text"
			    	component={FormInput}
			    	label={this.props.labels.email}/>
				<Field
					name="first_name"
					type="text"
					component={FormInput}
					label={this.props.labels.first_name}/>
				<Field
					name="last_name"
					type="text" component={FormInput}
					label={this.props.labels.last_name}/>
				<Field
					name="company"
					type="text" component={FormInput}
					label={this.props.labels.company}/>
			</div>
		);
	}

	_renderForm(){
		if(Tools.isAdmin){
			return this._renderForAdmin();
		}else{
			return this._renderForUser();
		}
	}

	render() {
		const { handleSubmit, checkSubmit, submitting, error, reset } = this.props;

		return (
			<form
				onSubmit={handleSubmit(checkSubmit)}
				>
				{this._renderForm()}

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
		type: FIELD_TYPE.EMAIL,
		required: true
	},first_name: {
		type: FIELD_TYPE.STRING,
		required: true
	},last_name: {
		type: FIELD_TYPE.STRING,
		required: true
	}
}

const validate = values => {
	return ValidateTools.validateInput(values, rules);
}

function mapStateToProps(state){
	return {
		initialValues: state.authReducer.profile
	}
}

function mapDispatchToProps(dispatch){
	return bindActionCreators(actionCreators, dispatch);
}

// Decorate the form component
/*
ProfileForm = reduxForm({
	form: 'ProfileForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(ProfileForm);

ProfileForm = connect(
	mapStateToProps,
	mapDispatchToProps
)(ProfileForm);
*/

ProfileForm.propTypes = {
};

ProfileForm.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(
	reduxForm({
		form: 'ProfileForm', // a unique name for this form
		enableReinitialize: true,
		validate
	})(ProfileForm)
);

// export default ProfileForm;
