import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';


@connect(state => ({
		checkBillReducer: state.checkBillReducer
	}),
	dispatch => ({
		...bindActionCreators(actionCreators, dispatch),
		resetForm: (formName) => {
			dispatch(reset(formName));
		}
	})
)
class TableFilter extends React.Component {
	static propTypes = {
		onFilter: PropTypes.func.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
	    this.onFilter = this.onFilter.bind(this);
	}

	onFilter(event){
		this.props.checkBillAction('keyword', event.target.value);
		this.props.onFilter(event);
	}

	render(){
		return (
			<div className="row">
				<div className="col-md-12">
					<input
						type="text"
						className="form-control"
						value={this.props.checkBillReducer.keyword}
						onChange={this.onFilter}
						placeholder="Mã vận đơn..."/>
				</div>
			</div>
		)
	}
}

export default TableFilter;