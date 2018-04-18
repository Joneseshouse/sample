import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import Table from 'rc-table';
import {labels} from './_data';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import WaitingMessage from 'utils/components/WaitingMessage';
import { SubmissionError, reset } from 'redux-form';
import DeliveryNote from './components/deliveryNote/DeliveryNote';
import MainForm from './forms/Main.form';


class ExportBillPreviewLayout extends React.Component {
    static propTypes = {
        bulkRemove: PropTypes.bool
    };
    static defaultProps = {
        bulkRemove: true
    };

    constructor(props) {
        super(props);
        this.state = {};
    }

    _renderContent(){
        if(!this.props.dataLoaded){
            return <WaitingMessage/>
        }
        return (
            <div>
                <div className="breadcrumb-container non-printable">
                    {labels.common.title}
                </div>
                <div className="main-content">
                    <DeliveryNote
                        toggleModal={this.props.toggleModal}
                        data={this.props.exportBillPreviewReducer.obj}
                        address={this.props.exportBillPreviewReducer.address}/>

                    <CustomModal
                    open={this.props.mainModal}
                    close={() => this.props.toggleModal(null, 'mainModal', false)}
                    size="md"
                    title="Đổi địa chỉ trên phiếu xuất"
                    >
                        <div>
                            <div className="custom-modal-content">
                                <MainForm
                                    onSubmit={this.props.onChange}
                                    labels={labels.mainForm}
                                    submitTitle="Save">

                                    <button
                                        type="button"
                                        className="btn btn-warning cancel"
                                        onClick={() => this.props.toggleModal(null, 'mainModal', false)}>
                                        <span className="glyphicon glyphicon-remove"></span> &nbsp;
                                        Cancel
                                    </button>
                                </MainForm>
                            </div>
                        </div>
                    </CustomModal>
                </div>

            </div>
        );
    }

    render() {
        return (
            <NavWrapper data-location={this.props.location} data-user={this.props.authReducer}>
                <div>
                    {this._renderContent()}
                </div>
            </NavWrapper>
        );
    }
}

export default ExportBillPreviewLayout;
