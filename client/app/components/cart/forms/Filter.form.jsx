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
		}
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'CartFilterForm', // a unique name for this form
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
			<div className="row">
				<div className="col-md-3">
					<Field
	      				name="link"
	      				type="string"
	      				component={FormInput}
	      				label={this.props.labels.link}/>
				</div>
				<div className="col-md-3">
					<Field
	      				name="shop"
	      				type="string"
	      				component={FormInput}
	      				label={this.props.labels.shop}/>
				</div>
				<div className="col-md-4">
					<Field
	      				name="date_range"
	      				type="dateRange"
	      				component={FormInput}
	      				label={this.props.labels.date_range}/>
				</div>
				<div className="col-md-2" style={{paddingTop: 22}}>
					<button className="btn btn-success btn-block">
						<span className="glyphicon glyphicon-search"></span> &nbsp;
						Tìm kiếm
					</button>
				</div>
			</div>
		);
	}

	render() {
		const { handleSubmit, onSubmit, submitting, error, reset } = this.props;
		return (
			<form onSubmit={handleSubmit(onSubmit)}>
      			{this._renderForm()}
			</form>
		);
	}
}

export default FilterForm;
