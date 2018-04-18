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
import ExportBillLayout from './ExportBill.layout';


@connect(state => ({}), dispatch => ({
    ...bindActionCreators(actionCreators, dispatch),
    resetForm: (formName) => {
        dispatch(reset(formName));
    }
}))
class ExportBill extends React.Component {
    static propTypes = {};
    static defaultProps = {};

    constructor(props) {
        super(props);
        this.state = {
            mainModal: false,
            itemId: null,
            bulkRemove: true,
            params: {},
            dataLoaded: store.getState().exportBillReducer.list.length?true:false,
            total: {
                amount: 0
            }
        };
        this.list = this.list.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.handleFilter = this.handleFilter.bind(this);
        this.handleCheck = this.handleCheck.bind(this);
        this.handleCheckAll = this.handleCheckAll.bind(this);
        this.handleRemove = this.handleRemove.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handlePageChange = this.handlePageChange.bind(this);

        this.filterTimeout = null;
    }

    setInitData(initData){
        let listAdmin = Tools.renameColumn(initData.extra.list_admin, 'full_name');
        listAdmin.unshift({id: 0, title: '--- Tất cả NV ---'});

        this.props.exportBillAction('newList', {
            list: [...initData.data.items],
            listAdmin: [...listAdmin],
            pages: initData.data._meta.last_page}
        );
        this.setState({
            dataLoaded: true,
            total: initData.extra.total
        });
    }

    componentDidMount(){
        document.title = 'ExportBill';
        // if(!this.props.exportBillReducer.list.length){
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
        // }
    }

    toggleModal(id=null, state, open=true){
        let newState = {};
        newState[state] = open;

        switch(state){
            case 'mainModal':
                newState.itemId = id;
                if(id && open){
                    Tools.apiCall(apiUrls.obj, {id: id}, false).then((result) => {
                        if(result.success){
                            this.props.exportBillAction('obj', {
                                ...result.data
                            });
                            this.setState(newState);
                            return;
                        }
                    });
                }else{
                    this.props.exportBillAction('obj', Tools.getInitData(labels.mainForm));
                    this.setState(newState);
                }
            break;
        }
    }

    list(outerParams={}, page=1){
        let params = {
            page,
            ...this.state.params
        };

        if(!isEmpty(outerParams)){
            params = {...params, ...outerParams};
        }

        Tools.apiCall(apiUrls.list, params, false).then((result) => {
            if(result.success){
                this.setInitData(result);
            }
        });
    }

    /*
    handleFilter(event){
        let keyword = event.target.value.toUpperCase();
        if(this.filterTimeout !== null){
            clearTimeout(this.filterTimeout);
        }
        this.filterTimeout = setTimeout(() => {
            if(keyword.length > 2){
                this.list({keyword: keyword});
            }else if(!keyword.length){
                this.list();
            }
        }, 600);
    }
    */

    handleFilter(eventData, dispatch){
        try{
            this.setState(
                {
                    params: {
                        date_range: eventData.date_range?(eventData.date_range[0] ? eventData.date_range.join(',') : ''):'',
                        uid: eventData.uid?eventData.uid:null,
                        address_code: eventData.address_code?eventData.address_code:null,
                        admin_id: eventData.admin_id?eventData.admin_id:0
                    }
                }, () => this.list()
            );
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handlePageChange(data){
        let page = data.selected + 1;
        this.list({}, page);
    }

    handleCheck(id, checked){
        let index = store.getState().exportBillReducer.list.findIndex(x => x.id===id);
        this.props.exportBillAction('edit', {checked}, index);
    }

    handleCheckAll(){
        let list = this.props.exportBillReducer.list;
        if(filter(list, {checked: true}).length === list.length){
            this.props.exportBillAction('uncheckAll');
        }else{
            this.props.exportBillAction('checkAll');
        }
    }

    handleRemove(id=null){
        const confirm = window.confirm('Do you want to remove this item(s)?');
        if(!confirm){
            return;
        }
        if(id === null){
            let listId = [];
            this.props.exportBillReducer.list.map(value => {
                if(value.checked){
                    listId.push(value.id);
                }
            });
            if(!listId.length){
                window.alert("Bạn vui lòng chọn ít nhất 1 phần tử để xoá.");
                return;
            }
            id = listId.join(',');
        }else{
            id = String(id);
        }
        Tools.apiCall(apiUrls.remove, {id}).then((result) => {
            if(result.success){
                let listId = result.data.id;
                if(typeof listId !== 'object'){
                    listId = [listId];
                }
                let listIndex = listId.map(id => {
                    return store.getState().exportBillReducer.list.findIndex(x => x.id === parseInt(id));
                });
                this.props.exportBillAction('remove', null, listIndex);
            }
        });
    }

    handleChange(eventData, dispatch){
        try{
            const params = {...eventData};
            const id = this.state.itemId;
            return Tools.apiCall(apiUrls[id?'edit':'add'], id?{...params, id}:params).then((result) => {
                if(result.success){
                    const data = {
                        ...result.data
                    };
                    if(id){
                        let index = store.getState().exportBillReducer.list.findIndex(x => x.id===id);
                        this.props.exportBillAction('edit', data, index);
                    }else{
                        this.props.exportBillAction('add', data);
                    }
                    dispatch(reset('ExportBillMainForm'));
                    this.toggleModal(null, 'mainModal', false);
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
            <ExportBillLayout
                {...this.props}
                total={this.state.total}
                dataLoaded={this.state.dataLoaded}
                mainModal={this.state.mainModal}
                bulkRemove={this.state.bulkRemove}
                list={this.list}
                toggleModal={this.toggleModal}
                onFilter={this.handleFilter}
                onCheck={this.handleCheck}
                onCheckAll={this.handleCheckAll}
                onRemove={this.handleRemove}
                onChange={this.handleChange}
                onPageChange={this.handlePageChange}
                />
        );
    }
}

export default ExportBill;
