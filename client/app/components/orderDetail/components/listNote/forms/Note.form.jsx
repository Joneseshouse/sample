import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import store from 'app/store';
import * as actionCreators from 'app/actions/actionCreators';
import {labels} from '../../../_data';
import ValidateTools from 'helpers/ValidateTools';
import FormInput from 'utils/components/FormInput';
import { FIELD_TYPE } from 'app/constants';
import Tools from 'helpers/Tools';


@connect(
	state => ({
		initialValues: {note: null}
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch)
	})
)
@reduxForm({
	form: 'NoteForm', // a unique name for this form
	enableReinitialize: true,
	validate: values => ValidateTools.validateInput(
		values,
		Tools.getRules(labels.mainForm)
	)
})
class MainForm extends React.Component {
	static propTypes = {
		onSubmit: PropTypes.func.isRequired
	};
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
				<div className="row">
					<div className="col-md-10">
						<Field
		      				name="note"
		      				type="text"
		      				focus={true}
		      				component={FormInput}
		      				label={{}}/>
					</div>
					<div className="col-md-2">
						<button className="btn btn-success btn-block" disabled={submitting}>
							<span className="glyphicon glyphicon-ok"></span> &nbsp;
						</button>
					</div>
				</div>
				{error && <div className="alert alert-danger" role="alert">{error}</div>}
			</form>
		);
	}
}

export default MainForm;
