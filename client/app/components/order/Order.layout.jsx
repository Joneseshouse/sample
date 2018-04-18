import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import Table from 'rc-table';
import {labels} from './_data';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import WaitingMessage from 'utils/components/WaitingMessage';
import MainTable from './tables/Main.table';
import MainForm from './forms/Main.form';
import FilterForm from './forms/Filter.form';
import StatusFilter from './components/statusFilter/StatusFilter';
import UserOrderLog from 'components/userOrderLog/UserOrderLog';
import UserProfile from 'components/auth/components/UserProfile';


class OrderLayout extends React.Component {
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
		return (
			<div>
				<div className="breadcrumb-container">
					{labels.common.title}
				</div>


				<div className="main-content">
					<div>
						<FilterForm
							onSubmit={this.props.onFilter}
							labels={labels.filterForm}
							submitTitle="Tìm kiếm">
						</FilterForm>
						<br/>
						<StatusFilter
							listStatus={this.props.orderReducer.listStatus}
							type={this.props.params.type}
							/>
						<br/>
						<MainTable {...this.props} listItem={this.props.orderReducer.list}/>
					</div>
				</div>
				<CustomModal
					open={this.props.mainModal}
					close={() => this.props.toggleModal(null, 'mainModal', false)}
					size="md"
					title="Order manager"
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
										// target: 'order',
										order_id: this.props.itemId
									}
								}
								/>
						</div>
					</div>
				</CustomModal>

				<CustomModal
					open={this.props.userDetailModal}
					close={() => this.props.toggleModal(null, 'userDetailModal', false)}
					size="md"
					title="Thông tin khách hàng"
					>
					<div>
						<div className="custom-modal-content">
							<UserProfile data={this.props.userDetailData}/>
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

export default OrderLayout;
