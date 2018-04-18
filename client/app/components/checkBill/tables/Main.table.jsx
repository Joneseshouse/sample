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
	TableCheckAll,
	TableRightTool,
	TableCheckBox
} from 'utils/components/table/TableComponents';
import TableFilter from '../components/tableFilter/TableFilter';

class MainTable extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			allowRemove: true,
			allowUpdate: false
		};
		this._renderRightTool = this._renderRightTool.bind(this);
	}

	_renderRightTool(value, row, index){
		return (
			<TableRightTool
				allowRemove={this.state.allowRemove}
				allowUpdate={this.state.allowUpdate}
				toggleModal={() => this.props.toggleModal(row.id, 'mainModal')}
				onRemove={() => this.props.onRemove(row.id)}
			/>
		);
	}

	render(){
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
			...columnData
			, {
				title: '',
				dataIndex: '',
				key:'opeartions',
				width: 70,
				fixed: 'right',
				render: this._renderRightTool
			}
		];

		return (
			<div>
				<TableFilter
					onFilter={this.props.onFilter}
					/>
				{/*
				<Table
					columns={columns}
					rowKey={record => record.id}
					data={this.props.checkBillReducer.list}/>
				<div className="pagination-wrapper">
					<Paginator
						pageCount={this.props.configReducer.pages}
						onPageChange={this.props.onPageChange}/>
				</div>
				*/}
			</div>
		);
	}
}

export default MainTable;
