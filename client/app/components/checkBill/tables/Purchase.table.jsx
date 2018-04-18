import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import isEqual from 'lodash/isEqual';
import Table from 'rc-table';
import {labels} from '../_data';
import Tools from 'helpers/Tools';
import PurchaseRow from '../components/purchaseRow/PurchaseRow';

class PurchaseTable extends React.Component {
	static propTypes = {
		onChangeBol: PropTypes.func.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
		};
		this._renderPurchase = this._renderPurchase.bind(this);
	}

	_renderPurchase(){
		if(this.props.listItem.length){
			return this.props.listItem.map((row, i) =>{
				return (
					<PurchaseRow
						{...this.props}
						key={i}
						index={i}
						bolCode={row.bill_of_landing.code}
						onChangeBol={this.props.onChangeBol}
						purchase={row}/>
				);
			});
		}
	}

	render(){
		return (
			<table className="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Ảnh</th>
						<th>Tên sản phẩm</th>
						<th className="right-align">S.Lượng</th>
						<th>Thực kiểm</th>
					</tr>
				</thead>
				{this._renderPurchase()}
			</table>
		);
	}
}

export default PurchaseTable;
