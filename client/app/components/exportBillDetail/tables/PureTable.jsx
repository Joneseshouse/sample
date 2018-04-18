import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import isEqual from 'lodash/isEqual';
import store from 'app/store';
import Table from 'rc-table';
import {labels} from '../_data';
import Tools from 'helpers/Tools';
import Paginator from 'utils/components/Paginator';
import {
	TableAddButton,
	TableCheckAll,
	TableRightTool,
	TableCheckBox
} from 'utils/components/table/TableComponents';
import TableFilter from '../components/tableFilter/TableFilter';


class PureTable extends React.Component {
	static propTypes = {
		bulkRemove: PropTypes.bool
	};
	static defaultProps = {
		bulkRemove: true
	};

	constructor(props) {
		super(props);
		this.state = {
			allowRemove: false,
			allowAdd: false,
			forceUpdate: false
		};
		this._renderCheckBox = this._renderCheckBox.bind(this);
		this._renderRightTool = this._renderRightTool.bind(this);
		this._renderCheckAll = this._renderCheckAll.bind(this);
		this._renderFilter = this._renderFilter.bind(this);
		this._renderAddButton = this._renderAddButton.bind(this);
	}

	shouldComponentUpdate(newProps, newState) {
		const sameList = Tools.isSameCollection(
			this.props.exportBillDetailReducer.listPure,
			newProps.exportBillDetailReducer.listPure
		);
		if(sameList){
			if(this.state.forceUpdate){
				this.setState({forceUpdate: false});
				return true;
			}
			return false;
		}
		return true;
	}

	_renderRightTool(value, row, index){
		return (
			<div className="center-align">
				<span
					onClick={() => this.props.onSelectItem(row)}
		        	className="glyphicon glyphicon-ok green pointer"></span>
			</div>
		);
	}

	_renderCheckBox(value, row, index){
		return (
			<TableCheckBox
				title={value}
				checked={row.checked || false}
				bulkRemove={this.props.bulkRemove}
				onCheck={(event) => this.props.onCheck(row.id, event.target.checked)}
			/>
		);
	}

	_renderCheckAll(){
		return (
			<TableCheckAll
				bulkRemove={this.props.bulkRemove}
				onCheckAll={this.props.onCheckAll}
			/>
		);
	}

	_renderFilter(){
		return (
			<TableFilter
				onFilter={this.props.onFilter}
				onFilterAddressUid={this.props.onFilterAddressUid}
				onRemove={this.props.onRemove}
				bulkRemove={false}
				/>
		);
	}

	_renderAddButton(){
		if(this.state.allowAdd){
			return (
				<TableAddButton
					onExecute={()=>this.props.toggleModal(null, 'mainModal')}
				/>
			);
		}
		return null;
	}

	render() {
		const headingData = Tools.getHeadingData(labels.mainForm);
		let columnData = [];
		forEach(headingData, (value, key) => {
			let data = {
				title: value.title,
				dataIndex: key,
				key,
				render: (value, row, index) => Tools.tableData(labels.mainForm, row, key, this.props.params, store.getState().exportBillDetailReducer)
			}
			if(value.width){
				data.width = value.width;
			}
			columnData.push(data);
		});
		const columns = [
			...columnData
			, {
				title: this._renderAddButton(),
				dataIndex: '',
				key:'opeartions',
				width: 60,
				// fixed: 'right',
				render: this._renderRightTool
			}
		];

		return (
			<div>
				{this._renderFilter()}
				<Table
					columns={columns}
					rowKey={record => record.id}
					data={this.props.exportBillDetailReducer.listPure}
					/>
				<div className="pagination-wrapper">
					<Paginator
						pageCount={this.props.exportBillDetailReducer.pages}
						onPageChange={this.props.onPageChange}/>
				</div>
			</div>
		);
	}
}

export default PureTable;
