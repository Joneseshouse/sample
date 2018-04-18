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
			user_id: 0,
			admin_id: 0,
			type: ''
		}
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'UserTransactionFilterForm', // a unique name for this form
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
		this._renderFormAdmin = this._renderFormAdmin.bind(this);
		this._renderFormUser = this._renderFormUser.bind(this);
	}

	componentDidMount(){
	}

	_renderFormAdmin(){
		if(!Tools.isAdmin) return null;
		return (
			<div>
				<div className="row">
					<div className="col-md-12">

						<div className="row">
							<div className="col-md-2">
								<Field
				      				name="admin_id"
				      				type="select"
				      				options={store.getState().userTransactionReducer.listAdmin}
				      				onInputChange={value => {}}
				      				component={FormInput}
				      				label={this.props.labels.admin_id}/>
							</div>
							<div className="col-md-4">
								<Field
				      				name="user_id"
				      				type="select"
				      				options={store.getState().userTransactionReducer.listUser}
				      				onInputChange={value => {}}
				      				component={FormInput}
				      				label={this.props.labels.user_id}/>
							</div>
							<div className="col-md-3">
								<Field
				      				name="type"
				      				type="select"
				      				options={store.getState().userTransactionReducer.listType}
				      				onInputChange={value => {}}
				      				component={FormInput}
				      				label={this.props.labels.type}/>
							</div>
							<div className="col-md-3">
								<Field
				      				name="money_type"
				      				type="select"
				      				options={store.getState().userTransactionReducer.listMoneyType}
				      				onInputChange={value => {}}
				      				component={FormInput}
				      				label={this.props.labels.money_type}/>
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

	_renderFormUser(){
		if(Tools.isAdmin) return null;
		return (
			<div>
				<div className="row">
					<div className="col-md-12">

						<div className="row">
							<div className="col-md-6">
								<Field
				      				name="type"
				      				type="select"
				      				options={store.getState().userTransactionReducer.listType}
				      				onInputChange={value => {}}
				      				component={FormInput}
				      				label={this.props.labels.type}/>
							</div>
							<div className="col-md-6">
								<Field
				      				name="note"
				      				type="text"
				      				component={FormInput}
				      				label={this.props.labels.note}/>
							</div>
						</div>

						<div className="row">
							<div className="col-md-3">
								<Field
				      				name="from_amount"
				      				type="number"
				      				component={FormInput}
				      				label={this.props.labels.from_amount}/>

							</div>
							<div className="col-md-3">
								<Field
				      				name="to_amount"
				      				type="number"
				      				component={FormInput}
				      				label={this.props.labels.to_amount}/>

							</div>
							<div className="col-md-6">
								<Field
				      				name="date_range"
				      				type="dateRange"
				      				component={FormInput}
				      				label={this.props.labels.date_range}/>

							</div>
						</div>
					</div>
	  			</div>
			</div>
		);
	}

	_renderForm(){
		return (
			<div>
				{this._renderFormAdmin()}
				{this._renderFormUser()}
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
