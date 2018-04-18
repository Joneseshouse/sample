import React from 'react';
import PropTypes from 'prop-types';
import Link from 'react-router/lib/Link';
import Tools from 'helpers/Tools';
/*
import {
	TableRightTool
} from 'utils/components/table/TableComponents';
*/
import TableRightTool from '../mainTableRightTool/MainTableRightTool';

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
		if(!Tools.isAdmin) return null;
		return (
			<TableRightTool
				row={row}
				allowRemove={this.state.allowRemove}
				toggleModal={() => this.props.toggleModal(row.id, 'mainModal')}
				togglePrintModal={() => this.props.toggleModal(row.receipt_id, 'printModal')}
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

	_renderForUser(row){
		if(!Tools.isAdmin) return null;
		return (
			<td>{row.user_fullname}</td>
		);
	}

	render(){
		const {row, index, orderReducer:{listStatus}} = this.props;
		const listStatusRef = Tools.mapLabels(listStatus);
		return(
			<tr>
				<td>{Tools.dateFormat(row.updated_at)}</td>
				<td>{row.uid}</td>
				<td>{row.admin_fullname}</td>
				{this._renderForUser(row)}
				<td>{row.type_label}</td>
				<td>{row.money_type_label}</td>
				<td className="right-align">₫{Tools.numberFormat(row.income)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.expense)}</td>
				{/*
					<td className="right-align">₫{Tools.numberFormat(row.purchasing)}</td>
				*/}
				<td className="tooltip1">{this._renderNote(row.note)}</td>
				<td>
					{this._renderRightTool(row, index)}
				</td>
			</tr>
		)
	}
}
