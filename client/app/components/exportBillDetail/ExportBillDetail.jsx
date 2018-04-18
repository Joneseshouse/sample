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
import ExportBillDetailLayout from './ExportBillDetail.layout';


@connect(state => ({}), dispatch => ({
    ...bindActionCreators(actionCreators, dispatch),
    resetForm: (formName) => {
        dispatch(reset(formName));
    }
}))
class ExportBillDetail extends React.Component {
    static propTypes = {};
    static defaultProps = {};

    constructor(props) {
        super(props);
        this.state = {
            address_uid: null,
            mainModal: false,
            failModal: false,
            itemId: null,
            bulkRemove: true,
            params: {},
            dataLoaded: store.getState().exportBillDetailReducer.listPure.length?true:false
        };
        this.list = this.list.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.handleFilter = this.handleFilter.bind(this);
        this.handleFilterCore = this.handleFilterCore.bind(this);
        this.handleFilterAddressUid = this.handleFilterAddressUid.bind(this);
        this.handleCheck = this.handleCheck.bind(this);
        this.handleCheckAll = this.handleCheckAll.bind(this);
        this.handleRemove = this.handleRemove.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleChangeFail = this.handleChangeFail.bind(this);
        this.handlePageChange = this.handlePageChange.bind(this);
        this.handleSelectItem = this.handleSelectItem.bind(this);

        this.filterTimeout = null;
    }

    setInitData(initData){
        this.props.exportBillDetailAction('listPure', {list: [...initData.data.items], pages: initData.data._meta.last_page});
        this.setState({dataLoaded: true});
    }

    componentDidMount(){
        document.title = 'ExportBillDetail';
        if(window.initData){
            if(window.initData.success){
                this.setInitData(window.initData);
            }else{
                // Pop message here
            }
            window.initData = null;
        }else{
            this.list();
        }
    }

    toggleModal(id=null, state, open=true){
        let newState = {};
        newState[state] = open;

        switch(state){
            case 'mainModal':
                if(open){
                    if(!this.props.exportBillDetailReducer.listSelected.length){
                        alert('Bạn vui lòng chọn ít nhất 1 vận đơn để làm hoá đơn xuất.');
                        return;
                    }
                }
                this.setState(newState);
            break;
            case 'failModal':
                newState.itemId = id;
                if(id && open){
                    Tools.apiCall(apiUrls.cnBillOfLandingFailObj, {id: id}, false).then((result) => {
                        if(result.success){
                            this.props.exportBillDetailAction('obj', {
                                ...result.data
                            });
                            this.setState(newState);
                            return;
                        }
                    });
                }else{
                    this.props.exportBillDetailAction('obj', Tools.getInitData(labels.mainForm));
                    this.setState(newState);
                }
            break;
        }
    }


    list(outerParams={}, page=1){
        let params = {
            page
        };

        if(this.state.address_uid){
            params.address_uid = this.state.address_uid;
        }

        if(!isEmpty(outerParams)){
            params = {...params, ...outerParams};
        }

        return new Promise((resolve, reject) => {
            Tools.apiCall(apiUrls.listPure, params, false).then((result) => {
                if(result.success){
                    this.setInitData(result);
                }
                resolve(result);
            });
        });

    }

    handleFilterCore(keyword){
        let params = {keyword: keyword.toUpperCase()};
        if(this.props.exportBillDetailReducer.address_uid){
            params.address_uid = this.props.exportBillDetailReducer.address_uid;
        }
        this.list(params).then(result => {
            if(result.success){
                if(result.data.items.length === 1){
                    const listSelected = this.props.exportBillDetailReducer.listSelected;
                    if(!listSelected.length || filter(listSelected, {'user_id': parseInt(result.data.items[0].user_id)}).length){
                        this.handleSelectItem(result.data.items[0]);
                        document.getElementById("success-sound").play();
                    }
                    this.props.exportBillDetailAction('keyword', '');
                    this.list();
                }else{
                    // Check failed record here
                    Tools.apiCall(apiUrls.cnBillOfLandingFailObjFilter, {'code': keyword}, false).then((result) => {
                        if(result.success){
                            document.getElementById("warning-sound").play();
                            this.props.exportBillDetailAction('obj', {
                                ...result.data
                            });

                            if(!filter(this.props.exportBillDetailReducer.listFail, {id: result.data.id}).length){
                                this.props.exportBillDetailAction('addFail', result.data);
                            }
                            // this.toggleModal(null, 'failModal');
                        }else{
                            document.getElementById("wrong-sound").play();
                        }
                        this.props.exportBillDetailAction('keyword', '');
                        this.list();
                    });
                }
            }
        });
    }

