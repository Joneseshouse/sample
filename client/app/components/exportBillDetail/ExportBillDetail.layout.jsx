import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import Table from 'rc-table';
import {STATIC_URL} from 'app/constants';
import {labels} from './_data';
import {labels as failLabels} from 'components/cnBillOfLandingFail/_data';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import PureTable from './tables/PureTable';
import SelectedTable from './tables/SelectedTable';
import FailTable from './tables/FailTable';
import MainForm from './forms/Main.form';
import {default as FailForm} from 'components/cnBillOfLandingFail/forms/Main.form';
import WaitingMessage from 'utils/components/WaitingMessage';


class ExportBillDetailLayout extends React.Component {
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
                <div className="breadcrumb-container">
                    {labels.common.title}
                </div>
                <div className="main-content">
                    <div className="row">
                        <div className="col-md-6">
                            <audio id="success-sound">
                              <source src={STATIC_URL+"sounds/success.wav"} type="audio/wav"/>
                            </audio>
                            <audio id="wrong-sound">
                              <source src={STATIC_URL + "sounds/wrong.wav"} type="audio/wav"/>
                            </audio>
                            <audio id="warning-sound">
                              <source src={STATIC_URL + "sounds/warning.wav"} type="audio/wav"/>
                            </audio>
                            <PureTable {...this.props}/>
                        </div>
                        <div className="col-md-6">
                            <SelectedTable {...this.props}/>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-12">
                            <hr/>
                            <FailTable {...this.props}/>
                        </div>
                    </div>
                </div>

                <CustomModal
                    open={this.props.mainModal}
                    close={() => this.props.toggleModal(null, 'mainModal', false)}
                    size="md"
                    title="Tạo hoá đơn xuất hàng"
                    >
                    <div>
                        <div className="custom-modal-content">
                            <MainForm
                                onSubmit={this.props.onChange}
                                labels={labels.mainForm}
                                initialValues={this.props.exportBillDetailReducer.obj}
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
                <CustomModal
                    open={this.props.failModal}
                    close={() => this.props.toggleModal(null, 'failModal', false)}
                    size="md"
                    title="Quản lý vận đơn TQ lỗi"
                    >
                    <div>
                        <div className="custom-modal-content">
                            <div>
                                <strong className="red">
                                    {this.props.exportBillDetailReducer.obj.error_note}
                                </strong>
                            </div>
                            <hr/>
                            <FailForm
                                onSubmit={this.props.onChangeFail}
                                labels={failLabels.mainForm}
                                initialValues={this.props.exportBillDetailReducer.obj}
                                submitTitle="Save">

                                <button
                                    type="button"
                                    className="btn btn-warning cancel"
                                    onClick={() => this.props.toggleModal(null, 'failModal', false)}>
                                    <span className="glyphicon glyphicon-remove"></span> &nbsp;
                                    Cancel
                                </button>
                            </FailForm>
                        </div>
                    </div>
                </CustomModal>
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

ExportBillDetailLayout.propTypes = {
    bulkRemove: PropTypes.bool
};

ExportBillDetailLayout.defaultProps = {
    bulkRemove: true
};

export default ExportBillDetailLayout;
