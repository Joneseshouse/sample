import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import store from 'app/store';
import * as actionCreators from 'app/actions/actionCreators';

import {labels} from '../_data';
import ValidateTools from 'helpers/ValidateTools';
import FormInput from 'utils/components/FormInput';
import { FIELD_TYPE } from 'app/constants';

import Tools from 'helpers/Tools';

class MainForm extends React.Component {
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
				<Field
      				name="email"
      				type="email"
      				focus={true}
      				component={FormInput}
      				label={this.props.labels.email}/>
      			<Field
      				name="password"
      				type="password"
      				component={FormInput}
      				label={this.props.labels.password}/>
      			<div className="row">
      				<div className="col-md-6">
						<Field
		      				name="last_name"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.last_name}/>
      				</div>
      				<div className="col-md-6">
						<Field
		      				name="first_name"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.first_name}/>
      				</div>
      			</div>

      			<Field
      				name="role_id"
      				type="select"
      				options={store.getState().adminReducer.listRole}
      				onInputChange={value => {}}
      				component={FormInput}
      				label={this.props.labels.role_id}/>

      			<Field
      				name="block_account"
      				type="checkbox"
      				onInputChange={value => {}}
      				component={FormInput}
      				label={this.props.labels.block_account}/>

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

const validate = values => {
	return ValidateTools.validateInput(
		values,
		Tools.getRules(labels.mainForm)
	);
};

function mapStateToProps(state){
	return {
		initialValues: state.adminReducer.obj
	}
}

function mapDispatchToProps(dispatch){
	return bindActionCreators(actionCreators, dispatch);
}

// Decorate the form component
/*
MainForm = reduxForm({
	form: 'AdminMainForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(MainForm);

MainForm = connect(
	mapStateToProps,
	mapDispatchToProps
)(MainForm);
*/

MainForm.propTypes = {
};

MainForm.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(
	reduxForm({
		form: 'AdminMainForm', // a unique name for this form
		enableReinitialize: true,
		validate
	})(MainForm)
);

// export default MainForm;
