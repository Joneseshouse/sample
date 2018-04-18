import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import {labels} from '../../_data';
import store from 'app/store';
import Tools from 'helpers/Tools';
import Select from 'react-select';


class OrderItemRow extends React.Component {
	static propTypes = {
		purchase: PropTypes.object.isRequired,
		bolCode: PropTypes.string.isRequired,
		item: PropTypes.object.isRequired,
		toggleModal: PropTypes.func.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			checking_quantity: this.props.item.checking_quantity,
			checking_status: this.props.item.checking_status
		};
		this.handleChangeCheckedQuantity = this.handleChangeCheckedQuantity.bind(this);
		this.handleChangeCheckedStatus = this.handleChangeCheckedStatus.bind(this);
		this._renderNote = this._renderNote.bind(this);
	}

	componentWillReceiveProps(newProps) {
		this.setState({
			checking_quantity: newProps.item.checking_quantity,
			checking_status: newProps.item.checking_status
		});
	}

	handleChangeCheckedQuantity(quantity){
		const data = {
			checking_quantity: parseInt(quantity)
		};
		this.setState(data);
		this.props.checkBillAction('checkedQuantity', data, this.props.index, this.props.purchaseIndex);
	}

	handleChangeCheckedStatus(status){
		const data = {
			checking_status: status
		};
		this.setState(data);
		this.props.checkBillAction('checkedStatus', data, this.props.index, this.props.purchaseIndex);
	}

	_renderNote(){
		return (
			<div>
				<span
					className="glyphicon glyphicon-comment green pointer"
					onClick={() => {this.props.toggleModal(this.props.item.id, 'noteModal')}}></span>&nbsp;
				<em className="red">
					{this.props.item.note}
				</em>
			</div>
		);
	}

	render(){
		return (
			<tr>
				<td className="grid-thumbnail">
					<img
						className="pointer"
						onClick={() => {this.props.toggleModal(this.props.item.avatar, 'previewModal')}}
						src={this.props.item.avatar}
						width="100%"/>
				</td>
				<td>
					<div>
						<span>[￥<strong>{this.props.item.unit_price}</strong>]</span>&nbsp;
						<a href={this.props.item.url} target="_blank">
							{this.props.item.title}
						</a>
					</div>
					<div className="cyan">
						{this.props.item.properties}
					</div>
					{this._renderNote()}
				</td>

				<td className="right-align" style={{verticalAlign: 'middle'}}>
					<strong>
						{this.props.item.checked_quantity}/{this.props.item.quantity}
					</strong>
				</td>

				<td style={{verticalAlign: 'middle'}}>
					<div className="row">
						<div className="col-md-2">
							<input
								type="number"
								className="form-control"
								value={this.state.checking_quantity}
								onChange={e => {this.handleChangeCheckedQuantity(e.target.value)}}
								placeholder="Số lượng..."/>
						</div>
						<div className="col-md-10">
							<Select
								valueKey="id"
								labelKey="title"
								value={this.state.checking_status}
								options={store.getState().checkBillReducer.listCheckItemStatus}
								onChange={value => {this.handleChangeCheckedStatus(value.id)}}/>
						</div>
					</div>
				</td>
			</tr>
		);
	}
}

export default OrderItemRow;
