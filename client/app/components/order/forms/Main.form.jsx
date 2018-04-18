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
		initialValues: state.orderReducer.obj
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'OrderMainForm', // a unique name for this form
	enableReinitialize: true,
	validate: values => ValidateTools.validateInput(
		values,
		Tools.getRules(labels.mainForm)
	)
})
class MainForm extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderForAdmin = this._renderForAdmin.bind(this);
	}

	componentDidMount(){
	}

	_renderForAdmin(){
		if(Tools.isAdmin){
			return (
				<div>
					<div className="row">
						<div className="col-md-6">
							<Field
			      				name="order_fee_factor"
			      				type="number"
			      				component={FormInput}
			      				label={this.props.labels.order_fee_factor}/>
						</div>
						<div className="col-md-6">
							<Field
			      				name="rate"
			      				type="float"
			      				component={FormInput}
			      				label={this.props.labels.rate}/>
						</div>
					</div>
					<Field
	      				name="admin_id"
	      				type="select"
	      				options={store.getState().orderReducer.listAdmin}
	      				onInputChange={value => {}}
	      				component={FormInput}
	      				label={this.props.labels.admin_id}/>

	      			<Field
	      				name="status"
	      				type="select"
	      				options={store.getState().orderReducer.listStatus}
	      				onInputChange={value => {}}
	      				component={FormInput}
	      				label={this.props.labels.status}/>

	      			<Field
	      				name="note"
	      				type="textarea"
	      				component={FormInput}
	      				label={this.props.labels.note}/>
	  			</div>
			);
		}
		return null;
	}

	render() {
		const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
		return (
			<form
				onSubmit={handleSubmit(onSubmit)}>
				<Field
      				name="address_id"
      				type="select"
      				options={store.getState().orderReducer.listAddress}
      				onInputChange={value => {}}
      				component={FormInput}
      				label={this.props.labels.address_id}/>

      			{this._renderForAdmin()}

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

export default MainForm;
