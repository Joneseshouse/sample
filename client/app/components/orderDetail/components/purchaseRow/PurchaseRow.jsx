import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';
import {ADMIN_ROLES} from 'app/constants';
import {labels} from '../../_data';
import Tools from 'helpers/Tools';
import OrderItemRow from '../orderItemRow/OrderItemRow';
import ListBillOfLanding from '../listBillOfLanding/ListBillOfLanding';


@connect(state => ({
    }), 
    dispatch => ({
        action: bindActionCreators(actionCreators, dispatch).orderDetailAction
    })
)
class PurchaseRow extends React.Component {
	static propTypes = {
		index: PropTypes.number.isRequired,
		purchase: PropTypes.object.isRequired,
		rate: PropTypes.number.isRequired,
		order_fee_factor: PropTypes.number.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderRealAmountLabel = this._renderRealAmountLabel.bind(this);
		this._renderRealAmountValue = this._renderRealAmountValue.bind(this);
		this._renderDeliveryFeeEditButton = this._renderDeliveryFeeEditButton.bind(this);
		this._renderPurchaseCodeEditButton = this._renderPurchaseCodeEditButton.bind(this);
		this._renderAddBolButton = this._renderAddBolButton.bind(this);
		this._sumQuantity = this._sumQuantity.bind(this);
	}

	_sumQuantity(items=[]){
		let total = 0;
		forEach(items, item => {
			total += item.quantity;
		});
		return (
			<strong>{total}</strong>
		);
	}

	_renderText($input){
		if(!$input){
			return <div><span className="strong">Chưa cập nhật</span></div>
		}
		return <div>{$input}</div>;
	}

	_renderItem(purchase, listItem, shopIndex){
		return listItem.map((row, i) => {
			return (
				<OrderItemRow
					{...this.props}
					key={i}
					index={i}
					purchase={purchase}
					numberOfItems={listItem.length}
					item={row}
					rate={this.props.rate}/>
			);
		});
	}

	_renderNumber($input, prefix){
		if($input < 0.001){
			return <span>Chưa cập nhật</span>
		}
		return prefix + Tools.numberFormat(parseFloat($input));
	}

	_renderDeliveryFeeEditButton(){
		if(Tools.isAdmin){
			return(
				<span>
				<span
					onClick={() => {this.props.toggleModal(this.props.purchase.id, 'deliveryFeeModal')}}
					className="glyphicon glyphicon-pencil pointer"></span>
				</span>
			);
		}
	}

	_renderRealAmountLabel(){
		if(Tools.isAdmin){
			return(
				<div>
					Thanh toán thực
					&nbsp;
					<span
						onClick={() => {this.props.toggleModal(this.props.purchase.id, 'realAmountModal')}}
						className="glyphicon glyphicon-pencil pointer"></span>
				</div>
			);
		}
	}

	_renderRealAmountValue(){
		if(Tools.isAdmin){
			return(
				<div>
					{this._renderNumber(this.props.purchase.real_amount, '￥')}
				</div>
			);
		}
	}

	_renderPurchaseCodeEditButton(){
		if(Tools.isAdmin){
			return (
				<span
					onClick={() => {this.props.toggleModal(this.props.purchase.id, 'purchaseCodeModal')}}
					className="glyphicon glyphicon-pencil pointer"></span>
			);
		}
	}

	_renderAddBolButton(){
		if(Tools.isAdmin){
			if(this.props.purchase.code){
				return (
					<span
						onClick={() => {
							this.props.orderDetailAction('selectedShop', this.props.purchase.id);
							this.props.toggleModal(null, 'billOfLandingModal')
						}}
						className="glyphicon glyphicon-plus pointer"></span>
				);
			}
		}
	}

