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
import WaitingMessage from 'utils/components/WaitingMessage';

import Select from 'react-select';
import CustomDateRangePicker from 'utils/components/CustomDateRangePicker';


class CollectBolLayout extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			adminId: 0
		};
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
					<div className="row">
						<div className="col-md-6">
							<CustomDateRangePicker onChange={this.props.setFilterDateParam}/>
						</div>
						<div className="col-md-6">
							<Select
								name="admin_id"
								valueKey="id"
								labelKey="title"
								value={this.state.adminId}
								options={this.props.collectBolReducer.listAdmin}
								onChange={value => {this.props.setFilterAdminParam(value.id);this.setState({adminId: value.id})}}/>
						</div>
					</div>
					<MainTable {...this.props}/>
				</div>

				<CustomModal
					open={this.props.mainModal}
					close={() => this.props.toggleModal(null, 'mainModal', false)}
					size="md"
					title="CollectBol manager"
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

CollectBolLayout.propTypes = {
	bulkRemove: PropTypes.bool
};

CollectBolLayout.defaultProps = {
	bulkRemove: true
};

export default CollectBolLayout;
