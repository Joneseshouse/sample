import React from 'react';
import PropTypes from 'prop-types';
import { SubmissionError, reset } from 'redux-form';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import {apiUrls, labels} from '../../_data';
import Tools from 'helpers/Tools';
import OrderInfoLayout from './OrderInfo.layout';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class OrderInfo extends React.Component {
	static propTypes = {
		orderId: PropTypes.number.isRequired,
		data: PropTypes.object.isRequired,
		listStatus: PropTypes.array.isRequired
	};
	static defaultProps = {
		data: {
			customer_id: null,
			customer_full_name: null,
			customer_phone: null,
			customer_email: null,

			address_id: null,
			address_code: null,
			address_name: null,

			created_at: null,
			uid: null,
			status: null,
			total: null
		}
	};

	constructor(props) {
		super(props);
		this.state = {
			addressModal: false,
			statusModal: false,
			adminModal: false
	    };
	    this.toggleModal = this.toggleModal.bind(this);
	    this.handleChangeAddress = this.handleChangeAddress.bind(this);
	    this.handleChangeStatus = this.handleChangeStatus.bind(this);
	    this.handleChangeAdmin = this.handleChangeAdmin.bind(this);
	    this.handleUpdateDeliveryFeeUnit = this.handleUpdateDeliveryFeeUnit.bind(this);
	}

	toggleModal(id=null, state, open=true){
		let newState = {};
		newState[state] = open;
		switch(state){
			case 'addressModal':
			case 'statusModal':
			case 'adminModal':
				this.props.orderDetailAction('obj', {
					...this.props.data
				});
				this.setState(newState);
			break;
		}
	}

	handleChangeAddress(eventData, dispatch){
		try{
			const params = {
				id: this.props.orderId,
				address_id: eventData.address_id
			};
			return Tools.apiCall(apiUrls.edit, params).then((result) => {
		    	if(result.success){
		    		this.props.setInitData(result);
		    		this.props.orderDetailAction('obj', {
		    			address_id: result.data.address_id,
		    			address_code: result.data.address_code,
		    			address_name: result.data.address_name
		    		});
		    		dispatch(reset('AddressForm'));
		    		this.toggleModal(null, 'addressModal', false);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	handleUpdateDeliveryFeeUnit(){
		try{
			const confirm = window.confirm('Bạn có muốn cập nhật lại giá vận chuyển?');
			if(!confirm) return;
			const params = {
				id: this.props.orderId
			};
			return Tools.apiCall(apiUrls.updateDeliveryFeeUnit, params).then((result) => {
		    	if(result.success){
		    		this.props.setInitData(result);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	handleChangeStatus(eventData, dispatch){
		try{
			const params = {
				id: this.props.orderId,
				status: eventData.status
			};
			return Tools.apiCall(apiUrls.edit, params).then((result) => {
		    	if(result.success){
		    		this.props.orderDetailAction('obj', {
		    			status: result.data.status
		    		});
		    		dispatch(reset('StatusForm'));
		    		this.toggleModal(null, 'statusModal', false);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	handleChangeAdmin(eventData, dispatch){
		try{
			const params = {
				id: this.props.orderId,
				admin_id: eventData.admin_id
			};
			return Tools.apiCall(apiUrls.edit, params).then((result) => {
		    	if(result.success){
		    		this.props.orderDetailAction('obj', {
		    			admin_id: result.data.admin_id,
		    			admin_full_name: result.data.admin_full_name
		    		});
		    		dispatch(reset('AdminForm'));
		    		this.toggleModal(null, 'adminModal', false);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
	    	console.error(error);
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	render() {
		return (
			<OrderInfoLayout
				{...this.props}
				addressModal={this.state.addressModal}
				statusModal={this.state.statusModal}
				adminModal={this.state.adminModal}
				toggleModal={this.toggleModal}
				onChangeAddress={this.handleChangeAddress}
				onChangeStatus={this.handleChangeStatus}
				onChangeAdmin={this.handleChangeAdmin}
				onUpdateDeliveryFeeUnit={this.handleUpdateDeliveryFeeUnit}
				/>
		);
	}
}

export default OrderInfo;
