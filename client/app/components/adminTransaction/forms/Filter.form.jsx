import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import store from 'app/store';
import * as actionCreators from 'app/actions/actionCreators';
import {labels} from '../_data';
import ValidateTools from 'helpers/ValidateTools';
import FormInput from 'utils/components/FormInput';
import { ADMIN_ROLES, FIELD_TYPE } from 'app/constants';
import Tools from 'helpers/Tools';


@connect(
	state => ({
		initialValues: {
			// ...Tools.getInitData(labels.filterForm)
			target_admin_id: 0,
			admin_id: 0,
			type: ''
		}
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'AdminTransactionFilterForm', // a unique name for this form
	enableReinitialize: true,
	validate: values => ValidateTools.validateInput(
		values,
		Tools.getRules(labels.filterForm)
	)
})
class FilterForm extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderForm = this._renderForm.bind(this);
	}

	componentDidMount(){
	}

	_renderForm(){
		return (
			<div>
				<div className="row">
					<div className="col-md-12">

								<div className="row">
									<div className="col-md-4">
										<Field
						      				name="admin_id"
						      				type="select"
						      				options={store.getState().adminTransactionReducer.listAdmin}
						      				onInputChange={value => {}}
						      				component={FormInput}
						      				label={this.props.labels.admin_id}/>
									</div>
									<div className="col-md-4">
										<Field
						      				name="target_admin_id"
						      				type="select"
						      				options={store.getState().adminTransactionReducer.listAdmin}
						      				onInputChange={value => {}}
						      				component={FormInput}
						      				label={this.props.labels.target_admin_id}/>
									</div>
									<div className="col-md-4">
										<Field
						      				name="type"
						      				type="select"
						      				options={store.getState().adminTransactionReducer.listType}
						      				onInputChange={value => {}}
						      				component={FormInput}
						      				label={this.props.labels.type}/>
									</div>
								</div>

								<div className="row">
									<div className="col-md-2">
										<Field
						      				name="from_amount"
						      				type="number"
						      				component={FormInput}
						      				label={this.props.labels.from_amount}/>

									</div>
									<div className="col-md-2">
										<Field
						      				name="to_amount"
						      				type="number"
						      				component={FormInput}
						      				label={this.props.labels.to_amount}/>

									</div>
									<div className="col-md-4">
										<Field
						      				name="date_range"
						      				type="dateRange"
						      				component={FormInput}
						      				label={this.props.labels.date_range}/>

									</div>
									<div className="col-md-4">
										<Field
						      				name="note"
						      				type="text"
						      				component={FormInput}
						      				label={this.props.labels.note}/>
									</div>
								</div>
					</div>
	  			</div>
			</div>
		);
	}

	render() {
		const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
		return (
			<form onSubmit={handleSubmit(onSubmit)}>
      			{this._renderForm()}

				<div className="right-align">
					<button className="btn btn-success" disabled={submitting}>
						<span className="glyphicon glyphicon-ok"></span> &nbsp;
						{this.props.submitTitle}
					</button>
				</div>
			</form>
		);
	}
}

export default FilterForm;