	render(){
		return (
			<tbody>
				<tr><td colSpan={8} style={{height: 50}}></td></tr>
				<tr><td colSpan={8}>
					<div className="row">
						<div className="col-md-4">
							<div>Tên shop:</div>
							<div>
								<strong>
									[{this.props.purchase.vendor}] {this.props.purchase.title}
								</strong>
							</div>
						</div>
						<div className="col-md-2">
							<div>
								Mã giao dịch:
								&nbsp;
								{this._renderPurchaseCodeEditButton()}
							</div>
							<div>
								<strong>
									{this._renderText(this.props.purchase.code)}
								</strong>
							</div>
						</div>
						<div className="col-md-2">
							<div>
								Mã bill:
								&nbsp;
								{this._renderAddBolButton()}
							</div>
							<div>
								<ListBillOfLanding
									{...this.props}
									purchaseId={this.props.purchase.id}
									listItem={this.props.purchase.bills_of_landing}/>
							</div>
						</div>
						<div className="col-md-2">
							<div>
								V.chuyển nội địa:
								&nbsp;
								{this._renderDeliveryFeeEditButton()}
							</div>
							<strong>
								{this._renderNumber(this.props.purchase.inland_delivery_fee_raw, '￥')}
							</strong>
						</div>
						<div className="col-md-2">
							<div>
								V.chuyển:&nbsp;
							</div>
							<strong>
								{this._renderNumber(this.props.purchase.delivery_fee, '₫')}
							</strong>
						</div>
					</div>
				</td></tr>
				<tr>
                    <th 
                        className="center-align pointer blue"
                        onClick={() => {
                            this.props.action('checkPurchase', {purchaseId: this.props.purchase.id});
                        }} 
                        >
                        Stt
                    </th>
					<th style={{width: 80}}>Ảnh</th>
					<th>Tên sản phẩm</th>
					<th className="right-align">Giá tiền</th>
					<th className="right-align">Số lượng</th>
					<th className="right-align">Thành tiền</th>
					<th>Trạng thái</th>
					<th>
						&nbsp;
					</th>
				</tr>


				{/*
				<tr className="cyan-bg">
					<td colSpan={3} className="white">
						<strong>
							<span
								onClick={()=>this.props.onToggleLog(this.props.purchase.id)}
								className="glyphicon glyphicon-th-large pointer green"></span>&nbsp;
							[{this.props.purchase.vendor}] {this.props.purchase.title}
						</strong>
					</td>
					<td className="white right-align"></td>
					<td className="white right-align"></td>
					<td className="white right-align">
						<div>
							<strong>
								{Tools.numberFormat(this.props.purchase.amount)} ￥
							</strong>
						</div>
						<div>
							<strong>
								{Tools.numberFormat(this.props.purchase.amount*this.props.rate)} ₫
							</strong>
						</div>
					</td>
					<td colSpan={2}>
						<div className="strong white">
							Phí vận chuyển
							&nbsp;
							{this._renderDeliveryFeeEditButton()}
						</div>
						{this._renderRealAmountLabel()}
					</td>
					<td colSpan={2}>
						<div className="strong white">
							{this._renderNumber(this.props.purchase.delivery_fee, '₫')} +
							{this._renderNumber(this.props.purchase.inland_delivery_fee_raw, '￥')}
						</div>
						{this._renderRealAmountValue()}
					</td>
				</tr>
				*/}


				{this._renderItem(this.props.purchase, this.props.purchase.order_items, this.props.index)}
				<tr>
					<td className="middle-align">
						<strong>Tổng</strong>
					</td>
					<td>
						<div>K.lượng: <strong>{this.props.purchase.mass} Kg</strong></div>
						<div>Phí d.vụ: <strong>￥{Tools.numberFormat(this.props.order_fee_factor * this.props.purchase.amount / 100)}</strong></div>
					</td>
					<td className="center-align">
						<div>Tổng tiền hàng + vận chuyển + phụ phí</div>
						<div><strong>₫{Tools.numberFormat(Math.floor(this.props.purchase.total))}</strong></div>
					</td>
					<td className="right-align">
						<div>Phụ phí</div>
						<div><strong>₫{Tools.numberFormat(this.props.purchase.sub_fee)}</strong></div>
					</td>
					<td className="right-align">
						{this._sumQuantity(this.props.purchase.order_items)}
					</td>
					<td className="right-align">
						<div>
							<strong>
								￥{Tools.numberFormat(this.props.purchase.amount)}
							</strong>
						</div>
						<div>
							<strong>
								₫{Tools.numberFormat(Math.floor(this.props.purchase.total_raw * this.props.rate))}
							</strong>
						</div>
					</td>
					<td colSpan={2} className="center-align">
						{this._renderRealAmountLabel()}
						<strong>
							{this._renderRealAmountValue()}
						</strong>
					</td>
				</tr>
			</tbody>
		);
	}
}

export default PurchaseRow;
