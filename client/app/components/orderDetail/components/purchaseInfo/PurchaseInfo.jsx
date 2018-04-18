import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import {ADMIN_ROLES} from 'app/constants';
import {labels} from '../../_data';
import Tools from 'helpers/Tools';
import ListBillOfLanding from '../listBillOfLanding/ListBillOfLanding';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch)
}))
class PurchaseInfo extends React.Component {
	static propTypes = {
		index: PropTypes.number.isRequired,
		purchase: PropTypes.object.isRequired,
		numberOfItems: PropTypes.number.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderPurchaseCodeEditButton = this._renderPurchaseCodeEditButton.bind(this);
		this._renderNoteEditButton = this._renderNoteEditButton.bind(this);
		this._renderBillOfLandingCodeAddButton = this._renderBillOfLandingCodeAddButton.bind(this);
	}

	_renderText($input){
		if(!$input){
			return <div><span className="strong">Chưa cập nhật</span></div>
		}
		return <div>{$input}</div>;
	}

	_renderPurchaseCodeEditButton(){
		if(Tools.isAdmin){
			return (
				<span
					onClick={() => {this.props.toggleModal(this.props.item.purchase_id, 'purchaseCodeModal')}}
					className="glyphicon glyphicon-pencil pointer"></span>
			);
		}
	}

	_renderNoteEditButton(){
		if(Tools.isAdmin){
			return (
				<span
					onClick={() => {this.props.toggleModal(this.props.item.purchase_id, 'purchaseNoteModal')}}
					className="glyphicon glyphicon-pencil pointer"></span>
			);
		}
	}

	_renderBillOfLandingCodeAddButton(){
		if(Tools.isAdmin){
			if(this.props.purchase.code){
				return (
					<button
						onClick={() => {
							this.props.orderDetailAction('selectedShop', this.props.item.purchase_id);
							this.props.toggleModal(null, 'billOfLandingModal')
						}}
						type="button" className="btn btn-success btn-block">
						<span className="glyphicon glyphicon-plus"></span>
						&nbsp;
						Mã vận đơn
					</button>
				);
			}
		}
	}

	render(){
		if(this.props.index === 0){
			return (
				<td rowSpan={this.props.numberOfItems}>
					<div>
						Ghi chú:
						&nbsp;
						{this._renderNoteEditButton()}
						{this._renderText(this.props.purchase.note)}
					</div>
					{/*
					<hr/>
					<div>
						Mã giao dịch:
						&nbsp;
						{this._renderPurchaseCodeEditButton()}
					</div>
					<div>
						<strong>
							{this._renderText(this.props.purchase.code)}
						</strong>
						<hr/>
						{this._renderBillOfLandingCodeAddButton()}
						<ListBillOfLanding
							{...this.props}
							purchaseId={this.props.item.purchase_id}
							listItem={this.props.purchase.bills_of_landing}/>
					</div>
					*/}
				</td>
			);
		}
		return null;
	}
}

export default PurchaseInfo;
