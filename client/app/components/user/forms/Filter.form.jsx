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
			customer_id: 0,
			customer_staff: 0,
			lock: 'all',
			care: 'all',
			debt: 'all'
		}
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'UserFilterForm', // a unique name for this form
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
	}

	componentDidMount(){
	}

	render() {
		const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
		return (
			<form onSubmit={handleSubmit(onSubmit)}>
				<div className="row">
					<div className="col-md-3">
						<Field
		      				name="customer_id"
		      				type="select"
		      				options={store.getState().userReducer.listUser}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.customer_id}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="customer_staff"
		      				type="select"
		      				options={store.getState().userReducer.listAdmin}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.customer_staff}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="lock"
		      				type="select"
		      				options={store.getState().userReducer.listLockFilter}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.lock}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="care"
		      				type="select"
		      				options={store.getState().userReducer.listCareFilter}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.care}/>
					</div>
				</div>
				<div className="row">
					<div className="col-md-3">
						<Field
		      				name="debt"
		      				type="select"
		      				options={store.getState().userReducer.listDebtFilter}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={this.props.labels.debt}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="address_uid"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.address_uid}/>
					</div>
					<div className="col-md-2">
						<Field
		      				name="rate"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.rate}/>
					</div>
					<div className="col-md-2">
						<Field
		      				name="deposit_factor"
		      				type="float"
		      				component={FormInput}
		      				label={this.props.labels.deposit_factor}/>
					</div>
					<div className="col-md-2">
						<Field
		      				name="order_fee_factor"
		      				type="float"
		      				component={FormInput}
		      				label={this.props.labels.order_fee_factor}/>
					</div>
				</div>

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
