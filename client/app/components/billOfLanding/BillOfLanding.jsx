import React from 'react';
import PropTypes from 'prop-types';
import isEmpty from 'lodash/isEmpty';
import keys from 'lodash/keys';
import values from 'lodash/values';
import filter from 'lodash/filter';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { SubmissionError, reset } from 'redux-form';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import {apiUrls, labels} from './_data';
import Tools from 'helpers/Tools';
import BillOfLandingLayout from './BillOfLanding.layout';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class BillOfLanding extends React.Component {
	static propTypes = {};
	static defaultProps = {};
	constructor(props) {
		super(props);
		this.state = {
			mainModal: false,
			complainModal: false,
			itemId: null,
			bulkRemove: true,
			params: {},
			dataLoaded: store.getState().billOfLandingReducer.list.length?true:false,
			landing_status_filter: 'all',
			wooden_box_filter: 'all',
			total: {
				amount: 0
			}
	    };
	    this.list = this.list.bind(this);
	    this.toggleModal = this.toggleModal.bind(this);
	    this.handleFilter = this.handleFilter.bind(this);
	    this.handleCheck = this.handleCheck.bind(this);
	    this.handleCheckAll = this.handleCheckAll.bind(this);
	    this.handleRemove = this.handleRemove.bind(this);
	    this.handleChange = this.handleChange.bind(this);
	    this.handlePageChange = this.handlePageChange.bind(this);
	    this.setLandingStatusFilter = this.setLandingStatusFilter.bind(this);
	    this.setWoodenBoxFilter = this.setWoodenBoxFilter.bind(this);
	    this.setUserFilter = this.setUserFilter.bind(this);
	    this.setDateFilter = this.setDateFilter.bind(this);
	    this.handleResetComplain = this.handleResetComplain.bind(this);
	    this.handleEditComplain = this.handleEditComplain.bind(this);

	    this.filterTimeout = null;
	}

	setInitData(initData){
		let listUser = Tools.renameColumn(initData.extra.list_user, 'full_name');
		listUser.unshift({id: 'all', title: '--- Tất cả khách hàng ---'})
		const listAddress = Tools.renameColumn(initData.extra.list_address, 'address');
		const defaultAddressObj = filter(listAddress, {default: true})[0];
		let defaultAddress = null
		if(defaultAddressObj){
			defaultAddress = defaultAddressObj.id;
		}
        this.props.billOfLandingAction(
            'newList', 
            {
                list: [...initData.data.items], 
                pages: initData.data._meta.last_page
            }
        );
		this.props.billOfLandingAction('listAddress', {list: listAddress});
		this.props.billOfLandingAction('listUser', {list: listUser});
		this.props.billOfLandingAction('defaultAddress', defaultAddress);
		this.setState({
			dataLoaded: true,
			total: initData.extra.total
		});
	}

	componentDidMount(){
		document.title = 'BillOfLanding';
		// if(!this.props.billOfLandingReducer.list.length){
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
		let oldRegisterStatus = prevProps.params.insurance_register;
		let newRegisterStatus = this.props.params.insurance_register;

		let oldType = prevProps.params.type;
		let newType = this.props.params.type;

		let oldDate = prevProps.params.date;
		let newDate = this.props.params.date;
		if (newRegisterStatus !== oldRegisterStatus || newType !== oldType || oldDate !== newDate){
			this.list();
		}
	}

	toggleModal(id=null, state, open=true){
		let newState = {};
		newState[state] = open;
		newState.itemId = id;
		switch(state){
			case 'mainModal':
				if(id && open){
					Tools.apiCall(apiUrls.obj, {id: id}, false).then((result) => {
						if(result.success){
							result.data.landing_status = result.data.landing_status.split(':')[0];
							this.props.billOfLandingAction('obj', {
								...result.data
							});
							this.setState(newState);
							return;
						}
					});
				}else{
                    this.props.billOfLandingAction(
                        'obj', 
                        {
                            ...Tools.getInitData(labels.mainForm), 
                            'address_id': store.getState().billOfLandingReducer.defaultAddress
                        }
                    );
					this.setState(newState);
				}
			break;
			case 'complainModal':
				if(id && open){
					Tools.apiCall(apiUrls.obj, {id: id}, false).then((result) => {
						if(result.success){
							if(result.data.complain_change_date){
								this.props.billOfLandingAction('obj', {...result.data});
							}else{
                                this.props.billOfLandingAction(
                                    'obj', 
                                    {
                                        ...Tools.getInitData(labels.complainForm)
                                    }
                                );
							}
							this.setState(newState);
						}
					});
				}else{
					this.props.billOfLandingAction('obj', {...Tools.getInitData(labels.complainForm)});
					this.setState(newState);
				}
			break;
		}
	}


	list(outerParams={}, page=1){
	    let params = {
	    	insurance_register: this.props.params.insurance_register,
	    	landing_status_filter: this.state.landing_status_filter,
	    	wooden_box_filter: this.state.wooden_box_filter,
	    	user_filter: this.state.user_filter?this.state.user_filter:null,
	    	page
	    };
	    if(this.props.params.type !== 'all'){
	    	params['type'] = this.props.params.type;
	    }
	    if(this.state.start_date && this.state.end_date){
	    	params['start_date'] = this.state.start_date;
	    	params['end_date'] = this.state.end_date;
	    }else{
	    	if(this.props.params.date){
		    	params['date'] = this.props.params.date;
		    }
	    }

		if(this.props.params.insurance_register === '2'){
			delete params.insurance_register;
		}

		if(!isEmpty(outerParams)){
	    	params = {...params, ...outerParams};
	    }

		Tools.apiCall(apiUrls.list, params, false).then((result) => {
	    	if(result.success){
		    	this.setInitData(result);
	    	}
	    });
	}

	setLandingStatusFilter(landing_status_filter){
		this.setState({landing_status_filter}, () => this.list());
	}

	setWoodenBoxFilter(wooden_box_filter){
		this.setState({wooden_box_filter}, () => this.list());
	}

	setUserFilter(user_filter){
		this.setState({user_filter}, () => this.list());
	}

	setDateFilter(dateRange){
		this.setState({start_date: dateRange[0], end_date: dateRange[1]}, () => this.list());
	}

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

	handlePageChange(data){
		let page = data.selected + 1;
		this.list({}, page);
	}

	handleCheck(id, checked){
		let index = store.getState().billOfLandingReducer.list.findIndex(x => x.id===id);
		this.props.billOfLandingAction('edit', {checked}, index);
	}

	handleCheckAll(){
		let list = this.props.billOfLandingReducer.list;
		if(filter(list, {checked: true}).length === list.length){
			this.props.billOfLandingAction('uncheckAll');
		}else{
			this.props.billOfLandingAction('checkAll');
		}
	}

	handleRemove(id=null){
		const confirm = window.confirm('Do you want to remove this item(s)?');
		if(!confirm){
			return;
		}
		if(id === null){
			let listId = [];
			this.props.billOfLandingReducer.list.map(value => {
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
					return store.getState().billOfLandingReducer.list.findIndex(x => x.id === parseInt(id));
	    		});
				this.props.billOfLandingAction('remove', null, listIndex);
	    	}
	    });
	}

	handleResetComplain(id){
		try{
			return Tools.apiCall(apiUrls.resetComplain, {id}).then((result) => {
		    	if(result.success){
					this.props.billOfLandingAction('obj', {...Tools.getInitData(labels.complainForm)});
					setTimeout(() => {
			    		store.dispatch(reset('BillOfLandingComplainForm'));
					}, 100);
		    	}else{
		    		console.error(result.message);
		    	}
		    });
	    }catch(error){
		    console.error(result.message);
		}
	}

	handleEditComplain(eventData, dispatch){
		try{
			const params = {...eventData};
			const id = this.state.itemId;
			return Tools.apiCall(apiUrls.editComplain, {...params, id}).then((result) => {
		    	if(result.success){
		    		const data = {
						...result.data
		    		};
					let index = store.getState().billOfLandingReducer.list.findIndex(x => x.id===id);
					this.props.billOfLandingAction('edit', data, index);

		    		dispatch(reset('BillOfLandingComplainForm'));
		    		this.toggleModal(null, 'complainModal', false);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	handleChange(eventData, dispatch){
		try{
			const params = {...eventData};
			const id = this.state.itemId;
			return Tools.apiCall(apiUrls[id?'edit':'add'], id?{...params, id}:params).then((result) => {
		    	if(result.success){
		    		const data = {
						...result.data
		    		};
		    		if(id){
						let index = store.getState().billOfLandingReducer.list.findIndex(x => x.id===id);
						this.props.billOfLandingAction('edit', data, index);
		    		}else{
						this.props.billOfLandingAction('add', data);
		    		}
		    		dispatch(reset('BillOfLandingMainForm'));
		    		this.toggleModal(null, 'mainModal', false);
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
			<BillOfLandingLayout
				{...this.props}
				id={this.state.itemId}
				total={this.state.total}
				dataLoaded={this.state.dataLoaded}
				mainModal={this.state.mainModal}
				complainModal={this.state.complainModal}
				bulkRemove={this.state.bulkRemove}
				list={this.list}
				toggleModal={this.toggleModal}
				onFilter={this.handleFilter}
				setLandingStatusFilter={this.setLandingStatusFilter}
				setWoodenBoxFilter={this.setWoodenBoxFilter}
				setUserFilter={this.setUserFilter}
				setDateFilter={this.setDateFilter}
				onCheck={this.handleCheck}
				onCheckAll={this.handleCheckAll}
				onRemove={this.handleRemove}
				onChange={this.handleChange}
				onResetComplain={this.handleResetComplain}
				onEditComplain={this.handleEditComplain}
				onPageChange={this.handlePageChange}
				/>
		);
	}
}

export default BillOfLanding;
