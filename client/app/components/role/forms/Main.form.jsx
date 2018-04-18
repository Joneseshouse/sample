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

	_renderCheckbox(actions, i){
		return actions.map((action, j) => {
			return (
				<div className="col-md-3" key={j}>
					<div className="checkbox">
						<label>
							<input
								type="checkbox"
								checked={action.allow}
								onChange={event => this.props.onCheckPermission(i, j)}
								/> {action.title}
						</label>
					</div>
				</div>
			);
		});
	}

	_renderModule(){
		var modules = this.props.routeData.map((module, i) => {
			return(
				<div key={i}>
					<div>
						<strong>
							{module.title} &nbsp;
							<span
								onClick={() => this.props.onTogglePermissions(i)}
								className="glyphicon glyphicon-check green pointer"></span>
						</strong>
					</div>
					<div className="row">
						{this._renderCheckbox(module.actions, i)}
					</div>
					<hr/>
				</div>
			);
		});
		return modules;
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
      				name="default_role"
      				type="checkbox"
      				component={FormInput}
      				label={this.props.labels.default_role}/>

      			<hr/>

      			<p>
      				<strong>
	      				Chọn tất cả
      				</strong> &nbsp;
					<span
						onClick={() => this.props.onTogglePermissions()}
						className="glyphicon glyphicon-check green pointer"></span>
      			</p>
				<br/>

      			{this._renderModule()}

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
		initialValues: state.roleReducer.obj
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
	form: 'RoleMainForm', // a unique name for this form
	enableReinitialize: true,
	validate
})(MainForm);

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(form);

// export default MainForm;
