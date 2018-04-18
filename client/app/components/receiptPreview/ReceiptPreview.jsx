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
import ReceiptPreviewLayout from './ReceiptPreview.layout';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class ReceiptPreview extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
			itemId: null,
			params: {},
			dataLoaded: false
	    };
	}

	setInitData(initData){
		this.props.receiptAction('obj', {...initData.data, ...initData.extra});
		this.setState({dataLoaded: true});
	}

	componentDidMount(){
		document.title = 'Xuất phiếu thu';
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

	render() {
		return (
			<ReceiptPreviewLayout
				{...this.props}
				dataLoaded={this.state.dataLoaded}
				/>
		);
	}
}

export default ReceiptPreview;
