import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import indexOf from 'lodash/indexOf';
import forEach from 'lodash/forEach';
import Table from 'rc-table';
import {labels} from './_data';
import {labels as cartLabels} from 'components/cart/_data';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import MainTable from './tables/Main.table';
import MainForm from './forms/Main.form';
import DeliveryFeeForm from './components/purchaseRow/forms/DeliveryFee.form';
import RealAmountForm from './components/purchaseRow/forms/RealAmount.form';
import UnitPriceForm from './components/orderItemRow/forms/UnitPrice.form';
import PurchaseCodeForm from './components/purchaseInfo/forms/PurchaseCode.form';
import PurchaseNoteForm from './components/purchaseInfo/forms/PurchaseNote.form';
// import BillOfLandingForm from './components/purchaseInfo/forms/BillOfLanding.form';
import BillOfLandingForm from 'components/billOfLanding/forms/Main.form';
import CartMainForm from 'components/cart/forms/Main.form';
import WaitingMessage from 'utils/components/WaitingMessage';
import OrderInfo from './components/orderInfo/OrderInfo';
import ListNote from './components/listNote/ListNote';
import UserOrderLog from 'components/userOrderLog/UserOrderLog';


class OrderDetailLayout extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderTable = this._renderTable.bind(this);
	}

	_renderTable(){
		return (
			<MainTable
				{...this.props}
				rate={this.props.rate}
				order_fee_factor={this.props.order_fee_factor}
				listItem={this.props.orderDetailReducer.list}/>
		);
	}

	_renderContent(){
		if(!this.props.dataLoaded){
			return <WaitingMessage/>
		}

		return (
			<div>
				<div className="breadcrumb-container">
					{labels.common.title}
				</div>
				<div className="main-content">
					<OrderInfo
						orderId={parseInt(this.props.params.id)}
						data={this.props.orderDetailReducer.obj}
						setInitData={this.props.setInitData}
						listStatus={this.props.orderDetailReducer.listStatus}
						/>
					{this._renderTable()}
				</div>

				<CustomModal
					open={this.props.mainModal}
					close={() => this.props.toggleModal(null, 'mainModal', false)}
					size="md"
					title="Thêm sản phẩm"
					>
					<div>
						<div className="custom-modal-content">
							<MainForm
								onSubmit={this.props.onChange}
								labels={labels.mainForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'mainModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</MainForm>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.itemModal}
					close={() => this.props.toggleModal(null, 'itemModal', false)}
					size="md"
					title="Sửa thông tin sản phẩm"
					>
					<div>
						<div className="custom-modal-content">
							<CartMainForm
								onSubmit={this.props.onChangeOrderItem}
								labels={cartLabels.mainForm}
								dataReducer="orderDetailReducer"
								data={this.props.orderDetailReducer}
								displayMessage={false}
								dataTarget="objItem"
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'itemModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</CartMainForm>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.deliveryFeeModal}
					close={() => this.props.toggleModal(null, 'deliveryFeeModal', false)}
					size="md"
					title="Cập nhật phí vận chuyển"
					>
					<div>
						<div className="custom-modal-content">
							<DeliveryFeeForm
								onSubmit={this.props.onChangeDeliveryFee}
								labels={labels.deliveryFeeForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'deliveryFeeModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</DeliveryFeeForm>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.unitPriceModal}
					close={() => this.props.toggleModal(null, 'unitPriceModal', false)}
					size="md"
					title="Cập nhật đơn giá"
					>
					<div>
						<div className="custom-modal-content">
							<UnitPriceForm
								onSubmit={this.props.onChangeUnitPrice}
								labels={labels.unitPriceForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'unitPriceModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</UnitPriceForm>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.realAmountModal}
					close={() => this.props.toggleModal(null, 'realAmountModal', false)}
					size="md"
					title="Cập nhật thanh toán thực"
					>
					<div>
						<div className="custom-modal-content">
							<RealAmountForm
								onSubmit={this.props.onChangeRealAmount}
								labels={labels.realAmountForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'realAmountModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</RealAmountForm>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.purchaseCodeModal}
					close={() => this.props.toggleModal(null, 'purchaseCodeModal', false)}
					size="md"
					title="Cập nhật mã giao dịch"
					>
					<div>
						<div className="custom-modal-content">
							<PurchaseCodeForm
								onSubmit={this.props.onChangePurchaseCode}
								labels={labels.purchaseCodeForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'purchaseCodeModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</PurchaseCodeForm>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.purchaseNoteModal}
					close={() => this.props.toggleModal(null, 'purchaseNoteModal', false)}
					size="md"
					title="Cập nhật ghi chú giao dịch"
					>
					<div>
						<div className="custom-modal-content">
							<PurchaseNoteForm
								onSubmit={this.props.onChangePurchaseNote}
								labels={labels.purchaseNoteForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'purchaseNoteModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</PurchaseNoteForm>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.billOfLandingModal}
					close={() => this.props.toggleModal(null, 'billOfLandingModal', false)}
					size="md"
					title="Cập nhật mã vận đơn"
					>
					<div>
						<div className="custom-modal-content">
							<BillOfLandingForm
								onSubmit={this.props.onChangeBillOfLanding}
								labels={labels.billOfLandingForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'billOfLandingModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</BillOfLandingForm>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.previewModal}
					close={() => this.props.toggleModal(null, 'previewModal', false)}
					size="md"
					title="Ảnh chi tiết"
					>
					<div>
						<div className="custom-modal-content">
							<img src={this.props.itemId} width="100%"/>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.noteModal}
					close={() => this.props.toggleModal(null, 'noteModal', false)}
					size="md"
					title="Danh sách ghi chú"
					>
					<div>
						<div className="custom-modal-content">
							<ListNote
								{...this.props}/>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.logModal}
					close={() => this.props.onToggleLog(null, false)}
					size="md"
					title="Log người dùng"
					>
					<div>
						<div className="custom-modal-content">
							<UserOrderLog
								{...this.props}
								conditions={
									{
										target: 'purchase',
										purchase_id: this.props.itemId
									}
								}
								/>
						</div>
					</div>
				</CustomModal>
			</div>
		);
	}

	render() {
		return (
			<NavWrapper data-location={this.props.location} data-user={this.props.authReducer}>
				<div>
					{this._renderContent()}
				</div>
			</NavWrapper>
		);
	}
}

export default OrderDetailLayout;
