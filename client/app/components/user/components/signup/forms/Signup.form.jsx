import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import store from 'app/store';
import * as actionCreators from 'app/actions/actionCreators';

import {labels} from '../../..//_data';
import ValidateTools from 'helpers/ValidateTools';
import FormInput from 'utils/components/FormInput';
import { FIELD_TYPE } from 'app/constants';

import Tools from 'helpers/Tools';

class SignupForm extends React.Component {
	constructor(props) {
		super(props);
		this.state = {}
	}

	componentDidMount(){
	}

	render() {
		const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
		return (
			<form
				onSubmit={handleSubmit(onSubmit)}>
				<div className="row">
					<div className="col-xs-4">
						<Field
		      				name="email"
		      				type="email"
		      				focus={true}
		      				component={FormInput}
		      				label={this.props.labels.email}/>
					</div>
					<div className="col-xs-8">
						<Field
		      				name="phone"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.phone}/>
					</div>
				</div>
				<div className="row">
					<div className="col-xs-4">
						<Field
		      				name="uid"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.uid}/>
					</div>
					<div className="col-xs-8">
						<Field
		      				name="password"
		      				type="password"
		      				component={FormInput}
		      				label={this.props.labels.password}/>
					</div>
				</div>


      			<div className="row">
      				<div className="col-xs-4">
						<Field
		      				name="last_name"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.last_name}/>
      				</div>
      				<div className="col-xs-8">
						<Field
		      				name="first_name"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.first_name}/>
      				</div>
      			</div>

      			<div className="row">
      				<div className="col-xs-4">
						<Field
		      				name="area_code_id"
		      				type="select"
		      				options={store.getState().userReducer.listAreaCode}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.area_code_id}/>
      				</div>
      				<div className="col-xs-8">
						<Field
		      				name="address"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.address}/>
      				</div>
      			</div>

      			<Field
      				name="company"
      				type="text"
      				component={FormInput}
      				label={this.props.labels.company}/>

      			<hr/>

				{error && <div className="alert alert-danger" role="alert">{error}</div>}

				<div className="row custom-modal-footer">
					<div className="col-xs-6 cancel">
						{this.props.children}
					</div>
					<div className="col-xs-6 submit">
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

const validate = values => {
	return ValidateTools.validateInput(
		values,
		Tools.getRules(labels.mainForm)
	);
};

function mapStateToProps(state){
	return {
		initialValues: state.userReducer.obj
	}
}

function mapDispatchToProps(dispatch){
	return bindActionCreators(actionCreators, dispatch);
}

SignupForm.propTypes = {
};

SignupForm.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(
	reduxForm({
		form: 'UserSignupForm', // a unique name for this form
		enableReinitialize: true,
		validate
	})(SignupForm)
);

