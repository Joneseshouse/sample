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
					<div className="col-md-5">
						<Field
		      				name="selected_year"
		      				type="select"
		      				options={store.getState().bolReportReducer.listYear}
		      				component={FormInput}
		      				showLabel={false}
		      				onInputChange={()=>{}}
		      				label={this.props.labels.selected_year}/>
					</div>
					<div className="col-md-5">
						<Field
		      				name="selected_month"
		      				type="select"
		      				options={store.getState().bolReportReducer.listMonth}
		      				component={FormInput}
		      				showLabel={false}
		      				onInputChange={()=>{}}
		      				label={this.props.labels.selected_month}/>
					</div>
					<div className="col-md-2">
						<button className="btn btn-success btn-block" disabled={submitting}>
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
		initialValues: state.bolReportReducer.initFilter
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
	form: 'BolReportMainForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(MainForm);

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(form);

// export default MainForm;
