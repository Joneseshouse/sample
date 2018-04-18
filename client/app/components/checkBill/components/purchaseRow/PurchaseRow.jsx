import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import Select from 'react-select';
import {ADMIN_ROLES} from 'app/constants';
import Tools from 'helpers/Tools';
import {apiUrls} from '../../_data';
import OrderItemRow from '../orderItemRow/OrderItemRow';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch)
}))
class PurchaseRow extends React.Component {
	static propTypes = {
		purchase: PropTypes.object.isRequired,
		bolCode: PropTypes.string.isRequired,
		onChangeBol: PropTypes.func.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			billOfLandingCode: this.props.billOfLandingCode,
			changeBillOfLandingCode: false,
			bolCode: this.props.bolCode
		};
		this._renderBillOfLanding = this._renderBillOfLanding.bind(this);
		this.handleChangeBillOfLandingCode = this.handleChangeBillOfLandingCode.bind(this);
		this.handleConfirmCode = this.handleConfirmCode.bind(this);
		this.handleToggleCheckAll = this.handleToggleCheckAll.bind(this);
		this.discardChangeCode = this.discardChangeCode.bind(this);
		this._renderInsuranceInfo = this._renderInsuranceInfo.bind(this);
	}

	componentWillReceiveProps(nextProps){
		if(this.props.dataSession !== nextProps.dataSession){
			this.discardChangeCode();
		}
	}

	handleChangeBillOfLandingCode(event){
		this.setState({billOfLandingCode: event.target.value});
	}

	discardChangeCode(index=null, purchaseCode=null){
		this.setState({changeBillOfLandingCode: false});
		this.setState({billOfLandingCode: this.props.billOfLandingCode});
		if(index !== null && purchaseCode !== null){
            this.props.checkBillAction(
                'updatePurchase', 
                {
                    bill_of_landing_code: this.props.billOfLandingCode
                }, index
            );
		}
	}

	handleConfirmCode(index){
		const billOfLandingCode = this.state.billOfLandingCode;
		const purchaseCode = this.props.purchase.code;

		if(billOfLandingCode !== this.props.billOfLandingCode){

			const changeState = !this.state.changeBillOfLandingCode;
			this.setState({changeBillOfLandingCode: changeState});
			if(changeState){
				let data = {};
				data[purchaseCode] = billOfLandingCode;
                this.props.checkBillAction(
                    'updatePurchase', 
                    {
                        bill_of_landing_code: billOfLandingCode
                    }, index
                );

                Tools.apiCall(
                    apiUrls.billOfLandingCheckDuplicateCode, 
                    {
                        code: billOfLandingCode
                    }, false
                ).then(result => {
					if(result.success && result.data.duplicate){
                        this.discardChangeCode(index, purchaseCode);
                        let errorMessage = [
                            'Mã vận đơn này đã được sử dụng.',
                            'Bạn vui lòng chọn mã vận đơn khác.'
                        ].join(' ');
						window.alert(errorMessage);
					}
				});

			}else{
				// Restore to normal
				this.discardChangeCode(index, purchaseCode);
			}
		}else{
			this.discardChangeCode(index, purchaseCode);
		}
	}

	handleChangePurchase(index, key, value){
		if(key === 'mass'){
			value = parseFloat(value?value:0);
		}else if(['packages', 'length', 'width', 'height', 'sub_fee'].indexOf(key) !== -1){
			value = parseInt(value?value:0);
		}
		let data = {};
		data[key] = value;
		this.props.checkBillAction('updatePurchase', data, index);
	}

	handleToggleCheckAll(index){
		let numberOfItem = this.props.purchase.order_items.length;
		let numberOfMatch = 0;
		let numberOfZero = 0;
		forEach(this.props.purchase.order_items, item =>{
			if(item.quantity === item.checking_quantity){
				numberOfMatch++;
			}
			if(!item.checking_quantity){
				numberOfZero++;
			}
		});
		if(numberOfMatch === numberOfItem){
			this.props.checkBillAction('uncheckAllItemOfPurchase', null, index);
		}else{
			this.props.checkBillAction('checkAllItemOfPurchase', null, index);
		}
	}

	_renderInsuranceInfo(){
		const {bill_of_landing: bol} = this.props.purchase;
		if(!bol.insurance_register) return null;
		return (
			<div className="row" style={{marginTop: 10}}>
                <div className="col-md-12">
                    ¥{Tools.numberFormat(bol.insurance_value)} &rarr; {bol.note}
                </div>
			</div>
		);
	}

	bolFilter(code){
		this.setState({bolCode: code}, () => {
			this.props.onChangeBol(code)
		});
	}

	_renderBillOfLanding(){
		return (
			<div>
				<div className="row">
					<div className="col-md-3">
						<div className="input-group">
							<input
								type="number"
								step="0.1"
								value={this.props.purchase.input_mass}
                                onChange={
                                    e => {
                                        this.handleChangePurchase(
                                            this.props.index, 
                                            'input_mass', 
                                            e.target.value
                                        )
                                    }
                                }
								className="form-control"
								placeholder="K.Lượng"/>
							<span className="input-group-addon">Kg</span>
					    </div>
					</div>
					<div className="col-md-3">
						<div className="input-group">
							<input
								type="number"
								value={this.props.purchase.packages}
                                onChange={
                                    e => {
                                        this.handleChangePurchase(
                                            this.props.index,
                                            'packages',
                                            e.target.value
                                        )
                                    }
                                }
								className="form-control"
								placeholder="Kiện"/>
							<span className="input-group-addon">Kiện</span>
					    </div>
					</div>
					<div className="col-md-6">
						<Select
							valueKey="code"
							labelKey="code"
							value={this.state.bolCode}
							options={this.props.purchase.bills_of_landing}
							onChange={value => this.bolFilter(value.code)}/>
					</div>
				</div>
				<div className="row">
					<div className="col-md-3">
						<div className="input-group">
							<span className="input-group-addon">dài</span>
							<input
								type="number"
								value={this.props.purchase.length}
                                onChange={
                                    e => {
                                        this.handleChangePurchase(
                                            this.props.index,
                                            'length',
                                            e.target.value
                                        )
                                    }
                                }
								className="form-control"
								placeholder="Dài"/>
					    </div>
					</div>
					<div className="col-md-3">
						<div className="input-group">
							<span className="input-group-addon">rộng</span>
							<input
								type="number"
								value={this.props.purchase.width}
                                onChange={
                                    e => {
                                        this.handleChangePurchase(
                                            this.props.index,
                                            'width',
                                            e.target.value
                                        )
                                    }
                                }
								className="form-control"
								placeholder="Rộng"/>
					    </div>
					</div>
					<div className="col-md-3">
						<div className="input-group">
							<span className="input-group-addon">cao</span>
							<input
								type="number"
								value={this.props.purchase.height}
                                onChange={
                                    e => {
                                        this.handleChangePurchase(
                                            this.props.index,
                                            'height',
                                            e.target.value
                                        )
                                    }
                                }
								className="form-control"
								placeholder="Cao"/>
					    </div>
					</div>
					<div className="col-md-3">
						<div className="input-group">
							<span className="input-group-addon">P.Phí</span>
							<input
								type="number"
								value={this.props.purchase.sub_fee}
                                onChange={
                                    e => {
                                        this.handleChangePurchase(
                                            this.props.index,
                                            'sub_fee',
                                            e.target.value
                                        )
                                    }
                                }
								className="form-control"
								placeholder="P.Phí"/>
					    </div>
					</div>
				</div>
				{this._renderInsuranceInfo()}
			</div>
		);
	}

	_renderItems(purchase, listItem){
		return listItem.map((row, i) => {
			return (
				<OrderItemRow
					{...this.props}
					bolCode={this.state.bolCode}
					key={i}
					index={i}
					purchaseIndex={this.props.index}
					purchase={purchase}
					item={row}/>
			);
		});
	}

	render(){
		return (
			<tbody>
				<tr className="cyan-bg">
					<td colSpan={2} className="white" style={{verticalAlign: 'middle'}}>
						<div>
							<div className="strong black">{this.props.purchase.user_fullname}</div>
							<span
								onClick={e => this.handleToggleCheckAll(this.props.index)}
								className="glyphicon glyphicon-ok green pointer"></span>&nbsp;&nbsp;&nbsp;
							<strong>
								{this.props.purchase.order_uid}
							</strong>
							&nbsp;&rarr;&nbsp;
							<strong>
								{this.props.purchase.code}
							</strong>
							&nbsp;
							<span className="black">
								({this.props.purchase.number_of_items}) link
							</span>
						</div>
					</td>
					<td style={{verticalAlign: 'middle'}}>
						<button
							type="button"
							className="btn btn-success btn-block"
							onClick={() => this.props.onChange(this.props.purchase)}>
							<span className="glyphicon glyphicon-ok"></span>
						</button>
					</td>
					<td className="white">
						{this._renderBillOfLanding()}
					</td>
				</tr>
				{this._renderItems(this.props.purchase, this.props.purchase.order_items)}
				<tr>
					<td colSpan={2}></td>
					<td className="right-align strong">
						{this.props.purchase.order_items.reduce((total, item)=>(total + item.quantity), 0)}
					</td>
					<td className="left-align strong">
						{this.props.purchase.order_items.reduce((total, item)=>(total + item.checking_quantity), 0)}
					</td>
				</tr>
			</tbody>
		);
	}
}

export default PurchaseRow;
