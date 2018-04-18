import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import Table from 'rc-table';
import {labels} from './_data';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import MainTable from './tables/Main.table';
import PurchaseTable from './tables/Purchase.table';
import DepositForm from './forms/Deposit.form';
import WaitingMessage from 'utils/components/WaitingMessage';
import ListNote from 'components/orderDetail/components/listNote/ListNote';


class CheckBillLayout extends React.Component {
	static propTypes = {
		bulkRemove: PropTypes.bool
	};
	static defaultProps = {
		bulkRemove: true
	};

	constructor(props) {
		super(props);
		this.state = {};
	}

	_renderContent(){
		if(!this.props.dataLoaded){
			return <WaitingMessage/>
		}
		const {listPurchase} = this.props.checkBillReducer;
		return (
			<div>
				<div className="breadcrumb-container">
					{labels.common.title}
				</div>
				<div className="main-content">
					<MainTable
						{...this.props}
						listItem={this.props.checkBillReducer.list}/>
				</div>

				<CustomModal
					open={this.props.mainModal}
					close={() => this.props.toggleModal(null, 'mainModal', false)}
					size="lg"
					title={"Vận đơn: " + this.props.checkBillReducer.keyword + ' / ' + (listPurchase.length?listPurchase[0].admin_fullname:'') + ' / ' + (listPurchase.length?listPurchase[0].customer_care_staff_full_name:'')}
					>
					<div>
						<div className="custom-modal-content">
							<PurchaseTable
								{...this.props}
								onChangeBol={this.props.onChangeBol}
								listItem={this.props.checkBillReducer.listPurchase}
								billOfLandingCode={this.props.checkBillReducer.keyword}/>

							<div className="row custom-modal-footer">
								<div className="col-md-6 cancel">
									<button
										type="button"
										className="btn btn-warning cancel"
										onClick={() => this.props.toggleModal(null, 'mainModal', false)}>
										<span className="glyphicon glyphicon-remove"></span> &nbsp;
										Cancel
									</button>
								</div>
							</div>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.depositModal}
					close={() => this.props.toggleModal(null, 'depositModal', false)}
					size="md"
					title={"Vận đơn: " + this.props.checkBillReducer.keyword}
					>
					<div>
						<div className="custom-modal-content">
							<DepositForm
								onSubmit={this.props.onChange}
								labels={labels.depositForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'depositModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</DepositForm>
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

export default CheckBillLayout;