    handleFilterCoreAddressUid(addressUid){
        this.setState({address_uid: addressUid}, () => this.list());
        // this.list({address_uid: addressUid});
    }

    handleFilter(event){
        let keyword = event.target.value.toUpperCase();
        if(this.filterTimeout !== null){
            clearTimeout(this.filterTimeout);
        }
        this.filterTimeout = setTimeout(() => {
            if(keyword.length > 2){
                this.handleFilterCore(keyword);
            }else if(!keyword.length){
                this.list();
            }
        }, 600);
    }

    handleFilterAddressUid(event){
        let addressUid = event.target.value.toUpperCase();
        if(this.filterTimeout !== null){
            clearTimeout(this.filterTimeout);
        }
        this.filterTimeout = setTimeout(() => {
            if(!addressUid.length || addressUid.length > 2){
                this.handleFilterCoreAddressUid(addressUid);
            }
            /*
            else if(!addressUid.length){
                this.list();
            }
            */
        }, 600);
    }

    handlePageChange(data){
        let page = data.selected + 1;
        this.list({}, page);
    }

    handleCheck(id, checked){
        let index = store.getState().exportBillDetailReducer.list.findIndex(x => x.id===id);
        this.props.exportBillDetailAction('edit', {checked}, index);
    }

    handleCheckAll(){
        let list = this.props.exportBillDetailReducer.list;
        if(filter(list, {checked: true}).length === list.length){
            this.props.exportBillDetailAction('uncheckAll');
        }else{
            this.props.exportBillDetailAction('checkAll');
        }
    }

    handleRemove(id){
        const confirm = window.confirm('Do you want to remove this item(s)?');
        if(!confirm){
            return;
        }
        const index = this.props.exportBillDetailReducer.listSelected.findIndex(x => x.id === parseInt(id));
        this.props.exportBillDetailAction('remove', null, [index]);
    }

    handleChange(eventData, dispatch){
        try{
            const listId = this.props.exportBillDetailReducer.listSelected.map(value => {
                return value.id
            });
            const params = {...eventData, list_id: listId.join(',')};

            return Tools.apiCall(apiUrls.exportBillAdd, params).then((result) => {
                if(result.success){
                    /*
                    const data = {
                        ...result.data
                    };
                    if(id){
                        let index = store.getState().exportBillDetailReducer.list.findIndex(x => x.id===id);
                        this.props.exportBillDetailAction('edit', data, index);
                    }else{
                        this.props.exportBillDetailAction('add', data);
                    }
                    */
                    this.props.exportBillDetailAction('listSelected', {list: []});
                    dispatch(reset('ExportBillDetailMainForm'));
                    this.toggleModal(null, 'mainModal', false);
                    Tools.goToUrl('export_bill', [result.data.id]);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleChangeFail(eventData, dispatch){
        try{
            const params = {...eventData};
            params.id = this.props.exportBillDetailReducer.obj.id;
            return Tools.apiCall(apiUrls.cnBillOfLandingFailEdit, params, false).then((result) => {
                if(result.success){
                    if(typeof result.data !== 'object'){
                        // Get actual object
                        // hanlde select it
                        this.handleFilterCore(params.code);
                        dispatch(reset('CnBillOfLandingFailMainForm'));
                        this.toggleModal(null, 'failModal', false);

                        let index = store.getState().exportBillDetailReducer.listFail.findIndex(x => x.id===result.data);
                        this.props.exportBillDetailAction('removeFail', null, [index]);
                    }else{
                        const data = result.data;
                        let index = store.getState().exportBillDetailReducer.listFail.findIndex(x => x.id===data.id);
                        this.props.exportBillDetailAction('editFail', data, index);
                        this.props.exportBillDetailAction('obj', data);
                    }
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleSelectItem(item){
        this.props.exportBillDetailAction('select', item);
    }

    render() {
        return (
            <ExportBillDetailLayout
                {...this.props}
                dataLoaded={this.state.dataLoaded}
                mainModal={this.state.mainModal}
                failModal={this.state.failModal}
                bulkRemove={this.state.bulkRemove}
                list={this.list}
                toggleModal={this.toggleModal}
                onSelectItem={this.handleSelectItem}
                onFilter={this.handleFilter}
                onFilterAddressUid={this.handleFilterAddressUid}
                onCheck={this.handleCheck}
                onCheckAll={this.handleCheckAll}
                onRemove={this.handleRemove}
                onChange={this.handleChange}
                onChangeFail={this.handleChangeFail}
                onPageChange={this.handlePageChange}
                />
        );
    }
}

export default ExportBillDetail;
