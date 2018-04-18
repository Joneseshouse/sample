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
import CnBillOfLandingLayout from './CnBillOfLanding.layout';

class CnBillOfLanding extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			mainModal: false,
			itemId: null,
			bulkRemove: true,
			params: {},
			dataLoaded: store.getState().cnBillOfLandingReducer.list.length?true:false
	    };
	    this.list = this.list.bind(this);
	    this.toggleModal = this.toggleModal.bind(this);
	    this.handleFilter = this.handleFilter.bind(this);
	    this.handleCheck = this.handleCheck.bind(this);
	    this.handleCheckAll = this.handleCheckAll.bind(this);
	    this.handleRemove = this.handleRemove.bind(this);
	    this.handleChange = this.handleChange.bind(this);
	    this.handleUpload = this.handleUpload.bind(this);
	    this.handlePageChange = this.handlePageChange.bind(this);
	    this.setDateFilter = this.setDateFilter.bind(this);

	    this.filterTimeout = null;
	}

	setInitData(initData){
        this.props.cnBillOfLandingAction(
            'newList', 
            {
                list: [...initData.data.items], 
                pages: initData.data._meta.last_page
            }
        );
		this.setState({dataLoaded: true});
	}

	componentDidMount(){
		document.title = 'CnBillOfLanding';
		// if(!this.props.cnBillOfLandingReducer.list.length){
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
							this.props.cnBillOfLandingAction('obj', {
								...result.data
							});
							this.setState(newState);
							return;
						}
					});
				}else{
					this.props.cnBillOfLandingAction('obj', Tools.getInitData(labels.mainForm));
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

        if(this.state.start_date && this.state.end_date){
	    	params['start_date'] = this.state.start_date;
	    	params['end_date'] = this.state.end_date;
	    }

		Tools.apiCall(apiUrls.list, params, false).then((result) => {
	    	if(result.success){
		    	this.setInitData(result);
	    	}
	    });
    }

    setDateFilter (dateRange) {
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
		let index = store.getState().cnBillOfLandingReducer.list.findIndex(x => x.id===id);
		this.props.cnBillOfLandingAction('edit', {checked}, index);
	}

	handleCheckAll(){
		let list = this.props.cnBillOfLandingReducer.list;
		if(filter(list, {checked: true}).length === list.length){
			this.props.cnBillOfLandingAction('uncheckAll');
		}else{
			this.props.cnBillOfLandingAction('checkAll');
		}
	}

	handleRemove(id=null){
		const confirm = window.confirm('Do you want to remove this item(s)?');
		if(!confirm){
			return;
		}
		if(id === null){
			let listId = [];
			this.props.cnBillOfLandingReducer.list.map(value => {
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
					return store.getState().cnBillOfLandingReducer.list.findIndex(x => x.id === parseInt(id));
	    		});
				this.props.cnBillOfLandingAction('remove', null, listIndex);
	    	}
	    });
	}

	handleChange(eventData, dispatch){
		try{
			const params = {...eventData};
			const id = this.state.itemId;
			return Tools.apiCall(apiUrls[id?'edit':'add'], id?{...params, id}:params).then((result) => {
		    	if(result.success){
		    		this.list();
		    		/*
		    		const data = {
						...result.data
		    		};
		    		if(id){
						let index = store.getState().cnBillOfLandingReducer.list.findIndex(x => x.id===id);
						this.props.cnBillOfLandingAction('edit', data, index);
		    		}else{
						this.props.cnBillOfLandingAction('add', data);
		    		}
		    		*/
		    		dispatch(reset('CnBillOfLandingMainForm'));
		    		this.toggleModal(null, 'mainModal', false);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	handleUpload(eventData, dispatch){
		try{
			const params = {...eventData};
			return Tools.apiCall(apiUrls.upload, params).then((result) => {
		    	if(result.success){
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
			<CnBillOfLandingLayout
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
				onUpload={this.handleUpload}
				onPageChange={this.handlePageChange}
				setDateFilter={this.setDateFilter}
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

CnBillOfLanding.propTypes = {
};

CnBillOfLanding.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(CnBillOfLanding);

// export default CnBillOfLanding;
