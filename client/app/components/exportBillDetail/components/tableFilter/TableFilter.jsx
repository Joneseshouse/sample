import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';


@connect(state => ({
		exportBillDetailReducer: state.exportBillDetailReducer
	}), dispatch => ({
		...bindActionCreators(actionCreators, dispatch),
		resetForm: (formName) => {
			dispatch(reset(formName));
		}
	})
)
class TableFilter extends React.Component {
	static propTypes = {
		onFilter: PropTypes.func.isRequired,
		onRemove: PropTypes.func.isRequired,
		bulkRemove: PropTypes.bool.isRequired
	};
	static defaultProps = {
		bulkRemove: true
	};

	constructor(props) {
		super(props);
	    this.onFilter = this.onFilter.bind(this);
	    this.onFilterAddressUid = this.onFilterAddressUid.bind(this);
		this._renderBulkRemoveButton = this._renderBulkRemoveButton.bind(this);
	}

	onFilter(event){
		// this.setState({keyword: event.target.value});
		this.props.exportBillDetailAction('keyword', event.target.value);
		this.props.onFilter(event);
	}

	onFilterAddressUid(event){
		// this.setState({keyword: event.target.value});
		this.props.exportBillDetailAction('address_uid', event.target.value);
		this.props.exportBillDetailAction('keyword', '');
		this.props.onFilterAddressUid(event);
	}

	_renderBulkRemoveButton(){
		if(this.props.bulkRemove){
			return(
				<span
					className="glyphicon glyphicon-remove"
					onClick={() => this.props.onRemove()}></span>
			);
		}
		return null;
	}

	render(){
		return (
			<div className="row">
				<div className="col-md-6">
					<input
						type="text"
						className="form-control"
						value={this.props.exportBillDetailReducer.keyword}
						onChange={this.onFilter}
						placeholder="Mã vận đơn..."/>
				</div>
				<div className="col-md-6">
					<input
						type="text"
						className="form-control"
						value={this.props.exportBillDetailReducer.address_uid}
						onChange={this.onFilterAddressUid}
						placeholder="Mã địa chỉ..."/>
				</div>
			</div>
		)
	}
}

export default TableFilter;