import React from 'react';
import PropTypes from 'prop-types';
import Link from 'react-router/lib/Link';
import {API_URL} from 'app/constants';
import Tools from 'helpers/Tools';
import MainTableRightTool from '../../components/mainTableRightTool/MainTableRightTool';
import {
	TableCheckBox
} from 'utils/components/table/TableComponents';
import { ADMIN_ROLES, FIELD_TYPE } from 'app/constants';


export default class TableRow extends React.Component {
	static propTypes = {
		row: PropTypes.object.isRequired,
		index: PropTypes.number.isRequired
	};
	static defaultProps = {
	};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderRightTool = this._renderRightTool.bind(this);
		this._renderCheckBox = this._renderCheckBox.bind(this);
		this._renderDate = this._renderDate.bind(this);
		this._renderNote = this._renderNote.bind(this);
	}

	_renderRightTool(row, index){
		return (
			<MainTableRightTool
				row={row}
				allowRemove={this.state.allowRemove}
				allowUpdate={this.state.allowUpdate}
				toggleModal={() => this.props.toggleModal(row.id, 'mainModal')}
				onToggleLog={() => this.props.onToggleLog(row.id)}
				onRemove={() => this.props.onRemove(row.id)}
			/>
		);
	}

	_renderCheckBox(row, index){
		return (
			<TableCheckBox
				checked={row.checked || false}
				onCheck={(event) => this.props.onCheck(row.id, event.target.checked)}
			/>
		);
	}

	_renderDate(input){
		let result = Tools.dateFormat(input);
		if(!result){
			return <span className="red">Chưa mua</span>
		}
		return <span className="green">{result}</span>
	}

	_renderStaff(input){
		if(input){
			return input;
		}
		return "Chưa phân công"
	}

	_switchClassName(status){
		switch(status){
			case 'new':
				return 'red-bg';
			case 'confirm':
				return 'blue-gray-bg';
			case 'purchasing':
				return 'blue-bg';
			case 'complain':
				return 'orange-bg';
			case 'done':
				return 'green-bg';
			default:
				return '';
		}
	}

	_renderNote(note){
		if(!note) return null;
		return (
			<div>
				<span className="tooltiptext1">{note}</span>
				<div className="center-align">
					<span className="glyphicon glyphicon-exclamation-sign"></span>
				</div>
			</div>
		);
	}

	_renderStatusDetail(row){
		if(!Tools.isAdmin) return null;
		return (
			<table>
				<tbody>
					<tr>
						<td>Đã t.toán: &nbsp;</td>
						<td>
							{row.bol_statistics.shop_release_bol} / {row.bol_statistics.total_bol}
						</td>
					</tr>
					<tr>
						<td>Kho TQ: &nbsp;</td>
						<td>
							{row.bol_statistics.cn_bol} / {row.bol_statistics.total_bol}
						</td>
					</tr>
					<tr>
						<td>HN nhận: &nbsp;</td>
						<td>
							{row.bol_statistics.vn_bol} / {row.bol_statistics.total_bol}
						</td>
					</tr>
					<tr>
						<td>Đã giao: &nbsp;</td>
						<td>
							{row.bol_statistics.export_bol} / {row.bol_statistics.total_bol}
						</td>
					</tr>
				</tbody>
			</table>
		);
	}

	render(){
		const {row, index, orderStatisticsReducer:{listStatus}} = this.props;
		const listStatusRef = Tools.mapLabels(listStatus);
		return(
			<tr>
				<td className="middle-align">
					{this._renderCheckBox(row, index)}
				</td>
				<td className="center-align">
					{index + 1}
				</td>
				<td className="center-align">
					<div className="borderStatistics-bottom">
						<Link to={Tools.toUrl('orderStatistics', [row.type, row.status, row.id])}>
							{row.uid}
						</Link>
					</div>
					<div>
						{row.address_code}
					</div>
				</td>
				<td className="center-align">
					<div
						className="strong pointer"
						onClick={()=>this.props.toggleModal(row.customer_id, 'userDetailModal')}
						>
						{/*
						<Link to={Tools.toUrl('user', [row.customer_id])}>
							{row.customer_full_name}
						</Link>
						*/}
						{row.customer_full_name}
					</div>
				</td>
				<td>
					<div>
						<strong>
							{listStatusRef[row.status]}
						</strong>
					</div>
					{this._renderStatusDetail(row)}
					{/*
					<table
						width="100%"
						className="center-align status-table">
						<tbody>
							<tr>
								<td
									rowSpan={2}
									className={"middle-align center-align " + this._switchClassName(row.status)}
									width={130}>
									{listStatusRef[row.status]}
								</td>
								<td className="light-green-bg tooltip1">
									<span className="tooltiptext1">Shop phát hàng</span>
									<span>
										{row.bol_statistics.shop_release_bol} / {row.bol_statistics.total_bol}
									</span>
								</td>
								<td className="lime-bg tooltip1">
									<span className="tooltiptext1">Quảng Châu nhận hàng</span>
									<span>
										{row.bol_statistics.cn_bol} / {row.bol_statistics.total_bol}
									</span>
								</td>
							</tr>
							<tr>
								<td className="yellow-bg tooltip1">
									<span className="tooltiptext1">Việt Nam nhận hàng</span>
									<span>
										{row.bol_statistics.vn_bol} / {row.bol_statistics.total_bol}
									</span>
								</td>
								<td className="amber-bg tooltip1">
									<span className="tooltiptext1">Xuất hàng</span>
									<span>
										{row.bol_statistics.export_bol} / {row.bol_statistics.total_bol}
									</span>
								</td>
							</tr>
						</tbody>
					</table>
					*/}
				</td>
				<td>
					<div className="borderStatistics-bottom">
						<strong>CSKH:</strong> {this._renderStaff(row.customer_care_staff_full_name)}
					</div>
					<div className="borderStatistics-bottom">
						<strong>Mua:</strong> {this._renderStaff(row.admin_full_name)}
					</div>
					<div>
						<strong>Duyệt:</strong> {row.confirm_full_name}
					</div>
				</td>
				<td className="left-align">
					<div className="borderStatistics-bottom">
						<strong>Đơn giá vận chuyển:</strong> {Tools.numberFormat(row.delivery_fee_unit)}
					</div>
					<div className="borderStatistics-bottom">
						<strong>Phí đặt hàng:</strong> {Tools.numberFormat(row.order_fee_factor)}%
					</div>
					<div className="borderStatistics-bottom">
						<strong>Tỷ giá:</strong> {Tools.numberFormat(row.rate)}
					</div>
				</td>
				<td className="right-align">
					₫{Tools.numberFormat(row.total)}
				</td>
				<td>
					<div className="borderStatistics-bottom">
						<strong>Tạo:</strong> {this._renderDate(row.created_at)}
					</div>
					<div>
						<strong>Mua:</strong> {this._renderDate(row.confirm_date)}
					</div>
				</td>
				<td className="tooltip1">
					{this._renderNote(row.note)}
				</td>
				<td>
					{this._renderRightTool(row, index)}
				</td>
			</tr>
		)
	}
}
