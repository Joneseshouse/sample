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
			dathang_filter_admin_id: 0
		}
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'BolCheckFilterForm', // a unique name for this form
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

	_renderForm(submitting){
		if(Tools.isAdmin){
			return (
				<div className="row">
					<div className="col-md-4">
						<Field
		      				name="date_range"
		      				type="dateRange"
		      				component={FormInput}
		      				label={this.props.labels.date_range}/>
					</div>
					<div className="col-md-3">
						<Field
		      				name="dathang_filter_admin_id"
		      				type="select"
		      				options={store.getState().bolCheckReducer.listAdmin}
		      				onInputChange={value => {}}
		      				component={FormInput}
		      				label={{}}/>
					</div>
					<div className="col-md-2">
						<Field
		      				name="order_uid"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.order_uid}/>
					</div>
					<div className="col-md-2">
						<Field
		      				name="purchase_code"
		      				type="text"
		      				component={FormInput}
		      				label={this.props.labels.purchase_code}/>
					</div>
					<div className="col-md-1">
						<button className="btn btn-success btn-block" disabled={submitting}>
							<span className="glyphicon glyphicon-search"></span>
						</button>
					</div>
				</div>
			);
		}
		return null;
	}

	render() {
		if(!Tools.isAdmin) return null
		const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
		return (
			<form onSubmit={handleSubmit(onSubmit)}>
      			{this._renderForm(submitting)}
			</form>
		);
	}
}

export default FilterForm;
