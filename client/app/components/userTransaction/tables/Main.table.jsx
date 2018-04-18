import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import isEqual from 'lodash/isEqual';
import Table from 'rc-table';
import store from 'app/store';
import {labels} from '../_data';
import Tools from 'helpers/Tools';
import Paginator from 'utils/components/Paginator';
import {
	TableAddButton,
	TableFilter,
	TableCheckAll,
	TableRightTool,
	TableCheckBox
} from 'utils/components/table/TableComponents';

class MainTable extends React.Component {
	static propTypes = {
		bulkRemove: PropTypes.bool
	};
	static defaultProps = {
		bulkRemove: true
	};
	constructor(props) {
		super(props);
		this.state = {
			allowRemove: true,
			forceUpdate: false
		};
		this._renderCheckBox = this._renderCheckBox.bind(this);
		this._renderRightTool = this._renderRightTool.bind(this);
		this._renderCheckAll = this._renderCheckAll.bind(this);
		this._renderFilter = this._renderFilter.bind(this);
		this._renderAddButton = this._renderAddButton.bind(this);
	}

	shouldComponentUpdate(newProps, newState) {
		const sameList = Tools.isSameCollection(this.props.userTransactionReducer.list, newProps.userTransactionReducer.list);
		const sameTotal = Tools.isSameCollection(this.props.total, newProps.total);

		if(sameList && sameTotal){
			if(this.state.forceUpdate){
				this.setState({forceUpdate: false});
				return true;
			}
			return false;
		}
		return true;
	}

	_renderRightTool(value, row, index){
		if(!index) return null;
		return (
			<TableRightTool
				allowRemove={this.state.allowRemove}
				toggleModal={() => this.props.toggleModal(row.id, 'mainModal')}
				onRemove={() => this.props.onRemove(row.id)}
			/>
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
				onRemove={this.props.onRemove}
				bulkRemove={this.props.bulkRemove}
				/>
		);
	}

	_renderAddButton(){
		return (
			<TableAddButton
				onExecute={()=>this.props.toggleModal(null, 'mainModal')}
			/>
		);
	}

	render() {
		const headingData = Tools.getHeadingData(labels.mainForm);
		let columnData = [];
		const total = {
			// amount: 0,
			id: 0,
			user_fullname: 'Tổng cộng',
			note: null,
			image: null,
			...this.props.total
		};
		forEach(headingData, (value, key) => {
			columnData.push(
				{
					title: value.title,
					dataIndex: key,
					key,
					render: (value, row, index) => Tools.tableData(labels.mainForm, row, key, this.props.params, store.getState().userTransactionReducer)
				}
			);
		});
		let columns = [...columnData];
		if(Tools.isAdmin){
			columns.push(
				{
					title: this._renderAddButton(),
					dataIndex: '',
					key:'opeartions',
					width: 70,
					fixed: 'right',
					render: this._renderRightTool
				}
			);
		}

		let data = [total, ...this.props.userTransactionReducer.list];
		return (
			<div>
				<Table
					columns={columns}
					scroll={{ x: true }}
					rowKey={record => record.id}
					data={data}/>
				<div className="pagination-wrapper">
					<Paginator
						pageCount={this.props.userTransactionReducer.pages}
						onPageChange={this.props.onPageChange}/>
				</div>
			</div>
		);
	}
}

export default MainTable;
