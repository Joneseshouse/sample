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
		      				name="purchase_code"
		      				type="text"
		      				focus={true}
		      				component={FormInput}
		      				label={this.props.labels.purchase_code}/>
							</div>
					<div className="col-md-6">
						<Field
		      				name="bill_of_landing_code"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.bill_of_landing_code}/>
					</div>
				</div>

      			<Field
      				name="real_amount"
      				type="float"
      				component={FormInput}
      				label={this.props.labels.real_amount}/>
      			<Field
      				name="note"
      				type="text"
      				component={FormInput}
      				label={this.props.labels.note}/>

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
		initialValues: state.collectBolReducer.obj
	}
}

function mapDispatchToProps(dispatch){
	return bindActionCreators(actionCreators, dispatch);
}


MainForm.propTypes = {
};

MainForm.defaultProps = {
};

// Decorate the form component
const form = reduxForm({
	form: 'CollectBolMainForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(MainForm);

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(form);
