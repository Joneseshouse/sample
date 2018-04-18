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
		this.state = {
			showSingle: true
		}
	}

	componentDidMount(){
		this.handleInputChange('type', store.getState().categoryReducer.obj.type);
	}

	handleInputChange(key, value){
		if(key === 'type'){
			if(value === 'article'){
				this.setState({showSingle: true});
			}else{
				this.setState({showSingle: false});
			}
		}
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
      				name="type"
      				type="select"
      				options={store.getState().categoryReducer.listType}
      				component={FormInput}
      				onInputChange={value => this.handleInputChange('type', value)}
      				label={this.props.labels.type}/>
      			<Field
      				show={this.state.showSingle}
      				name="single"
      				type="checkbox"
      				component={FormInput}
      				label={this.props.labels.single}/>

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
		initialValues: state.categoryReducer.obj
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
	form: 'CategoryMainForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(MainForm);

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(form);

// export default MainForm;
