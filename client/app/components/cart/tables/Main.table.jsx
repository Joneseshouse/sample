import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import {labels} from '../_data';
import Tools from 'helpers/Tools';


class MainTable extends React.Component {
	constructor(props) {
		super(props);
		this.state = {};
		this._renderCheckBox = this._renderCheckBox.bind(this);
		this._renderRightTool = this._renderRightTool.bind(this);
		this._renderCheckAll = this._renderCheckAll.bind(this);
		this._renderFilter = this._renderFilter.bind(this);
		this._renderAddButton = this._renderAddButton.bind(this);
		this._renderItem =  this._renderItem.bind(this);
	}

	_renderRightTool(value, row, index){
		return (
			<TableRightTool
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

	_renderShop(){
		if(this.props.cartReducer.list.shops){
			return this.props.cartReducer.list.shops.map((row, i) =>{
				return (
					<tbody key={i}>
						<tr className="cyan-bg">
							<td colSpan={4} className="white">
								<strong>
									<span
										onClick={() => this.props.onCheckAllShop(i)}
										className="glyphicon glyphicon-ok"></span>
									&nbsp;
									&nbsp;
									&nbsp;
									&nbsp;
									{row.title}
								</strong>
							</td>
							<td></td>
							<td></td>
							<td className="white right-align">
								<div>
									<strong>
										{Tools.numberFormat(row.total)} ￥
									</strong>
								</div>
								<div>
									<strong>
										{Tools.numberFormat(row.total * this.props.cartReducer.list.rate)} ₫
									</strong>
								</div>
							</td>
							<td colSpan={2}></td>
						</tr>
						{this._renderItem(row.items, i)}
					</tbody>
				);
			});
		}
	}

	_renderMessage(input){
		let message = input;
		if(!message){
			// message = 'Chưa cập nhật';
			return null;
		}
		return (
			<div>
				<strong>Ghi chú: </strong>{message}
			</div>
		);
	}

	_renderItem(listItem, shopIndex){
		return listItem.map((row, i) => {
			return (
				<tr key={i}>
					<td>
						<input
							type="checkbox"
							checked={row.checked || false}
							onChange={(event) => this.props.onCheck(row.id, shopIndex, event.target.checked)}
							/>
					</td>
					<td>{row.stt}</td>
					<td className="grid-thumbnail">
						<img
							onClick={() => this.props.toggleModal(row.avatar, 'previewModal')}
							src={row.avatar}
							width="100%"/>
					</td>
					<td>
						<div className="wrap" style={{width: 400}}>
							<a href={row.url} target="_blank">
								{row.title}
							</a>
						</div>
						<div className="cyan">
							{row.properties}
						</div>
						<div>
							<em>
								{this._renderMessage(row.message)}
							</em>
						</div>
					</td>
					<td className="right-align">
						<div>
							{Tools.numberFormat(row.unit_price)} ￥
						</div>
						<div>
							{Tools.numberFormat(row.unit_price * row.rate)} ₫
						</div>
					</td>
					<td className="right-align">{row.quantity}</td>
					<td className="right-align">
						<div>
							{Tools.numberFormat(parseFloat(row.unit_price) * parseInt(row.quantity))} ￥
						</div>
						<div>
							{Tools.numberFormat(parseFloat(row.unit_price) * parseInt(row.quantity) * row.rate)} ₫
						</div>
					</td>
					<td>
						<span
							onClick={() => this.props.toggleModal(row.id, 'mainModal')}
							className="glyphicon glyphicon-pencil"></span>
					</td>
					<td>
						<span
							onClick={() => this.props.onRemove(row.id)}
							className="glyphicon glyphicon-remove red"></span>
					</td>
				</tr>
			);
		});
	}

	_renderTotal(fixed=false){
		return (
			<tfoot className={fixed?"fixed-footer":""}>
				<tr className="brown-bg">
					<td className="cart-checkbox">
						<span
							onClick={this.props.onCheckAll}
							className="glyphicon glyphicon-ok"></span>
					</td>
					<td className="cart-stt">&nbsp;</td>
					<td>
						<button
							type="button"
							className="btn btn-info btn-block"
							onClick={()=>this.props.onSaveDraft()}>
							<span className="glyphicon glyphicon-floppy-disk"></span>&nbsp;
							Lưu
						</button>
					</td>
					<td>
						<button
							type="button"
							className="btn btn-success"
							onClick={this.props.onAddOrder}>
							<span className="glyphicon glyphicon-send"></span>&nbsp;
							Gửi đơn
						</button>
						&nbsp;
						&nbsp;
						<button
							type="button"
							className="btn btn-danger"
							onClick={()=>this.props.onRemove()}>
							<span className="glyphicon glyphicon-remove"></span>&nbsp;
							Xoá
						</button>
					</td>
					<td className="right-align middle-align">
						<div>
							<span>
							Số link:&nbsp;
							</span>
							<strong>
								{Tools.numberFormat(this.props.cartReducer.list.links)}
							</strong>
						</div>
					</td>
					<td className="right-align middle-align">
						<span>
							Số lượng:&nbsp;
							</span>
						<strong>
							{Tools.numberFormat(this.props.cartReducer.list.quantity)}
						</strong>
					</td>
					<td className="right-align">
						<table>
							<tbody>
								<tr>
									<td>Thành tiền: &nbsp;</td>
									<td>
										<div>
											<strong>
												{Tools.numberFormat(this.props.cartReducer.list.total)} ￥
											</strong>
										</div>
										<div>
											<strong>
												{Tools.numberFormat(this.props.cartReducer.list.total * this.props.cartReducer.list.rate)} ₫
											</strong>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
					<td colSpan={2} className="right-align">
						<table>
							<tbody>
								<tr>
									<td>Chọn: &nbsp;</td>
									<td>
										<div className="red strong">
											{Tools.numberFormat(this.props.cartReducer.totalSelected?this.props.cartReducer.totalSelected:this.props.cartReducer.list.total)} ￥
										</div>
										<div className="red strong">
											{Tools.numberFormat(this.props.cartReducer.totalSelectedWithRate?this.props.cartReducer.totalSelectedWithRate:this.props.cartReducer.list.total * this.props.cartReducer.list.rate)} ₫
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tfoot>
		);
	}

	render(){
		return (
			<table className="table table-striped table-hover table-bordered" style={{marginBottom: 36}}>
				<thead>
					<tr>
						<th>
							<span
								onClick={this.props.onCheckAll}
								className="glyphicon glyphicon-ok"></span>
						</th>
						<th>Stt</th>
						<th style={{width: 80}}>Ảnh</th>
						<th>Tên sản phẩm</th>
						<th className="right-align">Đơn giá</th>
						<th className="right-align">Số lượng</th>
						<th className="right-align">Thành tiền</th>
						<th colSpan={2}></th>
					</tr>
				</thead>
				{this._renderShop()}
				{this._renderTotal(true)}
			</table>
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
