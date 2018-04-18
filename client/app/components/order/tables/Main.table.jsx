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
import TableFilter from '../components/tableFilter/TableFilter';


class MainTable extends React.Component {
	static propTypes = {
	};
	static defaultProps = {
	};
	constructor(props) {
		super(props);
		this.state = {
		};
		this._renderFilter = this._renderFilter.bind(this);
		this._renderRows = this._renderRows.bind(this);
	}

	_renderFilter(){
		return (
			<TableFilter
				onFilter={this.props.onFilter}
				onRemove={this.props.onRemove}
				onConfirmOrder={this.props.onConfirmOrder}
				bulkRemove={this.props.bulkRemove}
				/>
		);
	}

	_renderRows(){
		return this.props.listItem.map((row, index) => {
			return (
				<TableRow {...this.props} key={index} row={row} index={index}/>
			);
		});
	}

	render(){
		return (
			<div>
				{this._renderFilter()}
				<table className="table table-striped table-bordered">
					<thead>
						<tr>
							<th style={{width: 25}}>
								<TableCheckAll
									onCheckAll={this.props.onCheckAll}
								/>
							</th>
							<th style={{width: 30}}>STT</th>
							<th className="center-align">Mã đơn</th>
							<th style={{width: 120}} className="center-align">Khách hàng</th>
							<th style={{width: 115}} className="center-align">Trạng thái</th>
							<th className="center-align">Nhân viên</th>
							<th style={{width: 190}} className="center-align">Hệ số</th>
							<th style={{width: 100}} className="center-align">Tổng tiền</th>
							<th style={{width: 120}} className="center-align">Ngày</th>
							<th className="center-align">G.Chú</th>
							<th style={{width: 100}}>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						{this._renderRows()}
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
