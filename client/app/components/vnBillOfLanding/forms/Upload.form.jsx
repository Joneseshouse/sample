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

class UploadForm extends React.Component {
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
					<div className="col-sm-10">
						<Field
		      				name="list_code"
		      				type="file"
		      				component={FormInput}
		      				label={this.props.labels.list_code}/>
					</div>
					<div className="col-sm-2" style={{paddingTop: 22}}>
						<button className="btn btn-success btn-block" disabled={submitting}>
							<span className="glyphicon glyphicon-ok"></span> &nbsp;
							{this.props.submitTitle}
						</button>
					</div>
				</div>

				{error && <div className="alert alert-danger" role="alert">{error}</div>}
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
		initialValues: state.vnBillOfLandingReducer.obj
	}
}

function mapDispatchToProps(dispatch){
	return bindActionCreators(actionCreators, dispatch);
}

UploadForm.propTypes = {
};

UploadForm.defaultProps = {
};

// Decorate the form component
const form = reduxForm({
	form: 'VnBillOfLandingUploadForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(UploadForm);

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(form);

// export default UploadForm;
