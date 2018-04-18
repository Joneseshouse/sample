import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';
import {labels} from '../../_data';
import Tools from 'helpers/Tools';
import PurchaseInfo from '../purchaseInfo/PurchaseInfo';
import {
	TableCheckBox
} from 'utils/components/table/TableComponents';


@connect(state => ({
    }), 
    dispatch => ({
        action: bindActionCreators(actionCreators, dispatch).orderDetailAction
    })
)
class OrderItemRow extends React.Component {
	static propTypes = {
		index: PropTypes.number.isRequired,
		purchase: PropTypes.object.isRequired,
		numberOfItems: PropTypes.number.isRequired,
		item: PropTypes.object.isRequired,
		rate: PropTypes.number.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {};
        this._renderNote = this._renderNote.bind(this);
        this._renderCheckbox = this._renderCheckbox.bind(this);
        this.handleCheck = this.handleCheck.bind(this);
    }

    handleCheck (purchaseId, id, checked) {
        this.props.action('check', {purchaseId, id, checked});
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

    _renderCheckbox (row) {
        return (
			<TableCheckBox
				checked={row.checked || false}
				onCheck={(event) => this.handleCheck(row.purchase_id, row.id, event.target.checked)}
			/>
		);
    }

	render(){
		let status = this.props.orderDetailReducer.obj.status;
		let listStatus = this.props.orderDetailReducer.listStatus;

		if(Tools.isExistInArray(listStatus, status, 1) && !Tools.isAdmin){
			return (
				<tr>
                    <td className="center-align">
                        <label>
                            {this.props.item.counter}
                        </label>
                        <div>
                            {this._renderCheckbox(this.props.item)}
                        </div>
                    </td>
					<td className="grid-thumbnail">
						<img
							className="pointer"
							onClick={() => {this.props.toggleModal(this.props.item.avatar, 'previewModal')}}
							src={this.props.item.avatar}
							width="100%"/>
					</td>
					<td>
						<div className="wrap" style={{width: 400}}>
							<a href={this.props.item.url} target="_blank">
								{this.props.item.title}
							</a>
						</div>
						<div className="cyan">
							{this.props.item.properties}
						</div>
						{this._renderNote()}
					</td>

					<td className="right-align">
						<div>
							￥{Tools.numberFormat(this.props.item.unit_price)}
						</div>
						<div>
							₫{Tools.numberFormat(this.props.item.unit_price * this.props.rate)}
						</div>
					</td>
					<td className="right-align">
						{Tools.numberFormat(this.props.item.quantity)}
					</td>
					<td className="right-align">
						<div>
							<strong>
                                ￥
                                {
                                    Tools.numberFormat(
                                        parseFloat(this.props.item.unit_price) * 
                                        parseInt(this.props.item.quantity
                                        )
                                    )
                                }
							</strong>
						</div>
						<div>
							<strong>
                                ₫ 
                                {
                                    Tools.numberFormat(
                                        Math.floor(
                                            parseFloat(this.props.item.unit_price) * 
                                            parseInt(this.props.item.quantity)
                                        ) * this.props.rate)}
							</strong>
						</div>
					</td>
					<td>
						<div>
							{this.props.item.checked_status.map((status, index) => {
								if(status.status){
									return (
										<div key={index}>
                                            <strong>
                                                {status.bol_code}:
                                            </strong> 
                                            [{status.quantity}] {status.status}
										</div>
									)
								}
							})}
						</div>
					</td>
					<td className="center-align">
						<span
							onClick={() => {this.props.toggleModal(this.props.item.id, 'itemModal')}}
							className="glyphicon glyphicon-pencil"></span>
					</td>
				</tr>
			);
		}
		return (
			<tr>
                <td className="center-align">
                    <label>
                        {this.props.item.counter}
                    </label>
                    <div>
                        {this._renderCheckbox(this.props.item)}
                    </div>
                </td>
				<td className="grid-thumbnail">
					<img
						className="pointer"
						onClick={() => {this.props.toggleModal(this.props.item.avatar, 'previewModal')}}
						src={this.props.item.avatar}
						width="100%"/>
				</td>
				<td>
					<div className="wrap" style={{width: 400}}>
						<a href={this.props.item.url} target="_blank">
							{this.props.item.title}
						</a>
					</div>
					<div className="cyan">
						{this.props.item.properties}
					</div>
					{this._renderNote()}
				</td>

				<td className="right-align">
					<div
						className="dot-underline blue pointer"
						onClick={() => {this.props.toggleModal(this.props.item.id, 'unitPriceModal')}}>
						￥{Tools.numberFormat(this.props.item.unit_price)}
					</div>
					<div>
						₫{Tools.numberFormat(this.props.item.unit_price * this.props.rate)}
					</div>
				</td>
				<td className="right-align">
					{Tools.numberFormat(this.props.item.quantity)}
				</td>
				<td className="right-align">
					<div>
						<strong>
                            ￥
                            {
                                Tools.numberFormat(
                                    parseFloat(this.props.item.unit_price) * 
                                    parseInt(this.props.item.quantity
                                    )
                                )
                            }
						</strong>
					</div>
					<div>
						<strong>
                            ₫ 
                            {
                                Tools.numberFormat(
                                    Math.floor(
                                        parseFloat(this.props.item.unit_price) * 
                                        parseInt(this.props.item.quantity)
                                    ) * this.props.rate
                                )
                            }
						</strong>
					</div>
				</td>
				<td>
					<div>
						{this.props.item.checked_status.map((status, index) => {
							if(status.status){
								return (
									<div key={index}>
										<strong>{status.bol_code}:</strong> [{status.quantity}] {status.status}
									</div>
								)
							}
						})}
					</div>
				</td>
				<td className="center-align">
					<span
						onClick={() => {this.props.toggleModal(this.props.item.id, 'itemModal')}}
						className="glyphicon glyphicon-pencil"></span>
					&nbsp;
					&nbsp;
					<span
						onClick={() => {this.props.onRemove(this.props.item.id)}}
						className="glyphicon glyphicon-remove red"></span>
				</td>
			</tr>
		);
	}
}

export default OrderItemRow;
