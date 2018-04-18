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
import ComplainForm from './forms/Complain.form';
import StatusFilter from './components/statusFilter/StatusFilter';
import WaitingMessage from 'utils/components/WaitingMessage';
import Select from 'react-select';
import {TableFilter} from 'utils/components/table/TableComponents';
import CustomDateRangePicker from 'utils/components/CustomDateRangePicker';


class BillOfLandingLayout extends React.Component {
	static propTypes = {
		bulkRemove: PropTypes.bool
	};
	static defaultProps = {
		bulkRemove: true
	};

	constructor(props) {
		super(props);
		this.state = {
			landingStatusFilter: 'all',
			woodenBoxFilter: 'all',
			userFilter: 'all',
			startDate: null,
			endDate: null,
			focusedInput: null
		};
		this._renderFilter = this._renderFilter.bind(this);
		this._renderUserFilter = this._renderUserFilter.bind(this);
	}

	_renderUserFilter(){
		if(!Tools.isAdmin) return null;
		return (
			<div className="col-md-2">
				<Select
					name="landing_user_filter"
					valueKey="id"
					labelKey="title"
					value={this.state.userFilter}
					options={this.props.billOfLandingReducer.listUser}
                    onChange={
                        value => {
                            this.props.setUserFilter(value.id);
                            this.setState({userFilter: value.id})
                        }
                    }/>
			</div>
		);
	}

	_renderFilter(){
		return (
			<div className="row">
				<div className="col-md-3">
					<TableFilter
						onFilter={this.props.onFilter}
						onRemove={this.props.onRemove}
						bulkRemove={true}
						/>
				</div>
				<div className="col-md-2">
					<Select
						name="landing_status_filter"
						valueKey="id"
						labelKey="title"
						value={this.state.landingStatusFilter}
						options={this.props.billOfLandingReducer.listLandingStatusFilter}
                        onChange={
                            value => {
                                this.props.setLandingStatusFilter(value.id);
                                this.setState({landingStatusFilter: value.id})
                            }
                        }/>
				</div>
				<div className="col-md-2">
					<Select
						name="wooden_box"
						valueKey="id"
						labelKey="title"
						value={this.state.woodenBoxFilter}
						options={this.props.billOfLandingReducer.listWoodenBoxFilter}
                        onChange={
                            value => {
                                this.props.setWoodenBoxFilter(value.id);
                                this.setState({woodenBoxFilter: value.id})
                            }
                        }/>
				</div>
				{this._renderUserFilter()}
				<div className="col-md-3">
					<CustomDateRangePicker onChange={this.props.setDateFilter}/>
				</div>
			</div>
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
				<StatusFilter params={this.props.params}/>
				<div className="main-content">
					{this._renderFilter()}
					<MainTable {...this.props}/>
				</div>

				<CustomModal
					open={this.props.mainModal}
					close={() => this.props.toggleModal(null, 'mainModal', false)}
					size="md"
					title="Quản lý vận đơn"
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
					open={this.props.complainModal}
					close={() => this.props.toggleModal(null, 'complainModal', false)}
					size="lg"
					title="Khiếu nại"
					>
					<div>
						<div className="custom-modal-content">
							<ComplainForm
								onSubmit={this.props.onEditComplain}
								onResetComplain={this.props.onResetComplain}
								id={this.props.id}
								labels={labels.complainForm}
								submitTitle="Save">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'complainModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</ComplainForm>
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

export default BillOfLandingLayout;
