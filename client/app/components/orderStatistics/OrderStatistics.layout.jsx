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
// import FilterForm from './forms/Filter.form';
import FilterForm from 'components/order/forms/Filter.form';
import StatusFilter from './components/statusFilter/StatusFilter';
import UserProfile from 'components/auth/components/UserProfile';


class OrderStatisticsLayout extends React.Component {
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
							reducer='orderStatisticsReducer'
							submitTitle="Tìm kiếm">
						</FilterForm>
						<br/>
						<MainTable
							{...this.props}
							total={this.props.total}
							listItem={this.props.orderStatisticsReducer.list}/>
					</div>
				</div>
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

export default OrderStatisticsLayout;
