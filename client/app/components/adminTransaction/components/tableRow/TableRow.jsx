import React from 'react';
import PropTypes from 'prop-types';
import Link from 'react-router/lib/Link';
import {API_URL} from 'app/constants';
import Tools from 'helpers/Tools';
import {
	TableRightTool
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
		if(!Tools.isAdmin) return null;
		return (
			<TableRightTool
				allowRemove={this.state.allowRemove}
				toggleModal={() => this.props.toggleModal(row.id, 'mainModal')}
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

	render(){
		const {row, index, orderReducer:{listStatus}} = this.props;
		const listStatusRef = Tools.mapLabels(listStatus);
		return(
			<tr>
				<td>{Tools.dateFormat(row.updated_at)}</td>
				<td>{row.admin_fullname}</td>
				<td>{row.user_fullname}</td>
				<td>{row.type_label}</td>
				<td>{row.uid}</td>
				<td className="right-align">₫{Tools.numberFormat(row.amount)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.credit_balance)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.liabilities)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.balance)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.credit_deposit)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.debt_deposit)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.export_bol_amount)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.delivery_fee)}</td>
				<td className="right-align">₫{Tools.numberFormat(row.discounts)}</td>
				<td className="center-align">
					<span className="glyphicon glyphicon-picture"/>
				</td>
				<td className="tooltip1">{this._renderNote(row.note)}</td>
				<td>
					{this._renderRightTool(row, index)}
				</td>
			</tr>
		)
	}
}
