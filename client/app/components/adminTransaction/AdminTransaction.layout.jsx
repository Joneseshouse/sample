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
import MainForm from './forms/Main.form';
import FilterForm from './forms/Filter.form';
import WaitingMessage from 'utils/components/WaitingMessage';


class AdminTransactionLayout extends React.Component {
	static propTypes = {
		bulkRemove: PropTypes.bool
	};
	static defaultProps = {
		bulkRemove: true
	};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderTotalTable = this._renderTotalTable.bind(this);
	}

	_renderTotalTable(){
		const {total} = this.props;
		return null;
		// (
			/*
			<div className="row">
				<div className="col-md-4">
					<table className="table table-striped">
						<tbody>
							<tr>
								<td><strong>Tổng có</strong></td>
								<td>₫{Tools.numberFormat(total.credit_balance)}</td>
							</tr>
							<tr>
								<td><strong>Tổng đang giao dịch</strong></td>
								<td>₫{Tools.numberFormat(total.purchasing_amount)}</td>
							</tr>
							<tr>
								<td><strong>Số dư khả dụng</strong></td>
								<td>₫{Tools.numberFormat(total.balance)}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div className="col-md-4">
					<table className="table table-striped">
						<tbody>
							<tr>
								<td><strong>Tổng nợ</strong></td>
								<td>₫{Tools.numberFormat(total.liabilities)}</td>
							</tr>
							<tr>
								<td><strong>Tổng cọc</strong></td>
								<td>₫{Tools.numberFormat(total.deposit)}</td>
							</tr>
							<tr>
								<td><strong>Tổng vận chuyển</strong></td>
								<td>₫{Tools.numberFormat(total.delivery_fee)}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div className="col-md-4">
					<table className="table table-striped">
						<tbody>
							<tr>
								<td><strong>Dư cọc</strong></td>
								<td>₫{Tools.numberFormat(total.deposit)}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			*/
		// );
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
					<FilterForm
						onSubmit={this.props.onFilter}
						labels={labels.filterForm}
						submitTitle="Tìm kiếm"/>
					<br/>
					{this._renderTotalTable()}
					<MainTable
						{...this.props}
						total={this.props.total}
						listType={this.props.adminTransactionReducer.listType}
						listMoneyType={this.props.adminTransactionReducer.listMoneyType}
						listItem={this.props.adminTransactionReducer.list}/>
				</div>

				<CustomModal
					open={this.props.mainModal}
					close={() => this.props.toggleModal(null, 'mainModal', false)}
					size="md"
					title="AdminTransaction manager"
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

export default AdminTransactionLayout;
