import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import Table from 'rc-table';
import {labels} from './_data';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import MainTable from './tables/Main.table';
import MainForm from './forms/Main.form';
import FilterForm from './forms/Filter.form';
import WaitingMessage from 'utils/components/WaitingMessage';


class UserTransactionLayout extends React.Component {
    static propTypes = {
        bulkRemove: PropTypes.bool
    };
    static defaultProps = {
        bulkRemove: true
    };

    constructor(props) {
        super(props);
        this.state = {};
        this._renderTotalTable = this._renderTotalTable.bind(this);
    }

    _renderTotalTable(){
        const {total} = this.props;
        let missing = total.purchasing - total.balance;
        if (missing < 0) {
            missing = 0;
        }
        return (
            <div className="alert alert-success" role="alert">
                <div className="row">
                    <div className="col-md-4">
                        <strong>Số dư: </strong> {Tools.numberFormat(total.balance)}₫
                    </div>
                    <div className="col-md-4 center-align">
                        <strong>Đang giao dịch: </strong> {Tools.numberFormat(total.purchasing)}₫
                    </div>
                    <div className="col-md-4 right-align">
                        <strong>Còn thiếu: </strong> {Tools.numberFormat(total.missing)}₫
                    </div>
                </div>
            </div>
        );
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
                    <FilterForm
                        onSubmit={this.props.onFilter}
                        labels={labels.filterForm}
                        submitTitle="Tìm kiếm"/>
                    <br/>
                    {this._renderTotalTable()}
                    <MainTable
                        {...this.props}
                        total={this.props.total}
                        listType={this.props.userTransactionReducer.listType}
                        listMoneyType={this.props.userTransactionReducer.listMoneyType}
                        listItem={this.props.userTransactionReducer.list}/>
                </div>

                <CustomModal
                    open={this.props.mainModal}
                    close={() => this.props.toggleModal(null, 'mainModal', false)}
                    size="md"
                    title="UserTransaction manager"
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

export default UserTransactionLayout;
