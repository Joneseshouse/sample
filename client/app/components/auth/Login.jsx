import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
// import Modal from 'react-modal';

import { SubmissionError, reset } from 'redux-form';
import md5 from 'blueimp-md5';

import * as actionCreators from 'app/actions/actionCreators';
import LoginForm from './forms/Login.form';
import ResetPasswordForm from './forms/ResetPassword.form';
import Tools from 'helpers/Tools';
import {apiUrls, labels} from './_data';
import {apiUrls as configApiUrls} from 'components/config/_data';
import store from 'app/store';

import CustomModal from 'utils/components/CustomModal';


class Login extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            resetPasswordModal: false,
            serverString: ''
        };
    }

    componentDidMount(){
        document.title = "Login";
    }

    loginHandle(eventData, dispatch){
        try{
            const params = {
                ...eventData,
                password: md5(eventData.password)
            };
            return Tools.apiCall(apiUrls.authenticate, params).then((result) => {
                if(result.success){
                    result.data.init = true;
                    Tools.setStorage('authData', result.data);
                    dispatch(reset('LoginForm'));
                    Tools.goToUrl();
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
        }
    }

    resetPasswordHandle(eventData, dispatch){
        try{
            const params = {
                ...eventData,
                password: md5(eventData.password)
            };
            if(eventData.password !== eventData.newPassword){
                return Tools.sleep().then(() => {
                    throw new SubmissionError(Tools.errorMessageProcessing('Passwords not matched!'));
                });
            }
            return Tools.apiCall(apiUrls.resetPassword, params).then((result) => {
                if(result.success){
                    this.toggleModal('resetPasswordModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
        }
    }

    toggleModal(state, value=true){
        let newState = {};
        newState[state] = value;
        this.setState(newState);
    }

    render() {
        return (
            <div className="row">
                <div className="col-md-4 col-md-offset-4">
                    <br/>
                    <div className="well">
                        <p>
                            <strong>
                                Login Form
                            </strong>
                        </p>

                        <p>
                            Please enter your username as email & password.
                        </p>

                        <LoginForm
                            checkSubmit={this.loginHandle.bind(this)}
                            labels={labels.login}
                            submitTitle="Login">
                            <button
                                type="button"
                                className="btn btn-warning"
                                onClick={() => this.toggleModal('resetPasswordModal')}>
                                Forgot password
                            </button>
                        </LoginForm>
                    </div>

                    <CustomModal
                        open={this.state.resetPasswordModal}
                        close={() => this.toggleModal('resetPasswordModal', false)}
                        size="md"
                        title="Reset password"
                        >
                        <div>
                            <div className="custom-modal-content">
                                <ResetPasswordForm
                                    checkSubmit={this.resetPasswordHandle.bind(this)}
                                    labels={labels.resetPassword}
                                    submitTitle="Reset password">

                                    <button
                                        type="button"
                                        className="btn btn-warning cancel"
                                        onClick={() => this.toggleModal('resetPasswordModal', false)}>
                                        <span className="glyphicon glyphicon-remove"></span> &nbsp;
                                        Cancel
                                    </button>
                                </ResetPasswordForm>
                            </div>
                        </div>
                    </CustomModal>
                </div>
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

Login.propTypes = {
};

Login.defaultProps = {
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(Login);

// export default Login;
