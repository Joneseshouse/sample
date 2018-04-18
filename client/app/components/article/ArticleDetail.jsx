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
import ArticleDetailLayout from './ArticleDetail.layout';

class ArticleDetail extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			dataLoaded: false,
			title: null,
			category: {}
	    };
	    this.handleChange = this.handleChange.bind(this);
	}

	setInitData(initData){
		this.setState({
			category: initData.data.category,
			title: initData.data.title
		});
		this.props.articleAction('obj', {
			...initData.data
		});
		document.title = initData.data.title;
		this.setState({dataLoaded: true});
	}

	componentDidMount(){
		if(window.initData){
			if(window.initData.success){
				this.setInitData(window.initData);
			}
		    window.initData = null;
		}else{
			Tools.apiCall(apiUrls.obj, {id: this.props.params.id}, false).then((result) => {
				if(result.success){
					this.setInitData(result);
				}
			});
		}
	}

	handleChange(eventData, dispatch){
		try{
			const params = {...eventData};
			const id = this.props.params.id;
			return Tools.apiCall(apiUrls['edit'], {...params, id}).then((result) => {
		    	if(result.success){
		    		/*
		    		const data = {
						...result.data
		    		};
					let index = store.getState().articleReducer.list.findIndex(x => x.id===id);
					this.props.articleAction('edit', data, index);
					*/
		    		dispatch(reset('ArticleDetailForm'));
		    		setTimeout(()=>{
			    		Tools.goToUrl('article', [result.data.category_id]);
		    		}, 100);
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
			<ArticleDetailLayout
				{...this.props}
				dataLoaded={this.state.dataLoaded}
				category={this.state.category}
				title={this.state.title}
				onChange={this.handleChange}
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

ArticleDetail.propTypes = {
};

ArticleDetail.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(ArticleDetail);

// export default ArticleDetail;
