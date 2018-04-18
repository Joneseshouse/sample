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
import UserOrderLogLayout from './UserOrderLog.layout';

class UserOrderLog extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			mainModal: false,
			itemId: null,
			bulkRemove: true,
			params: {},
			dataLoaded: store.getState().userOrderLogReducer.list.length?true:false
	    };
	    this.list = this.list.bind(this);
	    this.handlePageChange = this.handlePageChange.bind(this);

	    this.filterTimeout = null;
	}

	setInitData(initData){
		this.props.userOrderLogAction('newList', {list: [...initData.data.items], pages: initData.data._meta.last_page});
		this.setState({dataLoaded: true});
	}

	componentDidMount(){
		if(window.initData){
	    	if(window.initData.success){
	    		this.setInitData(window.initData);
			}else{
				// Pop message here
			}
		    window.initData = null;
		}else{
			this.list(this.props.conditions);
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

	handlePageChange(data){
		let page = data.selected + 1;
		this.list({}, page);
	}

	render() {
		return (
			<UserOrderLogLayout
				{...this.props}
				dataLoaded={this.state.dataLoaded}
				bulkRemove={this.state.bulkRemove}
				list={this.list}
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

UserOrderLog.propTypes = {
	conditions: PropTypes.object
};

UserOrderLog.defaultProps = {
	conditions: {}
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(UserOrderLog);

// export default UserOrderLog;
