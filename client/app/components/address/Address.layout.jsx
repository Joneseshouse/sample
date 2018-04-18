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


class AddressLayout extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
		};
		this._renderAddressDetail = this._renderAddressDetail.bind(this);
	}

	_renderAddressDetail(){
		if(this.props.showMainTable){
			return null;
		}
		return (
			<div>
				<h1><strong>Người gửi</strong></h1>
				<h2>
					Tên: {this.props.addressReducer.obj.from_address}, Số đt: {this.props.addressReducer.obj.from_phone}
				</h2>
				<h1><strong>Người nhận</strong></h1>
				<h2>
					Tên: {this.props.addressReducer.obj.fullname}, Số đt: {this.props.addressReducer.obj.phone}
				</h2>
				<h2>
					Vùng: {this.props.addressReducer.obj.area}
				</h2>
				<h2>
					Địa chỉ: {this.props.addressReducer.obj.address}
				</h2>
			</div>
		);
	}

	_renderContent(){
		if(!this.props.showMainTable){
			return null;
		}
		if(!this.props.dataLoaded){
			return <WaitingMessage/>
		}
		return (
			<div className="non-printable">
				<div className="breadcrumb-container">
					{labels.common.title}
				</div>
				<div className="main-content">
					<MainTable {...this.props}/>
				</div>

				<CustomModal
					open={this.props.mainModal}
					close={() => this.props.toggleModal(null, 'mainModal', false)}
					size="md"
					title="Address manager"
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
					{this._renderAddressDetail()}
					{this._renderContent()}
				</div>
			</NavWrapper>
		);
	}
}

AddressLayout.propTypes = {
	bulkRemove: PropTypes.bool
};

AddressLayout.defaultProps = {
	bulkRemove: true
};

export default AddressLayout;
