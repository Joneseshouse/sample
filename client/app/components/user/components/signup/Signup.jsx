import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { SubmissionError, reset } from 'redux-form';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import {apiUrls, labels} from '../../_data';
import { BASE_URL } from 'app/constants';
import Tools from 'helpers/Tools';
import SignupForm from './forms/Signup.form';
import WaitingMessage from 'utils/components/WaitingMessage';


class Signup extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
	    };
	    this.handleChange = this.handleChange.bind(this);
	}

	componentDidMount() {
		return Tools.apiCall(apiUrls.areaCodeList, {}, false).then((result) => {
			const listAreaCode = result.data.items.reverse().map(item => {
				item.title = item.code + ': ' + item.title;
				return item;
			});
			this.props.userAction('listAreaCode', {list: listAreaCode});
			this.props.userAction('defaultAreaCode', listAreaCode[0].id);
			this.props.userAction('obj', {...Tools.getInitData(labels.mainForm), area_code_id: store.getState().userReducer.defaultAreaCode});
		});
	}

	handleChange(eventData, dispatch){
		try{
			const params = {...eventData};
			return Tools.apiCall(apiUrls.signup, params).then((result) => {
		    	if(result.success){
		    		const data = {
						...result.data
		    		};
		    		/*
		    		if(id){
						let index = store.getState().userReducer.list.findIndex(x => x.id===id);
						this.props.userAction('edit', data, index);
		    		}else{
						this.props.userAction('add', data);
		    		}
		    		*/
		    		setTimeout(()=>{
			    		window.top.location = BASE_URL + 'login';
					}, 1000);
		    		dispatch(reset('UserSignupForm'));
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	render() {
		if(!this.props.userReducer.listAreaCode.length){
			return <WaitingMessage/>
		}
		return (
			<div style={{backgroundColor: '#F7F7F7', paddingBottom: 200}}>
				<strong>
					Đăng ký thành viên
				</strong>
				<div>
					Bạn hãy đăng ký thành viên để có thể sử dụng dịch vụ của chúng tôi và thoải mái mua sắm.
				</div>
				<hr/>
				<SignupForm
					onSubmit={this.handleChange}
					labels={labels.mainForm}
					submitTitle="Đăng ký">
				</SignupForm>
			</div>
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

Signup.propTypes = {
};

Signup.defaultProps = {
};

export default connect(
	mapStateToProps,
	mapDispatchToProps
)(Signup);

// export default Signup;
