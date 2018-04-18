import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { SubmissionError, reset } from 'redux-form';
import md5 from 'blueimp-md5';

// App components
import { ADMIN_ROLES } from 'app/constants';
import * as actionCreators from 'app/actions/actionCreators';
import ProfileForm from './forms/Profile.form';
import ChangePasswordForm from './forms/ChangePassword.form';
import Tools from 'helpers/Tools';
import {apiUrls, labels} from './_data';

import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import UserProfile from './components/UserProfile';


class Profile extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            profileModal: false,
            changePasswordModal: false,
            popupModal: false,
            extensionUrlGrab: null,
            data: {}
        };
        this._renderFullName = this._renderFullName.bind(this);
        this._renderInfo = this._renderInfo.bind(this);
        this._renderForAdmin = this._renderForAdmin.bind(this);
        this._renderPromotion = this._renderPromotion.bind(this);
    }

    componentDidMount(){
        document.title = 'Profile';
        Tools.apiCall(apiUrls.profile, {}, false).then((result) => {
            if(result.success){
                let promotionLink = result.extra.promotion_link;
                const data = this.setState({
                    'data':
                    {
                        ...result.data,
                        extension_url_grabbing: result.extra.extension_url_grabbing,
                        extension_url_shopping: result.extra.extension_url_shopping
                    }
                });
                this.props.updateProfile({...result.data});

                this.setState({
                    promotionLink,
                    promotionMessage: result.extra.promotion_message,
                    popupModal: (promotionLink && Tools.getStorage('authData').init) ? true : false
                }, () => {
                    let data = {...Tools.getStorage('authData')};
                    data.init = false;
                    Tools.setStorage('authData', data);
                });
            }
        });
    }

    toggleModal(state, value=true){
        let newState = {};
        newState[state] = value;
        this.setState(newState);
    }

    updateProfileHandle(eventData, dispatch){
        try{
            const params = {
                ...eventData
            };
            return Tools.apiCall(apiUrls.updateProfile, params).then((result) => {
                if(result.success){
                    Tools.setStorage('authData', result.data);
                    this.props.updateProfile({
                        email: result.data.email,
                        first_name: result.data.first_name,
                        last_name: result.data.last_name,
                        company: result.data.company
                    });
                    dispatch(reset('ProfileForm'));
                    this.toggleModal('profileModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
        }
    }

    changePasswordHandle(eventData, dispatch){
        try{
            const params = {
                password: md5(eventData.password)
            };
            if(eventData.password !== eventData.newPassword){
                return Tools.sleep().then(() => {
                    throw new SubmissionError(Tools.errorMessageProcessing('Passwords not matched!'));
                });
            }
            return Tools.apiCall(apiUrls.changePassword, params).then((result) => {
                if(result.success){
                    this.toggleModal('changePasswordModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
        }
    }

    _renderFullName(){
        if(this.props.authReducer.profile){
            return `${this.props.authReducer.profile.first_name || ''} ${this.props.authReducer.profile.last_name || ''}`;
        }
        return '';
    }

    _renderForAdmin(){
        return (
            <table className="table table-striped">
                <tbody>
                    <tr>
                        <td>Email: </td>
                        <td>{this.props.authReducer.profile?this.props.authReducer.profile.email:''}</td>
                    </tr>
                    <tr>
                        <td>Họ tên: </td>
                        <td>
                            {this._renderFullName()}
                        </td>
                    </tr>
                    <tr>
                        <td>Công cụ lấy thông tin vận đơn: </td>
                        <td>
                            <a href={this.state.data.extension_url_grabbing} target="_blank">
                                Link
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        );
    }


    _renderInfo(){
        if(Tools.isAdmin){
            return this._renderForAdmin();
        }else{
            return (
                <UserProfile data={this.state.data}/>
            );
        }
    }

    _renderPromotion () {
        const {promotionLink, promotionMessage} = this.state;
        if (promotionLink !== '0' && promotionMessage !== '0') {
            return (
                <div className="col-md-12">
                    <div className="alert alert-warning" role="alert">
                        <strong>Thông báo: </strong>
                        <a href={promotionLink} target="_blank">{promotionMessage}</a>
                    </div>
                </div>
            )
        }
        return null;
    }

    render() {
        return (
            <NavWrapper data-location={this.props.location} data-user={this.props.authReducer}>
                <div>
                    {this._renderInfo()}

                    <div>
                        <button
                            type="button"
                            className="btn btn-primary"
                            onClick={() => this.toggleModal('profileModal')}>
                            Thông tin cá nhân
                        </button>
                        <button
                            type="button"
                            className="btn btn-success"
                            onClick={() => this.toggleModal('changePasswordModal')}>
                            Đổi mật khẩu
                        </button>
                    </div>

                    <CustomModal
                        open={this.state.profileModal}
                        close={() => this.toggleModal('profileModal', false)}
                        size="md"
                        title="Update profile"
                        >
                        <div>
                            <div className="custom-modal-content">
                                <ProfileForm
                                    checkSubmit={this.updateProfileHandle.bind(this)}
                                    labels={labels.profile}
                                    submitTitle="Update profile">

                                    <button
                                        type="button"
                                        className="btn btn-warning cancel"
                                        onClick={() => this.toggleModal('profileModal', false)}>
                                        <span className="glyphicon glyphicon-remove"></span> &nbsp;
                                        Cancel
                                    </button>
                                </ProfileForm>
                            </div>

                        </div>
                    </CustomModal>

                    <CustomModal
                        open={this.state.changePasswordModal}
                        close={() => this.toggleModal('changePasswordModal', false)}
                        size="md"
                        title="Change password"
                        >
                        <div>
                            <div className="custom-modal-content">
                                <ChangePasswordForm
                                    checkSubmit={this.changePasswordHandle.bind(this)}
                                    labels={labels.changePassword}
                                    submitTitle="Change password">

                                    <button
                                        type="button"
                                        className="btn btn-warning cancel"
                                        onClick={() => this.toggleModal('changePasswordModal', false)}>
                                        <span className="glyphicon glyphicon-remove"></span> &nbsp;
                                        Cancel
                                    </button>
                                </ChangePasswordForm>
                            </div>
                        </div>
                    </CustomModal>

                    <CustomModal
                        open={this.state.popupModal}
                        close={() => this.toggleModal('popupModal', false)}
                        size="md"
                        title="Thông báo"
                        >
                        <div>
                            <div className="custom-modal-content">
                                {this._renderPromotion()}
                            </div>
                        </div>
                    </CustomModal>
                </div>
            </NavWrapper>
        );
    }
}

function mapStateToProps(state){
    return {
    }
}

function mapDispatchToProps(dispatch){
    return {
        ...bindActionCreators(actionCreators, dispatch)
    };
}

Profile.propTypes = {
};

Profile.defaultProps = {
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(Profile);

// export default Profile;
