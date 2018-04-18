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
      				name="title"
      				type="text"
      				focus={true}
      				component={FormInput}
      				label={this.props.labels.title}/>
      			<Field
      				name="branch"
      				type="text"
      				component={FormInput}
      				label={this.props.labels.branch}/>
      			<div className="row">
      				<div className="col-md-6">
						<Field
		      				name="account_number"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.account_number}/>
      				</div>
      				<div className="col-md-6">
						<Field
		      				name="type"
		      				type="select"
		      				options={store.getState().bankReducer.listType}
		      				component={FormInput}
		      				onInputChange={value => {}}
		      				label={this.props.labels.type}/>
      				</div>
      			</div>

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
		initialValues: state.bankReducer.obj
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
	form: 'BankMainForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(MainForm);

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(form);

// export default MainForm;
