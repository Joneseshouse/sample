import React from 'react';
import PropTypes from 'prop-types';
import isEmpty from 'lodash/isEmpty';
import keys from 'lodash/keys';
import forEach from 'lodash/forEach';
import filter from 'lodash/filter';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { SubmissionError, reset } from 'redux-form';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import {apiUrls, labels} from './_data';
import Tools from 'helpers/Tools';
import CheckBillLayout from './CheckBill.layout';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class CheckBill extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			dataSession: new Date().getTime(),
			mainModal: false,
			noteModal: false,
			depositModal: false,
			previewModal: false,
			itemId: null,
			isDeposit: false,
			bulkRemove: true,
			params: {},
			dataLoaded: true
	    };
	    this.list = this.list.bind(this);
	    this.toggleModal = this.toggleModal.bind(this);
	    this.handleFilter = this.handleFilter.bind(this);
	    this.handleCheck = this.handleCheck.bind(this);
	    this.handleCheckAll = this.handleCheckAll.bind(this);
	    this.handleRemove = this.handleRemove.bind(this);
	    this.handleChange = this.handleChange.bind(this);
	    this.handlePageChange = this.handlePageChange.bind(this);
	    this.listPurchaseProcessing = this.listPurchaseProcessing.bind(this);
	    this.handleFilterCore = this.handleFilterCore.bind(this);
	    this.filterTimeout = null;
	}

	setInitData(initData){
		this.props.checkBillAction(
			'newList', {
				list: [...initData.data.items],
				listCheckItemStatus: [...initData.extra.list_check_item_status],
				pages: initData.data._meta.last_page
			}
		);
		this.setState({dataLoaded: true});
	}

	componentDidMount(){
		document.title = 'CheckBill';
		if(!this.props.checkBillReducer.list.length){
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
		}
	}

	toggleModal(id=null, state, open=true){
		let newState = {};
		newState[state] = open;

		switch(state){
			case 'mainModal':
				newState.itemId = id;
				if(id && open){
					/*
					Tools.apiCall(apiUrls.obj, {id: id}, false).then((result) => {
						if(result.success){
							this.props.checkBillAction('obj', {
								...result.data
							});
							this.setState(newState);
							return;
						}
					});
					*/
				}else{
					// this.props.checkBillAction('obj', Tools.getInitData(labels.mainForm));
					this.setState(newState);
				}
			break;
			case 'depositModal':
				if(open){
					this.props.checkBillAction('obj', Tools.getInitData(labels.depositForm));
				}
				this.setState(newState);
			break;
			case 'previewModal':
				newState.itemId = id;
				this.setState(newState);
			break;
			case 'noteModal':
				newState.itemId = id;
				if(id && open){
					Tools.apiCall(apiUrls.orderItemNoteList, {order_item_id: id}, false).then((result) => {
						if(result.success){
							this.props.orderDetailAction('listNote', {
								list: result.data.items,
								orderItemId: id
							});
							this.setState(newState);
						}
					});
				}else{
					this.setState(newState);
				}
			break;
		}
		if(!open){
			this.props.checkBillAction('keyword', '');
		}
	}


	list(outerParams={}, page=1){
	    let params = {
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

	listPurchaseProcessing(listPurchase){
		this.setState({dataSession: new Date().getTime()});
		return listPurchase.map(purchase => {
			const bol = purchase.bill_of_landing;
    		purchase.note = '';
    		purchase.sub_fee = bol.sub_fee;
    		purchase.input_mass = parseFloat(bol.input_mass);
    		purchase.packages = bol.packages?bol.packages:1;
    		purchase.length = bol.length;
    		purchase.width = bol.width;
    		purchase.height = bol.height;

    		purchase.bill_of_landing_code = this.props.checkBillReducer.keyword;
    		purchase.original_bill_of_landing_code = this.props.checkBillReducer.keyword;
    		purchase.order_items = purchase.order_items.map(orderItem => {
    			// orderItem.checking_quantity = 0;
    			return orderItem;
    		})
    		return purchase;
    	});
	}

	handleFilterCore(keyword){
		// Toggle modal to showup list purchase and list item
		const params = {code: keyword.toUpperCase()};
		this.props.checkBillAction('keyword', keyword);
		Tools.apiCall(apiUrls.billOfLandingListCheckBill, params, false).then((result) => {
	    	if(result.success){
		    	const data = this.listPurchaseProcessing(result.data.items);
		    	if(data.length){
		    		// Có hàng hoá
		    		this.setState({isDeposit: false});
			    	this.props.checkBillAction('listPurchase', {list: data})
					this.toggleModal(null, 'mainModal');
		    	}else{
		    		// Không có hàng hoá
		    		this.setState({isDeposit: true});
					this.toggleModal(null, 'depositModal');
		    	}
	    	}
	    });
	}

	handleFilter(event){
		let keyword = event.target.value;
		if(this.filterTimeout !== null){
			clearTimeout(this.filterTimeout);
		}
		this.filterTimeout = setTimeout(() => {
			if(keyword.length >= 4){
				this.handleFilterCore(keyword);
			}
		}, 600);
	}

	handlePageChange(data){
		let page = data.selected + 1;
		this.list({}, page);
	}

	handleCheck(id, checked){
		let index = store.getState().checkBillReducer.list.findIndex(x => x.id===id);
		this.props.checkBillAction('edit', {checked}, index);
	}

	handleCheckAll(){
		let list = this.props.checkBillReducer.list;
		if(filter(list, {checked: true}).length === list.length){
			this.props.checkBillAction('uncheckAll');
		}else{
			this.props.checkBillAction('checkAll');
		}
	}

	handleRemove(id=null){
		const confirm = window.confirm('Do you want to remove this item(s)?');
		if(!confirm){
			return;
		}
		if(id === null){
			let listId = [];
			this.props.checkBillReducer.list.map(value => {
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
					return store.getState().checkBillReducer.list.findIndex(x => x.id === parseInt(id));
	    		});
				this.props.checkBillAction('remove', null, listIndex);
	    	}
	    });
	}

	handleChange(data){
		try{
			if(this.state.isDeposit){
				if(!parseInt(data.packages)){
					window.alert('Bạn cần điền đầy đủ thông tin về số kiện.');
					return;
				}
				if(!parseFloat(data.input_mass)){
					if(!parseInt(data.length) || !parseInt(data.width) || !parseInt(data.height)){
						window.alert('Bạn cần điền đầy đủ thông tin về khối lượng hoặc kích thước.');
						return;
					}
				}
				data.original_bill_of_landing_code = this.props.checkBillReducer.keyword;
				const params = {data: JSON.stringify(data), list_order_item_id: '[]'};
				return Tools.apiCall(apiUrls.checkFull, params).then((result) => {
			    	if(result.success){
						this.props.checkBillAction('add', result.data);
			    		store.dispatch(reset('CheckBillDepositForm'));
				    	this.toggleModal(null, 'depositModal', false);
			    	}else{
						throw new SubmissionError(Tools.errorMessageProcessing(result.message));
			    	}
			    });
			}else{
				/*
				const listPurchase = this.props.checkBillReducer.listPurchase;
				const checkingBillOfLandingCode = this.props.checkBillReducer.keyword;
				if(listPurchase.length > 1){
					// 1 mã vận đơn trong nhiều mã giao dịch -> luôn luôn ép phải tạo mã bill mới cho đến cái cuối.
					if(data.bill_of_landing_code === checkingBillOfLandingCode){
						window.alert('Bạn cần đổi tên mã vận đơn này trước khi lưu lại.');
						return;
					}
				}else{
					// 1 mã vận đơn 1 mã giao dịch -> chỉ ép tạo mã giao dịch khi thiếu số lượng
					let isFull = true;
					forEach(listPurchase[0].order_items, orderItem => {
						if(orderItem.checking_quantity !== orderItem.checked_quantity){
							isFull = false;
							return false;
						}
					})
					if(!isFull){
						// Trường hợp số lượng thiếu
						if(data.bill_of_landing_code === checkingBillOfLandingCode){
							window.alert('Bạn cần đổi tên mã vận đơn này trước khi lưu lại.');
							return;
						}
					}
				}
				*/

				if(!parseInt(data.packages)){
					window.alert('Bạn cần điền đầy đủ thông tin về số kiện.');
					return;
				}
				if(!parseFloat(data.input_mass)){
					if(!parseInt(data.length) || !parseInt(data.width) || !parseInt(data.height)){
						window.alert('Bạn cần điền đầy đủ thông tin về khối lượng hoặc kích thước.');
						return;
					}
				}
				/*
				if(filter(data.order_items, {checking_quantity: 0}).length === data.order_items.length){
					window.alert('Bạn không được để trống tất cả số lượng trong đơn hàng.');
					return;
				}
				*/
				let listOrderItemId = [];
				forEach(this.props.checkBillReducer.listPurchase, purchase => {
					forEach(purchase.order_items, orderItem => {
						listOrderItemId.push(parseInt(orderItem.id));
					})
				});
				const params = {data: JSON.stringify(data), list_order_item_id: JSON.stringify(listOrderItemId)}
				return Tools.apiCall(apiUrls.checkFull, params).then((result) => {
			    	if(result.success){
			    		/*
						this.props.checkBillAction('add', result.data);
			    		if(result.extra.list_purchase){
				    		const data = this.listPurchaseProcessing(result.extra.list_purchase);
					    	this.props.checkBillAction('listPurchase', {list: data});
			    		}else{
				    		this.toggleModal(null, 'mainModal', false);
			    		}
			    		*/
			    	}else{
						throw new SubmissionError(Tools.errorMessageProcessing(result.message));
			    	}
			    });
			}
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	render() {
		return (
			<CheckBillLayout
				{...this.props}
				itemId={this.state.itemId}
				dataSession={this.state.dataSession}
				dataLoaded={this.state.dataLoaded}
				mainModal={this.state.mainModal}
				noteModal={this.state.noteModal}
				depositModal={this.state.depositModal}
				previewModal={this.state.previewModal}
				bulkRemove={this.state.bulkRemove}
				list={this.list}
				toggleModal={this.toggleModal}
				onFilter={this.handleFilter}
				onCheck={this.handleCheck}
				onCheckAll={this.handleCheckAll}
				onRemove={this.handleRemove}
				onChange={this.handleChange}
				onPageChange={this.handlePageChange}
				onChangeBol={this.handleFilterCore}
				/>
		);
	}
}

export default CheckBill;
