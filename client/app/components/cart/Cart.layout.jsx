import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import {labels} from './_data';
import {STATIC_URL} from 'app/constants'
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import MainTable from './tables/Main.table';
import MainForm from './forms/Main.form';
import UploadForm from './forms/Upload.form';
import FilterForm from './forms/Filter.form';
import ManualForm from 'components/orderDetail/forms/Main.form';


class CartLayout extends React.Component {
	static propTypes = {
		bulkRemove: PropTypes.bool
	};
	static defaultProps = {
		bulkRemove: true
	};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderContent = this._renderContent.bind(this);
		this._renderBreadcrumb = this._renderBreadcrumb.bind(this);
	}

	_renderBreadcrumb(){
		if(!this.props.params.auth) return null;
		return (
			<div className="breadcrumb-container">
				Giỏ hàng
			</div>
		);
	}

	_renderContent(){
		return (
			<div>
				{this._renderBreadcrumb()}
				<div className="main-content">
					<div className="row">
						<div className="col-md-12">
							<FilterForm
								onSubmit={this.props.onFilter}
								labels={labels.filterForm}
								submitTitle="Tìm kiếm"/>
						</div>
					</div>
					<div className="row">
						<div className="col-md-3">
							<a href={STATIC_URL + 'samples/cart-upload.xlsx'}>File upload mẫu</a>
						</div>
						<div className="col-md-7">
							<UploadForm
								onSubmit={this.props.onUpload}
								labels={labels.mainForm}
								submitTitle="Upload"/>
						</div>
						<div className="col-md-2">
							<button
								type="button"
								className="btn btn-primary btn-block"
								onClick={()=>this.props.toggleModal(null, 'manualModal')}>
								<span className="glyphicon glyphicon-plus"></span>&nbsp;
								Thêm thủ công
							</button>
						</div>
					</div>
					<MainTable {...this.props}/>
				</div>
				<CustomModal
					open={this.props.mainModal}
					close={() => this.props.toggleModal(null, 'mainModal', false)}
					size="md"
					title="Cart manager"
					>
					<div>
						<div className="manual-modal-content">
							<MainForm
								onSubmit={this.props.onChange}
								labels={labels.mainForm}
								dataReducer="cartReducer"
								dataTarget="obj"
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
					open={this.props.manualModal}
					close={() => this.props.toggleModal(null, 'manualModal', false)}
					size="md"
					title="Thêm sản phẩm"
					>
					<div>
						<div className="manual-modal-content">
							<ManualForm
								onSubmit={this.props.onManualAdd}
								labels={labels.manualForm}
								dataReducer="cartReducer"
								dataTarget="obj"
								submitTitle="Thêm mới">

								<button
									type="button"
									className="btn btn-warning cancel"
									onClick={() => this.props.toggleModal(null, 'manualModal', false)}>
									<span className="glyphicon glyphicon-remove"></span> &nbsp;
									Cancel
								</button>
							</ManualForm>
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
			</div>
		);
	}

	render() {
		if(this.props.params.auth){
			return (
				<NavWrapper data-location={this.props.location} data-user={this.props.authReducer}>
					<div>
						{this._renderContent()}
					</div>
				</NavWrapper>
			);
		}
		return this._renderContent();
	}
}

export default CartLayout;
