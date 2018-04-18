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
				<div className="row">
					<div className="col-md-6">
						<Field
		      				name="admin_id"
		      				type="select"
		      				options={store.getState().userReducer.listAdmin}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.admin_id}/>
					</div>
					<div className="col-md-6">
						<Field
		      				name="dathang_admin_id"
		      				type="select"
		      				options={store.getState().userReducer.listDathangAdmin}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.dathang_admin_id}/>
					</div>
				</div>

				<div className="row">
					<div className="col-md-6">
						<Field
		      				name="email"
		      				type="email"
		      				focus={true}
		      				component={FormInput}
		      				label={this.props.labels.email}/>
					</div>
					<div className="col-md-6">
						<Field
		      				name="password"
		      				type="password"
		      				component={FormInput}
		      				label={this.props.labels.password}/>
					</div>
				</div>

				<Field
						name="phone"
						type="text"
						component={FormInput}
						label={this.props.labels.phone} />

      			<div className="row">
      				<div className="col-md-4">
						<Field
		      				name="last_name"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.last_name}/>
      				</div>
      				<div className="col-md-8">
						<Field
		      				name="first_name"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.first_name}/>
      				</div>
      			</div>

      			<div className="row">
      				<div className="col-md-4">
						<Field
		      				name="area_code_id"
		      				type="select"
		      				options={store.getState().userReducer.listAreaCode}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.area_code_id}/>
      				</div>
      				<div className="col-md-8">
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

      			<div className="row">
      				<div className="col-md-6">
						<Field
		      				name="order_fee_factor"
		      				type="float"
		      				component={FormInput}
		      				label={this.props.labels.order_fee_factor}/>
      				</div>
      				<div className="col-md-6">
						<Field
		      				name="rate"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.rate}/>
      				</div>
      			</div>

      			<div className="row">
      				<div className="col-md-6">
						<Field
		      				name="deposit_factor"
		      				type="float"
		      				component={FormInput}
		      				label={this.props.labels.deposit_factor}/>
      				</div>
      				<div className="col-md-6">
						<Field
		      				name="delivery_fee_unit"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.delivery_fee_unit}/>
      				</div>
      			</div>

      			<Field
      				name="complain_day"
      				type="number"
      				component={FormInput}
      				label={this.props.labels.complain_day}/>

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
		initialValues: state.userReducer.obj
	}
}

function mapDispatchToProps(dispatch){
	return bindActionCreators(actionCreators, dispatch);
}

// Decorate the form component
/*
MainForm = reduxForm({
	form: 'UserMainForm', // a unique name for this form
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
		form: 'UserSignupForm', // a unique name for this form
		enableReinitialize: true,
		validate
	})(MainForm)
);

// export default MainForm;
