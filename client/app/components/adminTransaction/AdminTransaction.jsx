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
import AdminTransactionLayout from './AdminTransaction.layout';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class AdminTransaction extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			mainModal: false,
			itemId: null,
			bulkRemove: true,
			params: {},
			total: {
				income: 0,
				expense: 0,
				purchasing: 0,
				balance: 0,
				debt: 0
			},
			dataLoaded: store.getState().adminTransactionReducer.list.length?true:false
	    };
	    this.list = this.list.bind(this);
	    this.toggleModal = this.toggleModal.bind(this);
	    this.handleFilter = this.handleFilter.bind(this);
	    this.handleCheck = this.handleCheck.bind(this);
	    this.handleCheckAll = this.handleCheckAll.bind(this);
	    this.handleRemove = this.handleRemove.bind(this);
	    this.handleChange = this.handleChange.bind(this);
	    this.handlePageChange = this.handlePageChange.bind(this);

	    this.filterTimeout = null;
	}

	setInitData(initData){
		let listAdmin = Tools.renameColumn(initData.extra.list_admin, 'full_name');
		listAdmin.unshift({id: 0, title: '--- Chọn nhân viên ---'});
		let listType = initData.extra.list_type;
		listType.unshift({id: '', title: '--- Chọn hành động ---'});

		this.props.adminTransactionAction('listAdmin', {list: listAdmin});
		this.props.adminTransactionAction('listType', {list: listType});

		this.props.adminTransactionAction('newList', {
			list: [...initData.data.items],
			pages: initData.data._meta.last_page,
			total: initData.extra.total
		});
		this.setState({
			total: initData.extra.total,
			dataLoaded: true
		});
	}

	componentDidMount(){
		document.title = 'AdminTransaction';
		// if(!this.props.adminTransactionReducer.list.length){
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

	toggleModal(id=null, state, open=true){
		let newState = {};
		newState[state] = open;

		switch(state){
			case 'mainModal':
				newState.itemId = id;
				if(id && open){
					Tools.apiCall(apiUrls.obj, {id: id}, false).then((result) => {
						if(result.success){
							this.props.adminTransactionAction('obj', {
								...result.data
							});
							this.setState(newState);
							return;
						}
					});
				}else{
					this.props.adminTransactionAction('obj', Tools.getInitData(labels.mainForm));
					this.setState(newState);
				}
			break;
		}
	}


	list(outerParams={}, page=1){
	    let params = {
	    	page,
	    	...this.state.params
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

	handleFilter(eventData, dispatch){
		this.setState({params: {...eventData, date_range: eventData.date_range?eventData.date_range.join(','):null}}, () => this.list());
		/*
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
		*/
	}

	handlePageChange(data){
		let page = data.selected + 1;
		this.list({}, page);
	}

	handleCheck(id, checked){
		let index = store.getState().adminTransactionReducer.list.findIndex(x => x.id===id);
		this.props.adminTransactionAction('edit', {checked}, index);
	}

	handleCheckAll(){
		let list = this.props.adminTransactionReducer.list;
		if(filter(list, {checked: true}).length === list.length){
			this.props.adminTransactionAction('uncheckAll');
		}else{
			this.props.adminTransactionAction('checkAll');
		}
	}

	handleRemove(id=null){
		const confirm = window.confirm('Do you want to remove this item(s)?');
		if(!confirm){
			return;
		}
		if(id === null){
			let listId = [];
			this.props.adminTransactionReducer.list.map(value => {
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
					return store.getState().adminTransactionReducer.list.findIndex(x => x.id === parseInt(id));
	    		});
				this.props.adminTransactionAction('remove', null, listIndex);
				this.list();
	    	}
	    });
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
						let index = store.getState().adminTransactionReducer.list.findIndex(x => x.id===id);
						this.props.adminTransactionAction('edit', data, index);
		    		}else{
						this.props.adminTransactionAction('add', data);
		    		}
		    		dispatch(reset('AdminTransactionMainForm'));
		    		this.toggleModal(null, 'mainModal', false);
		    		this.list();
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
			<AdminTransactionLayout
				{...this.props}
				total={this.state.total}
				dataLoaded={this.state.dataLoaded}
				mainModal={this.state.mainModal}
				bulkRemove={this.state.bulkRemove}
				list={this.list}
				toggleModal={this.toggleModal}
				onFilter={this.handleFilter}
				onCheck={this.handleCheck}
				onCheckAll={this.handleCheckAll}
				onRemove={this.handleRemove}
				onChange={this.handleChange}
				onPageChange={this.handlePageChange}
				/>
		);
	}
}

export default AdminTransaction;
