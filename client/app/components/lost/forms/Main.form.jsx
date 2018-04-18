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
import { ADMIN_ROLES } from 'app/constants';


@connect(
	state => ({
		initialValues: state.lostReducer.obj
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'LostMainForm', // a unique name for this form
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
		if(Tools.isAdmin){
			const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
			return (
				<form
					onSubmit={handleSubmit(onSubmit)}>
					<div className="row">
						<div className="col-md-12">
							<Field
			      				name="bill_of_landing_id"
			      				type="select"
			      				options={store.getState().lostReducer.listBillLost}
			      				onInputChange={value => {}}
			      				focus={true}
			      				component={FormInput}
			      				label={this.props.labels.bill_of_landing_id}/>
						</div>
					</div>
					<Field
	      				name="preview"
	      				type="text"
	      				component={FormInput}
	      				label={this.props.labels.preview}
	      			/>
					<Field
	      				name="description"
	      				type="richtext"
	      				component={FormInput}
	      				label={this.props.labels.description}
	      			/>

					{error && <div className="alert alert-danger" role="alert">{error}</div>}

					<div className="row custom-modal-footer">
						<div className="col-md-6 col-sm-12 cancel">
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
		const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
		return (
			<form
				onSubmit={handleSubmit(onSubmit)}>
				<div className="row">
					<div className="col-md-12">
						<Field
		      				name="bill_of_landing_id"
		      				type="select"
		      				disabled={true}
		      				focus={true}
		      				component={FormInput}
		      				options={store.getState().lostReducer.listBillLost}
		      				onInputChange={value => {}}
		      				label={this.props.labels.bill_of_landing_id}/>
					</div>
				</div>
      			<div className="row">
					<div className="col-md-12">
						<label>{this.props.labels.description.title}</label>
	      				<div className="form-group">
		      				<div className="col-md-12 description" dangerouslySetInnerHTML={{__html: store.getState().lostReducer.obj.description}} />
	      				</div>
					</div>
      			</div>

				{error && <div className="alert alert-danger" role="alert">{error}</div>}

				<div className="row custom-modal-footer">
					<div className="col-md-6 cancel">
						{this.props.children}
					</div>
				</div>
			</form>
		);
	}
}

export default MainForm;
