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
					<div className="col-md-3">
						<Field
		      				name="code"
		      				type="text"
		      				focus={true}
		      				component={FormInput}
		      				label={this.props.labels.code}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="address_uid"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.address_uid}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="sub_fee"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.sub_fee}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="packages"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.packages}/>
					</div>
				</div>

      			<div className="row">
      				<div className="col-md-3">
						<Field
		      				name="input_mass"
		      				type="float"
		      				component={FormInput}
		      				label={this.props.labels.input_mass}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="length"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.length}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="width"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.width}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="height"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.height}/>
					</div>
				</div>

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
		initialValues: state.cnBillOfLandingReducer.obj
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
	form: 'CnBillOfLandingMainForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(MainForm);

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(form);

// export default MainForm;
