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
import AddressLayout from './Address.layout';

class Address extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			mainModal: false,
			itemId: null,
			bulkRemove: true,
			params: {},
			showMainTable: true,
			dataLoaded: store.getState().addressReducer.list.length?true:false
	    };

	    this.list = this.list.bind(this);
	    this.toggleModal = this.toggleModal.bind(this);
	    this.handleFilter = this.handleFilter.bind(this);
	    this.handleCheck = this.handleCheck.bind(this);
	    this.handleCheckAll = this.handleCheckAll.bind(this);
	    this.handleRemove = this.handleRemove.bind(this);
	    this.handlePrint = this.handlePrint.bind(this);
	    this.handleChange = this.handleChange.bind(this);
	    this.handlePageChange = this.handlePageChange.bind(this);
	    this.filterTimeout = null;
	}

	setInitData(initData){
		const listAreaCode = initData.extra.list_area_code.map(item => {
			item.title = item.code + ': ' + item.title;
			return item;
		});
	    this.props.addressAction('listAreaCode', {list: listAreaCode});
		this.props.addressAction('defaultAreaCode', listAreaCode[0].id);
		this.props.addressAction('newList', {list: [...initData.data.items], pages: initData.data._meta.last_page});
		this.setState({dataLoaded: true});
	}

	componentDidMount(){
		document.title = 'Address';
		if(!this.props.addressReducer.list.length){
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
					Tools.apiCall(apiUrls.obj, {id: id}, false).then((result) => {
						if(result.success){
							this.props.addressAction('obj', {
								...result.data
							});
							this.setState(newState);
							return;
						}
					});
				}else{
					this.props.addressAction('obj', {...Tools.getInitData(labels.mainForm), area_code_id: store.getState().addressReducer.defaultAreaCode});
					this.setState(newState);
				}
			break;
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

	handlePageChange(data){
		let page = data.selected + 1;
		this.list({}, page);
	}

	handleCheck(id, checked){
		let index = store.getState().addressReducer.list.findIndex(x => x.id===id);
		this.props.addressAction('edit', {checked}, index);
	}

	handleCheckAll(){
		let list = this.props.addressReducer.list;
		if(filter(list, {checked: true}).length === list.length){
			this.props.addressAction('uncheckAll');
		}else{
			this.props.addressAction('checkAll');
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
		    		const oldDefaultId = result.extra.oldDefaultId;
	    			if(oldDefaultId !== null){
						let oldIndex = store.getState().addressReducer.list.findIndex(x => x.id === oldDefaultId);
						if(oldIndex !== -1){
							this.props.addressAction('edit', {default: false}, oldIndex);
						}
	    			}
		    		if(id){
						let index = store.getState().addressReducer.list.findIndex(x => x.id === id);
						this.props.addressAction('edit', data, index);
		    		}else{
						this.props.addressAction('add', data);
		    		}
		    		dispatch(reset('AddressMainForm'));
		    		this.toggleModal(null, 'mainModal', false);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	handlePrint(data){
		this.props.addressAction('obj', {
			...data
		});

		this.setState({showMainTable: false}, () => {
			setTimeout(() => {
				window.print();
				this.setState({showMainTable: true});
			}, 100);
		});
	}

	handleRemove(id=null){
		const confirm = window.confirm('Do you want to remove this item(s)?');
		if(!confirm){
			return;
		}
		if(id === null){
			let listId = [];
			this.props.addressReducer.list.map(value => {
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
					return store.getState().addressReducer.list.findIndex(x => x.id === parseInt(id));
	    		});
				this.props.addressAction('remove', null, listIndex);
				setTimeout(() => {
					const oldDefaultId = result.extra.oldDefaultId;
	    			if(oldDefaultId !== null){
						let oldIndex = store.getState().addressReducer.list.findIndex(x => x.id === oldDefaultId);
						if(oldIndex !== -1){
							this.props.addressAction('edit', {default: true}, oldIndex);
						}
	    			}
				}, 50);
	    	}
	    });
	}

	render() {
		return (
			<AddressLayout
				{...this.props}
				dataLoaded={this.state.dataLoaded}
				mainModal={this.state.mainModal}
				bulkRemove={this.state.bulkRemove}
				showMainTable={this.state.showMainTable}
				list={this.list}
				toggleModal={this.toggleModal}
				onFilter={this.handleFilter}
				onCheck={this.handleCheck}
				onCheckAll={this.handleCheckAll}
				onRemove={this.handleRemove}
				onPrint={this.handlePrint}
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

Address.propTypes = {
};

Address.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(Address);

// export default Address;
