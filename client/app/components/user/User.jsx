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
import UserLayout from './User.layout';
import md5 from 'blueimp-md5';

class User extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			mainModal: false,
			itemId: null,
			bulkRemove: true,
			params: {},
			defaultRole: null,
			dataLoaded: store.getState().userReducer.list.length?true:false
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
		listAdmin.unshift({id: 0, title: '--- Chọn NVCS ---'});

		let listDathangAdmin = Tools.renameColumn(initData.extra.list_dathang_admin, 'full_name');
		listDathangAdmin.unshift({id: 0, title: '--- Chọn NV đặt hàng ---'});

	    this.props.userAction('listAdmin', {list: listAdmin});
	    this.props.userAction('listDathangAdmin', {list: listDathangAdmin});

	   	if(listAdmin.length){
		    this.props.userAction('defaultAdmin', listAdmin[0].id);
	   	}

	   	let listUser = Tools.renameColumn(initData.extra.list_user, 'fulltitle');
		listUser.unshift({id: 0, title: '--- Chọn khách hàng ---'});
	    this.props.userAction('listUser', {list: listUser});

		const listAreaCode = initData.extra.list_area_code.map(item => {
			item.title = item.code + ': ' + item.title;
			return item;
		});

		this.props.userAction('listAreaCode', {list: listAreaCode});
		this.props.userAction('defaultAreaCode', listAreaCode[0].id);

		this.props.userAction('newList', {list: [...initData.data.items], pages: initData.data._meta.last_page});
		this.setState({
			dataLoaded: true,
			order_fee_factor: parseFloat(initData.extra.order_fee_factor)
		});
	}

	componentDidMount(){
		document.title = 'User';
		if(!this.props.userReducer.list.length){
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
		const id = parseInt(this.props.params.id);
		if(id){
			this.toggleModal(id, 'mainModal');
		}
	}

	toggleModal(id=null, state, open=true){
		let newState = {};
		newState[state] = open;

		switch(state){
			case 'mainModal':
				newState.itemId = id;
				if(id && open){
					Tools.goToUrl('user', [id]);
					Tools.apiCall(apiUrls.obj, {id: id}, false).then((result) => {
						if(result.success){
							this.props.userAction('obj', {
								...result.data,
								password: null
							});
							this.setState(newState);
							return;
						}
					});
				}else{
					this.props.userAction('obj', {
						...Tools.getInitData(labels.mainForm),
						region: store.getState().userReducer.defaultRegion,
						order_fee_factor: this.state.order_fee_factor
					});
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
	/*
	handleFilter(event){
		let keyword = event.target.value;
		if(this.filterTimeout !== null){
			clearTimeout(this.filterTimeout);
		}
		this.filterTimeout = setTimeout(() => {
			if(keyword.length >= 1){
				this.list({keyword: keyword});
			}else if(!keyword.length){
				this.list();
			}
		}, 600);
	}
	*/

	handleFilter(eventData, dispatch){
		this.setState({params: eventData}, () => this.list());
	}

	handlePageChange(data){
		let page = data.selected + 1;
		this.list({}, page);
	}

	handleCheck(id, checked){
		let index = store.getState().userReducer.list.findIndex(x => x.id===id);
		this.props.userAction('edit', {checked}, index);
	}

	handleCheckAll(){
		let list = this.props.userReducer.list;
		if(filter(list, {checked: true}).length === list.length){
			this.props.userAction('uncheckAll');
		}else{
			this.props.userAction('checkAll');
		}
	}

	handleRemove(id=null){
		const confirm = window.confirm('Do you want to remove this item(s)?');
		if(!confirm){
			return;
		}
		if(id === null){
			let listId = [];
			this.props.userReducer.list.map(value => {
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
					return store.getState().userReducer.list.findIndex(x => x.id === parseInt(id));
	    		});
				this.props.userAction('remove', null, listIndex);
	    	}
	    });
	}

	handleChange(eventData, dispatch){
		try{
			let params = {...eventData};
			if(params.password){
				params.password = md5(params.password);
			}else{
				delete params.password;
			}
			const id = this.state.itemId;
			return Tools.apiCall(apiUrls[id?'edit':'add'], id?{...params, id}:params).then((result) => {
		    	if(result.success){
		    		const data = {
						...result.data
		    		};
		    		if(id){
						let index = store.getState().userReducer.list.findIndex(x => x.id===id);
						this.props.userAction('edit', data, index);
		    		}else{
						this.props.userAction('add', data);
		    		}
		    		dispatch(reset('UserMainForm'));
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
			<UserLayout
				{...this.props}
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

function mapStateToProps(state){
	return {
	}
}

function mapDispatchToProps(dispatch){
	return {
		...bindActionCreators(actionCreators, dispatch),
		resetForm: (formName) => {
			dispatch(reset(formName));
		}
	};
}

User.propTypes = {
};

User.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(User);

// export default User;
