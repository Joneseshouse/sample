import React from 'react';
import store from 'app/store';
import {ADMIN_ROLES} from 'app/constants';
import {labels} from '../_data';
import Tools from 'helpers/Tools';
import Paginator from 'utils/components/Paginator';
import {
	TableAddButton,
	TableCheckAll,
	TableCheckBox
} from 'utils/components/table/TableComponents';
import TableRow from '../components/tableRow/TableRow';


class MainTable extends React.Component {
	static propTypes = {
	};
	static defaultProps = {
	};
	constructor(props) {
		super(props);
		this.state = {
		};
		this._renderRow = this._renderRow.bind(this);
		this._renderTotal = this._renderTotal.bind(this);
	}

	_renderAddButton(){
		if(!Tools.isAdmin) return null;
		return (
			<TableAddButton
				onExecute={()=>this.props.toggleModal(null, 'mainModal')}
			/>
		);
	}

	_renderRow(){
		return this.props.listItem.map((row, index) => {
			const listType = Tools.mapLabels(this.props.listType)
			const listMoneyType = Tools.mapLabels(this.props.listMoneyType)
			const type = listType[row.type];
			const moneyType = listMoneyType[row.money_type];
			row.type_label = type;
			row.money_type_label = moneyType;
			return (
				<TableRow
					{...this.props}
					key={index}
					row={row}
					index={index}/>
			);
		});
	}

	_renderTotal(){
		return null;
		return (
			<tr>
				<th colSpan={6}>
					Tổng cộng
				</th>
				<th className="right-align bottom-align">
					₫{Tools.numberFormat(this.props.total.income)}
				</th>
				<th className="right-align bottom-align">
					₫{Tools.numberFormat(this.props.total.expense)}
				</th>
				<th className="right-align bottom-align">
					₫{Tools.numberFormat(this.props.total.purchasing)}
				</th>
				<th className="right-align bottom-align">
					<div>Số dư:</div>
					<div className="green">
						 ₫{Tools.numberFormat(this.props.total.balance)}
					</div>
				</th>
				<th className="right-align">
					<div>Công nợ:</div>
					<div className="red">
						 ₫{Tools.numberFormat(this.props.total.debt)}
					</div>
				</th>
			</tr>
		);
	}

	render(){
		return (
			<div className="table-responsive">
				<table className="table">
					<thead>
						<tr>
							<th>Tạo/sửa</th>
							<th>Nhân viên</th>
							<th>Khách hàng</th>
							<th>Loại GD</th>
							<th>Mã GD</th>
							<th className="right-align">Giá trị GD</th>
							<th className="right-align">SD có</th>
							<th className="right-align">SD nợ</th>
							<th className="right-align">Khả dụng</th>
							<th className="right-align">Cọc g.có</th>
							<th className="right-align">Cọc g.nợ</th>
							<th className="right-align">Giao hàng</th>
							<th className="right-align">V.Chuyển</th>
							<th className="right-align">C.Khấu</th>
							<th>Ảnh</th>
							<th>G.Chú</th>
							<th>{this._renderAddButton()}</th>
						</tr>
					</thead>
					<tbody>
						{this._renderTotal()}
						{this._renderRow()}
					</tbody>
				</table>
				<div className="pagination-wrapper">
					<Paginator
						pageCount={this.props.orderReducer.pages}
						onPageChange={this.props.onPageChange}/>
				</div>
			</div>
		);
	}
}
export default MainTable;
