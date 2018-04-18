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
	TableFilter,
	TableCheckAll,
	TableRightTool,
	TableCheckBox
} from 'utils/components/table/TableComponents';


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
			allowRemove: true,
			allowUpdate: false,
			allowAdd: true,
			forceUpdate: false
		};
		this._renderCheckBox = this._renderCheckBox.bind(this);
		this._renderRightTool = this._renderRightTool.bind(this);
		this._renderCheckAll = this._renderCheckAll.bind(this);
		this._renderAddButton = this._renderAddButton.bind(this);
	}

	shouldComponentUpdate(newProps, newState) {
		const sameList = Tools.isSameCollection(
			this.props.exportBillDetailReducer.listSelected,
			newProps.exportBillDetailReducer.listSelected
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
			<TableRightTool
				allowUpdate={this.state.allowUpdate}
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
				<Table
					columns={columns}
					rowKey={record => record.id}
					data={this.props.exportBillDetailReducer.listSelected}
					/>
			</div>
		);
	}
}

export default PureTable;
