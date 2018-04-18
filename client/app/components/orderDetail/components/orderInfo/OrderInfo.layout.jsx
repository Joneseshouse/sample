import React from 'react';
import PropTypes from 'prop-types';
import Tools from 'helpers/Tools';
import CustomModal from 'utils/components/CustomModal';
import AddressForm from './forms/Address.form';
import StatusForm from './forms/Status.form';
import AdminForm from './forms/Admin.form';

import {labels} from '../../_data';
import {ADMIN_ROLES} from 'app/constants';

class OrderInfoLayout extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
	    };
	    this._renderAddressForm = this._renderAddressForm.bind(this);
	    this._renderStatusForm = this._renderStatusForm.bind(this);
	    this._renderBuyer = this._renderBuyer.bind(this);
	    this._renderEditBuyer = this._renderEditBuyer.bind(this);
	    this._renderEditStatus = this._renderEditStatus.bind(this);
	}

	_renderEditBuyer(){
		if(Tools.isAdmin){
			return (
				<span>
					&nbsp;
					<span
						onClick={() => this.props.toggleModal(null, 'adminModal')}
						className="glyphicon glyphicon-pencil pointer blue"></span>
				</span>
			);
		}else{
			return null;
		}
	}

	_renderEditStatus(){
		if(Tools.isAdmin){
			return (
				<span>
					&nbsp;
					<span
						onClick={() => this.props.toggleModal(null, 'statusModal')}
						className="glyphicon glyphicon-pencil pointer blue"></span>
				</span>
			);
		}else{
			return null;
		}
	}

	_renderBuyer(){
		if(!this.props.data.admin_id){
			return <span>Chưa gán</span>
		}else{
			return <span>{this.props.data.admin_full_name}</span>
		}
	}
	_renderConfirmer(){
		if(!this.props.data.confirm_admin_id || this.props.data.status === "new"){
			return <span>Đơn hàng chưa được duyệt</span>
		}else{
			return <span>{this.props.data.confirm_full_name}</span>
		}
	}


	_renderMainTable(){
		let status = this.props.data.status;
		let listStatus = this.props.listStatus;
		if(Tools.isExistInArray(listStatus, status, 1) && !Tools.isAdmin){
			return (
				<table className="table table-bordered">
					<tbody>
						<tr>
							<td>
								<strong>
									{this.props.data.customer_id}:
								</strong>&nbsp;
								{this.props.data.customer_full_name}
							</td>
							<td>
								<a href={"tel:" + this.props.data.customer_phone}>
									{this.props.data.customer_phone}
								</a>
							</td>
							<td>
								<a href={"mailto:" + this.props.data.customer_email}>
									{this.props.data.customer_email}
								</a>
							</td>
						</tr>
						<tr>
							<td>
								<strong>Ngày tạo:</strong>&nbsp;
								{Tools.dateFormat(this.props.data.created_at)}
							</td>
							<td>
								<strong>Mã đơn:</strong> {this.props.data.uid}
							</td>
							<td>
								<strong>Trạng thái:</strong>
								&nbsp;
								{Tools.mapLabels(this.props.listStatus)[this.props.data.status]}
								{this._renderEditStatus()}
							</td>
						</tr>
						<tr>
							<td>
								<strong>NV mua hàng:</strong>
								&nbsp;
								{this._renderBuyer()}
								{this._renderEditBuyer()}
							</td>
							<td>
								<strong>Địa chỉ:</strong>
								&nbsp;
								<strong>
									<em>
										{this.props.data.address_code}
									</em>
								</strong>
								&nbsp;&rarr;&nbsp;
								{this.props.data.address_name}
								&nbsp;
							</td>
							<td>
								<strong>Tổng cộng:</strong> ₫ {Tools.numberFormat(parseFloat(Math.floor(this.props.data.total)))}
							</td>
						</tr>
					</tbody>
				</table>
			);
		}
		return (
			<table className="table table-bordered">
				<tbody>
					<tr>
						<td>
							<strong>
								{this.props.data.customer_id}:
							</strong>&nbsp;
							{this.props.data.customer_full_name}
						</td>
						<td>
							<a href={"tel:" + this.props.data.customer_phone}>
								{this.props.data.customer_phone}
							</a>
						</td>
						<td>
							<a href={"mailto:" + this.props.data.customer_email}>
								{this.props.data.customer_email}
							</a>
						</td>
					</tr>
					<tr>
						<td>
							<strong>Ngày tạo:</strong>&nbsp;
							{Tools.dateFormat(this.props.data.created_at)}
						</td>
						<td>
							<strong>Mã đơn:</strong> {this.props.data.uid}
						</td>
						<td>
							<strong>Trạng thái:</strong>
							&nbsp;
							{Tools.mapLabels(this.props.listStatus)[this.props.data.status]}
							{this._renderEditStatus()}
						</td>
					</tr>
					<tr>
						<td>
							<strong>NV mua hàng:</strong>
							&nbsp;
							{this._renderBuyer()}
							{this._renderEditBuyer()}
						</td>
						<td>
							<strong>Địa chỉ:</strong>
							&nbsp;
							<strong>
								<em>
									{this.props.data.address_code}
								</em>
							</strong>
							&nbsp;&rarr;&nbsp;
							{this.props.data.address_name}
							&nbsp;
							<span
								onClick={() => this.props.toggleModal(null, 'addressModal')}
								className="glyphicon glyphicon-pencil pointer blue"></span>
						</td>
						<td>
							<strong>NV duyệt hàng:</strong>
							&nbsp;
							{this._renderConfirmer()}
						</td>
					</tr>
					<tr>
						<td>
							<div>
								<strong>Tổng tiền đơn hàng:</strong> ￥{Tools.numberFormat(this.props.data.amount)}
							</div>
							<div>
								<strong>Tổng tiền đơn hàng:</strong> ₫{Tools.numberFormat(this.props.data.amount * this.props.data.rate)}
							</div>
						</td>
						<td>
							<div>
								<strong>Tổng phí dịch vụ:</strong> ￥{Tools.numberFormat(this.props.data.order_fee)}
							</div>
							<div>
								<strong>Tổng phí vận chuyển:</strong> ₫{Tools.numberFormat(this.props.data.delivery_fee)}&nbsp;
								<span
									onClick={this.props.onUpdateDeliveryFeeUnit}
									className="glyphicon glyphicon-refresh pointer blue"></span>
							</div>
							<div>
								<strong>Tổng ship nội địa:</strong> ￥{Tools.numberFormat(this.props.data.inland_delivery_fee_raw)}
							</div>
						</td>
						<td>
							<strong>Tổng cộng:</strong> ₫ {Tools.numberFormat(parseFloat(Math.floor(this.props.data.total)))}
						</td>
					</tr>
				</tbody>
			</table>
		);
	}

	_renderAddressForm(){
		return(
			<CustomModal
				open={this.props.addressModal}
				close={() => this.props.toggleModal(null, 'addressModal', false)}
				size="md"
				title="Cập nhật địa chỉ nhận hàng"
				>
				<div>
					<div className="custom-modal-content">
						<AddressForm
							onSubmit={this.props.onChangeAddress}
							labels={labels.addressForm}
							submitTitle="Save">

							<button
								type="button"
								className="btn btn-warning cancel"
								onClick={() => this.props.toggleModal(null, 'addressModal', false)}>
								<span className="glyphicon glyphicon-remove"></span> &nbsp;
								Cancel
							</button>
						</AddressForm>
					</div>
				</div>
			</CustomModal>
		);
	}

	_renderStatusForm(){
		return(
			<CustomModal
				open={this.props.statusModal}
				close={() => this.props.toggleModal(null, 'statusModal', false)}
				size="md"
				title="Cập nhật trạng thái"
				>
				<div>
					<div className="custom-modal-content">
						<StatusForm
							onSubmit={this.props.onChangeStatus}
							labels={labels.statusForm}
							submitTitle="Save">

							<button
								type="button"
								className="btn btn-warning cancel"
								onClick={() => this.props.toggleModal(null, 'statusModal', false)}>
								<span className="glyphicon glyphicon-remove"></span> &nbsp;
								Cancel
							</button>
						</StatusForm>
					</div>
				</div>
			</CustomModal>
		);
	}

	_renderAdminForm(){
		return(
			<CustomModal
				open={this.props.adminModal}
				close={() => this.props.toggleModal(null, 'adminModal', false)}
				size="md"
				title="Cập nhật trạng thái"
				>
				<div>
					<div className="custom-modal-content">
						<AdminForm
							onSubmit={this.props.onChangeAdmin}
							labels={labels.adminForm}
							submitTitle="Save">

							<button
								type="button"
								className="btn btn-warning cancel"
								onClick={() => this.props.toggleModal(null, 'adminModal', false)}>
								<span className="glyphicon glyphicon-remove"></span> &nbsp;
								Cancel
							</button>
						</AdminForm>
					</div>
				</div>
			</CustomModal>
		);
	}

	render() {
		return (
			<div>
				{this._renderMainTable()}
				{this._renderAddressForm()}
				{this._renderStatusForm()}
				{this._renderAdminForm()}
			</div>
		);
	}
}

export default OrderInfoLayout;
