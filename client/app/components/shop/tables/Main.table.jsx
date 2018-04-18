import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import isEqual from 'lodash/isEqual';
import Table from 'rc-table';
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
		const sameList = Tools.isSameCollection(this.props.shopReducer.list, newProps.shopReducer.list);
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
		forEach(headingData, (value, key) => {
			columnData.push(
				{
					title: value.title,
					dataIndex: key,
					key,
					render: (value, row, index) => Tools.tableData(labels.mainForm, row, key, this.props.params)
				}
			);
		});
		const columns = [
			{
				title: this._renderCheckAll(),
				dataIndex: '',
				key: 'checkbox',
				width: 30,
				fixed: 'left',
				render: this._renderCheckBox
			},
			...columnData
			, {
				title: this._renderAddButton(),
				dataIndex: '',
				key:'opeartions',
				width: 70,
				fixed: 'right',
				render: this._renderRightTool
			}
		];

		return (
			<div>
				{this._renderFilter()}
				<Table
					columns={columns}
					rowKey={record => record.id}
					data={this.props.shopReducer.list}
					/>
				<div className="pagination-wrapper">
					<Paginator
						pageCount={this.props.shopReducer.pages}
						onPageChange={this.props.onPageChange}/>
				</div>
			</div>
		);
	}
}

MainTable.propTypes = {
	bulkRemove: PropTypes.bool
};

MainTable.defaultProps = {
	bulkRemove: true
};

export default MainTable;
