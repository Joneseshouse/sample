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
import ExportBillPreviewLayout from './ExportBillPreview.layout';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class ExportBillPreview extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			mainModal: false,
			itemId: null,
			params: {},
			dataLoaded: false
	    };
	    this.toggleModal = this.toggleModal.bind(this);
	    this.handleChange = this.handleChange.bind(this);
	}

	setInitData(initData){
		const list_contact = [
			{id: 0, title: '--- Địa chỉ mặc định trong cấu hình ---'},
			...Tools.renameColumn(initData.extra.list_contact, 'address')
		];
		this.props.exportBillPreviewAction('obj', {...initData.data, ...initData.extra, list_contact});
		this.setState({dataLoaded: true});
	}

	componentDidMount(){
		document.title = 'ExportBillPreview';
		const parentNode = document.getElementsByClassName('content-wrapper')[0].parentNode;
		parentNode.setAttribute('id', 'content-wrapper-parent');
		if(window.initData){
	    	if(window.initData.success){
	    		this.setInitData(window.initData);
			}else{
				// Pop message here
			}
		    window.initData = null;
		}else{
			this.getInitData();
		}
	}

	getInitData(){
		Tools.apiCall(apiUrls.obj, {id: this.props.params.id}, false).then((result) => {
	    	if(result.success){
		    	this.setInitData(result);
	    	}
	    });
	}

	handleChange(eventData, dispatch){
		try{
			const params = {...eventData, id: this.props.params.id};
			return Tools.apiCall(apiUrls.editContact, params).then((result) => {
		    	if(result.success){
					this.props.exportBillPreviewAction('obj', {
						...result.data
					});
		    		dispatch(reset('ExportBillPreviewMainForm'));
		    		this.toggleModal(null, 'mainModal', false);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
	    	console.error(error);
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	toggleModal(id=null, state, open=true){
		let newState = {};
		newState[state] = open;
		this.setState(newState);
	}

	render() {
		return (
			<ExportBillPreviewLayout
				{...this.props}
				mainModal={this.state.mainModal}
				dataLoaded={this.state.dataLoaded}
				onChange={this.handleChange}
				toggleModal={this.toggleModal}
				/>
		);
	}
}

export default ExportBillPreview;
