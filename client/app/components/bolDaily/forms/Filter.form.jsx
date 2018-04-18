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
	form: 'BolDailyFilterForm', // a unique name for this form
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
		if(Tools.isAdmin){
			return (
				<div>
					<div className="row">
						<div className="col-md-12">
							<div className="panel panel-primary">
								<div className="panel-heading">Lọc giao dịch</div>
								<div className="panel-body">
									<div className="row">
										<div className="col-md-4">
											<Field
							      				name="date_range"
							      				type="dateRange"
							      				component={FormInput}
							      				label={this.props.labels.date_range}/>
										</div>
										<div className="col-md-4">
											<Field
							      				name="last_updated"
							      				type="date"
							      				component={FormInput}
							      				label={this.props.labels.last_updated}/>

										</div>
										<div className="col-md-4">
											<Field
							      				name="bol"
							      				type="string"
							      				component={FormInput}
							      				label={this.props.labels.bol}/>
										</div>
									</div>
								</div>
							</div>
						</div>
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
