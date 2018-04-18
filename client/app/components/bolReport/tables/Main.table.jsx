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
import 'rc-table/assets/index.css';
import 'rc-table/assets/bordered.css';


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
		const sameList = Tools.isSameCollection(this.props.bolReportReducer.list, newProps.bolReportReducer.list);
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
		let groupTitles = [];
		let objGroups = {};
		forEach(headingData, (value, key) => {
			let data = {
				title: value.title,
				dataIndex: key,
				key,
				render: (value, row, index) => Tools.tableData(labels.mainForm, row, key, this.props.params)
			}
			if(value.fixed){
				data.fixed = value.fixed;
			}

			if(value.group){
				if(groupTitles.indexOf(value.group) === -1){
					groupTitles.push(value.group);
					objGroups[value.group] = {title: value.group, children: []};
					columnData.push(objGroups[value.group]);
				}
				objGroups[value.group].children.push(data);
			}else{
				columnData.push(data);
			}
		});

		const columns = [
			...columnData
		];

		return (
			<div>
				<Table
					className="bordered"
					columns={columns}
					rowKey={record => record.id}
					data={this.props.bolReportReducer.list}/>
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
