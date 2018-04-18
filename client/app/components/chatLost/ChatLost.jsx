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
import ChatLostLayout from './ChatLost.layout';


@connect(state => ({
	}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class ChatLost extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			itemId: null,
			showPanel: false
	    };

	    this.handleChange = this.handleChange.bind(this);
	    this.onPanel = this.onPanel.bind(this);

	}

	setInitData(initData){

	}

	componentDidMount(){

	}

	handleChange(eventData, dispatch){
		try{
			const params = {...eventData, lost_id: this.props.itemId};

			return Tools.apiCall(apiUrls['add'], params).then((result) => {
		    	if(result.success){
		    		const data = {
						...result.data
		    		};
					this.props.chatLostAction('appendList', data);
		    		this.props.resetForm('ChatLostMainForm');
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}
	onPanel(eventData){
		this.setState({ showPanel: !this.state.showPanel});
	}
	render() {
		return (
			<ChatLostLayout
				{...this.props}
				onChange={this.handleChange}
				list={this.state.list}
				onPanel={this.onPanel}
				showPanel={this.state.showPanel}
			/>
		);
	}
}

export default ChatLost;