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
import { FIELD_TYPE } from 'app/constants';
import Tools from 'helpers/Tools';


@connect(
	state => ({
		initialValues: state.orderDetailReducer.obj
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'OrderDetailMainForm', // a unique name for this form
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
		this.state = {}
	}

	componentDidMount(){
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
      				name="shop_name"
      				type="text"
      				component={FormInput}
      				label={this.props.labels.shop_name}/>
      			<Field
      				name="url"
      				type="text"
      				component={FormInput}
      				label={this.props.labels.url}/>
      			<div className="row">
      				<div className="col-md-6">
						<Field
		      				name="unit_price"
		      				type="float"
		      				component={FormInput}
		      				label={this.props.labels.unit_price}/>
      				</div>
      				<div className="col-md-6">
						<Field
		      				name="quantity"
		      				type="number"
		      				component={FormInput}
		      				label={this.props.labels.quantity}/>
      				</div>
      			</div>
      			<Field
      				name="avatar"
      				type="text"
      				component={FormInput}
      				label={this.props.labels.avatar}/>
      			<Field
      				name="properties"
      				type="text"
      				component={FormInput}
      				label={this.props.labels.properties}/>
      			<Field
      				name="message"
      				type="text"
      				component={FormInput}
      				label={this.props.labels.message}/>

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
