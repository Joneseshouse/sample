import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import store from 'app/store';
import * as actionCreators from 'app/actions/actionCreators';
import {ADMIN_ROLES} from 'app/constants';
import {USER_ROLES} from 'app/constants';
import {labels} from '../../../_data';
import ValidateTools from 'helpers/ValidateTools';
import FormInput from 'utils/components/FormInput';
import { FIELD_TYPE } from 'app/constants';
import Tools from 'helpers/Tools';


@connect(
	state => ({
		initialValues: state.orderDetailReducer.objBillOfLanding
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'BillOfLandingForm', // a unique name for this form
	enableReinitialize: true,
	validate: values => ValidateTools.validateInput(
		values,
		Tools.getRules(labels.mainForm)
	)
})
class BillOfLandingForm extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderContent = this._renderContent.bind(this);
		this._renderAdmin = this._renderAdmin.bind(this);
		this._renderUser = this._renderUser.bind(this);
	}

	componentDidMount(){
	}

	_renderContent(){
		if(Tools.isAdmin){
			return this._renderAdmin();
		}else{
			return this._renderUser();
		}
	}

	_renderAdmin(){
		return (
			<div>
				<div className="row">
					<div className="col-md-4">
						<Field
		      				name="code"
		      				type="text"
		      				focus={true}
		      				component={FormInput}
		      				label={this.props.labels.code}/>
					</div>
					<div className="col-md-4">
						<Field
		      				name="transform_factor"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.transform_factor}/>
					</div>
					<div className="col-md-4">
						<Field
		      				name="packages"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.packages}/>
					</div>
				</div>

				<div className="row">
					<div className="col-md-4">
						<Field
		      				name="length"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.length}/>
					</div>
					<div className="col-md-4">
						<Field
		      				name="width"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.width}/>
					</div>
					<div className="col-md-4">
						<Field
		      				name="height"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.height}/>
					</div>
				</div>

				<Field
	  				name="input_mass"
	  				type="float"
	  				component={FormInput}
	  				label={this.props.labels.input_mass}/>
  			</div>
		);
	}

	_renderUser(){
		return (
			<div>
				<div className="row">
					<div className="col-md-4">
						<Field
		      				name="code"
		      				type="text"
		      				focus={true}
		      				component={FormInput}
		      				label={this.props.labels.code}/>
					</div>
					<div className="col-md-4">
						<Field
		      				name="packages"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.packages}/>
					</div>
					<div className="col-md-4">
						<Field
			  				name="mass"
			  				type="float"
			  				component={FormInput}
			  				label={this.props.labels.mass}/>
					</div>
				</div>
				<Field
      				name="insurance_register"
      				type="checkbox"
      				component={FormInput}
      				label={this.props.labels.insurance_register}/>
      			<Field
      				name="insurance_value"
      				type="float"
      				component={FormInput}
      				label={this.props.labels.insurance_value}/>

			</div>
		);
	}

	render() {
		const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
		return (
			<form
				onSubmit={handleSubmit(onSubmit)}>

				{this._renderContent()}

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

export default BillOfLandingForm;
