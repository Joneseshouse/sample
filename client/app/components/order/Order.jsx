import React from 'react';
import PropTypes from 'prop-types';
import isEmpty from 'lodash/isEmpty';
import keys from 'lodash/keys';
import values from 'lodash/values';
import filter from 'lodash/filter';
import forEach from 'lodash/forEach';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { SubmissionError, reset } from 'redux-form';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import {apiUrls, labels} from './_data';
import Tools from 'helpers/Tools';
import OrderLayout from './Order.layout';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class Order extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			mainModal: false,
			userDetailModal: false,
			userDetailData: {},
			logModal: false,
			itemId: null,
			bulkRemove: true,
			params: {},
			dataLoaded: store.getState().orderReducer.list.length?true:false
	    };

	    this.list = this.list.bind(this);
	    this.toggleModal = this.toggleModal.bind(this);
	    this.handleFilter = this.handleFilter.bind(this);
	    this.handleCheck = this.handleCheck.bind(this);
	    this.handleCheckAll = this.handleCheckAll.bind(this);
	    this.handleRemove = this.handleRemove.bind(this);
	    this.handleConfirmOrder = this.handleConfirmOrder.bind(this);
	    this.handleChange = this.handleChange.bind(this);
	    this.handlePageChange = this.handlePageChange.bind(this);
	    this.handleToggleLog = this.handleToggleLog.bind(this);
	    this.handleDraftToNew = this.handleDraftToNew.bind(this);
	    this.setRefs = this.setRefs.bind(this);

	    this.filterTimeout = null;
	}

	setInitData(initData){
		this.setRefs(initData);

		let listStatus = this.props.orderReducer.listStatus;
		const totalStatus = initData.extra.total_status;
		forEach(listStatus, (status, index) => {
			if(typeof totalStatus[status.id] !== 'undefined'){
				listStatus[index].total = totalStatus[status.id];
			}
		});
		this.props.orderAction('listStatus', {list: [...listStatus]});
		this.props.orderAction('newList', {list: [...initData.data.items], pages: initData.data._meta.last_page});
		setTimeout(() => {
			this.setState({dataLoaded: true});
		}, 100);
	}

	setRefs(initData){
		const listAddress = Tools.renameColumn(initData.extra.list_address, 'address');
		let listAdmin = Tools.renameColumn(initData.extra.list_admin, 'full_name');
		listAdmin.unshift({id: 0, title: '--- Chọn nhân viên ---'});

		let listUser = Tools.renameColumn(initData.extra.list_user, 'fulltitle');
		listUser.unshift({id: 0, title: '--- Chọn khách hàng ---'});

		const defaultAddressObj = filter(listAddress, {default: true})[0];
		const defaultAdmin = listAdmin.length?listAdmin[0].id:null;
		let defaultAddress = null
		if(defaultAddressObj){
			defaultAddress = defaultAddressObj.id;
		}
		this.props.orderAction('listAddress', {list: [...listAddress]});
		this.props.orderAction('listAdmin', {list: [...listAdmin]});
		this.props.orderAction('listUser', {list: [...listUser]});
		this.props.orderAction('defaultAddress', defaultAddress);
		this.props.orderAction('defaultAdmin', defaultAdmin);
	}

	componentDidMount(){
		document.title = 'Order';
		// if(!this.props.orderReducer.list.length){
			if(window.initData){
		    	if(window.initData.success){
		    		this.setInitData(window.initData);
				}else{
					// Pop message here
				}
			    window.initData = null;
			}else{
				this.list();
			}
		// }
	}

	componentDidUpdate (prevProps) {
		let oldType = prevProps.params.type;
		let newType = this.props.params.type;

		let oldStatus = prevProps.params.status;
		let newStatus = this.props.params.status;
		if (oldType !== newType || oldStatus !== newStatus){
			this.list();
		}
	}

	toggleModal(id=null, state, open=true){
		let newState = {};
		newState[state] = open;

		switch(state){
			case 'mainModal':
				newState.itemId = id;
				if(id && open){
					Tools.apiCall(apiUrls.obj, {id: id}, false).then((result) => {
						if(result.success){
							this.props.orderAction('obj', {
								...result.data
							});
							this.setRefs(result);
							this.setState(newState);
							return;
						}
					});
				}else{
					this.props.orderAction('obj', {...Tools.getInitData(labels.mainForm), address_id: store.getState().orderReducer.defaultAddress});
					this.setState(newState);
				}
			break;
			case 'userDetailModal':
				if(id && open){
					Tools.apiCall(apiUrls.userObj, {id}, false).then((result) => {
						if(result.success){
							newState.userDetailData = result.data;
							this.setState(newState);
						}
					});
				}else{
					this.setState(newState);
				}
			break;
		}
	}

	handleToggleLog(id, open=true){
		let newState = {
			logModal: open,
			itemId: id
		};
		this.setState(newState);
	}


	list(outerParams={}, page=1){
	    let params = {
	    	type: this.props.params.type,
	    	status: this.props.params.status,
	    	...this.state.params,
	    	page
	    };
		if(!isEmpty(outerParams)){
	    	params = {...params, ...outerParams};
	    }

		Tools.apiCall(apiUrls.list, params, false).then((result) => {
	    	if(result.success){
		    	this.setInitData(result);
	    	}
	    });
	}
	/*
	handleFilter(event){
		let keyword = event.target.value;
		if(this.filterTimeout !== null){
			clearTimeout(this.filterTimeout);
		}
		this.filterTimeout = setTimeout(() => {
			if(keyword.length > 2){
				this.list({keyword: keyword});
			}else if(!keyword.length){
				this.list();
			}
		}, 600);
	}
	*/
	handleFilter(eventData, dispatch){
		let params = {
			...eventData,
			created_at: eventData.created_at?(eventData.created_at[0] ? eventData.created_at.join(',') : ''):''
		};
		this.setState({params}, () => this.list());
	}

	handlePageChange(data){
		let page = data.selected + 1;
		this.list({}, page);
	}

	handleDraftToNew(id){
	    let params = {id};
		Tools.apiCall(apiUrls.draftToNew, params).then((result) => {
	    	if(result.success){
	    		this.list();
	    	}
	    });
	}

	handleCheck(id, checked){
		let index = store.getState().orderReducer.list.findIndex(x => x.id===id);
		this.props.orderAction('edit', {checked}, index);
	}

	handleCheckAll(){
		let list = this.props.orderReducer.list;
		if(filter(list, {checked: true}).length === list.length){
			this.props.orderAction('uncheckAll');
		}else{
			this.props.orderAction('checkAll');
		}
	}

	handleRemove(id=null){
		const confirm = window.confirm('Do you want to remove this item(s)?');
		if(!confirm){
			return;
		}
		if(id === null){
			let listId = [];
			this.props.orderReducer.list.map(value => {
				if(value.checked){
					listId.push(value.id);
				}
			});
			if(!listId.length){
				window.alert("Bạn vui lòng chọn ít nhất 1 phần tử để xoá.");
				return;
			}
			id = listId.join(',');
		}else{
			id = String(id);
		}
		Tools.apiCall(apiUrls.remove, {id}).then((result) => {
	    	if(result.success){
	    		let listId = result.data.id;
	    		if(typeof listId !== 'object'){
	    			listId = [listId];
	    		}
	    		let listIndex = listId.map(id => {
					return store.getState().orderReducer.list.findIndex(x => x.id === parseInt(id));
	    		});
				this.props.orderAction('remove', null, listIndex);
	    	}
	    });
	}

	handleConfirmOrder(id=null){
		const confirm = window.confirm('Bạn muốn duyệt tất cả đơn này?');
		if(!confirm){
			return;
		}
		if(id === null){
			let listId = [];
			this.props.orderReducer.list.map(value => {
				if(value.checked){
					listId.push(value.id);
				}
			});

			if(!listId.length){
				window.alert("Bạn vui lòng chọn ít nhất 1 phần tử để xoá.");
				return;
			}
			id = listId.join(',');
		}else{
			id = String(id);
		}
		Tools.apiCall(apiUrls.massConfirm, {id}).then((result) => {
	    	if(result.success){
	    		let listId = result.data.id;

	    		let listIndex = listId.map(id => {
					let index = store.getState().orderReducer.list.findIndex(x => x.id===id);
					this.props.orderAction('edit', {status: 'confirm', checked: false}, index);
	    		});
	    	}
	    });
	}

	handleChange(eventData, dispatch){
		try{
			let params = {
				type: this.props.params.type,
				address_id: eventData.address_id
			};
			const id = this.state.itemId;
			if(id){
				params = {
					address_id: eventData.address_id,
					admin_id: eventData.admin_id,
					status: eventData.status,
					note: eventData.note,
					order_fee_factor: eventData.order_fee_factor,
					rate: eventData.rate
				}
			}
			return Tools.apiCall(apiUrls[id?'edit':'add'], id?{...params, id}:params).then((result) => {
		    	if(result.success){
		    		const data = {
						...result.data
		    		};
		    		if(id){
						let index = store.getState().orderReducer.list.findIndex(x => x.id===id);
						this.props.orderAction('edit', data, index);
		    		}else{
						this.props.orderAction('add', data);
		    		}
		    		dispatch(reset('OrderMainForm'));
		    		this.toggleModal(null, 'mainModal', false);
		    		if(!id){
			    		Tools.goToUrl('order', [data.type, 'new', data.id]);
		    		}
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	render() {
		return (
			<OrderLayout
				{...this.props}
				dataLoaded={this.state.dataLoaded}
				mainModal={this.state.mainModal}
				userDetailModal={this.state.userDetailModal}
				userDetailData={this.state.userDetailData}
				logModal={this.state.logModal}
				bulkRemove={this.state.bulkRemove}
				itemId={this.state.itemId}
				list={this.list}
				toggleModal={this.toggleModal}
				onFilter={this.handleFilter}
				onCheck={this.handleCheck}
				onCheckAll={this.handleCheckAll}
				onRemove={this.handleRemove}
				onConfirmOrder={this.handleConfirmOrder}
				onChange={this.handleChange}
				onPageChange={this.handlePageChange}
				onToggleLog={this.handleToggleLog}
				onDraftToNew={this.handleDraftToNew}
				/>
		);
	}
}

export default Order;
