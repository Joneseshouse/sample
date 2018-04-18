import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import store from 'app/store';
import * as actionCreators from 'app/actions/actionCreators';
import {ADMIN_ROLES} from 'app/constants';
import {USER_ROLES} from 'app/constants';
import {labels} from '../_data';
import ValidateTools from 'helpers/ValidateTools';
import FormInput from 'utils/components/FormInput';
import { FIELD_TYPE } from 'app/constants';
import Tools from 'helpers/Tools';


@connect(
	state => ({
		initialValues: state.billOfLandingReducer.obj
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'BillOfLandingComplainForm', // a unique name for this form
	enableReinitialize: true,
	validate: values => ValidateTools.validateInput(
		values,
		Tools.getRules(labels.complainForm)
	)
})
class ComplainForm extends React.Component {
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
					<div className="col-md-6">
						<Field
		      				name="complain_amount"
		      				type="number"
		      				focus={true}
		      				component={FormInput}
		      				label={this.props.labels.complain_amount}/>
					</div>
					<div className="col-md-6">
		      			<Field
		      				name="complain_type"
		      				type="select"
		      				disabled={true}
		      				options={store.getState().billOfLandingReducer.listComplainType}
		      				component={FormInput}
		      				onInputChange={value => {}}
		      				label={this.props.labels.complain_type}/>
					</div>
				</div>
				<Field
      				name="complain_resolve"
      				type="checkbox"
      				component={FormInput}
      				label={this.props.labels.complain_resolve}/>
      			<div className="row">
      				<div className="col-md-6" dangerouslySetInnerHTML={{__html: this.props.initialValues.complain_note_user}}>
      				</div>
      				<div className="col-md-6">
		      			<Field
		      				name="complain_note_admin"
		      				type="richtext"
		      				table="bills_of_landing"
		      				parent={this.props.id}
		      				component={FormInput}
		      				label={this.props.labels.complain_note_admin}/>
      				</div>
      			</div>
  			</div>
		);
	}

	_renderUser(){
		return (
			<div>
				<div>
					<button
						onClick={()=>this.props.onResetComplain(this.props.id)}
						type="button"
						className="btn btn-primary btn-block">
						Tạo mới khiếu nại
					</button>
				</div>
				<br/>
				<div className="row">
					<div className="col-md-6">
						<Field
		      				name="complain_amount"
		      				type="number"
		      				focus={true}
		      				component={FormInput}
		      				label={this.props.labels.complain_amount}/>
					</div>
					<div className="col-md-6">
		      			<Field
		      				name="complain_type"
		      				type="select"
		      				options={store.getState().billOfLandingReducer.listComplainType}
		      				component={FormInput}
		      				onInputChange={value => {}}
		      				label={this.props.labels.complain_type}/>
					</div>
				</div>
				<Field
      				name="complain_resolve"
      				type="checkbox"
      				component={FormInput}
      				label={this.props.labels.complain_resolve}/>
      			<div className="row">
      				<div className="col-md-6">
						<Field
		      				name="complain_note_user"
		      				type="richtext"
		      				table="bills_of_landing"
		      				parent={this.props.id}
		      				component={FormInput}
		      				label={this.props.labels.complain_note_user}/>
      				</div>
      				<div className="col-md-6" dangerouslySetInnerHTML={{__html: this.props.initialValues.complain_note_admin}}></div>

      			</div>
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

export default ComplainForm;
