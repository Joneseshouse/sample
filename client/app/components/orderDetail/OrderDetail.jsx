import React from 'react';
import PropTypes from 'prop-types';
import forEach from 'lodash/forEach';
import isEmpty from 'lodash/isEmpty';
import keys from 'lodash/keys';
import values from 'lodash/values';
import filter from 'lodash/filter';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { SubmissionError, reset } from 'redux-form';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';

import {USER_ROLES, ADMIN_ROLES} from 'app/constants';
import {apiUrls, labels} from './_data';
import {labels as cartLabels} from 'components/cart/_data';
import Tools from 'helpers/Tools';
import OrderDetailLayout from './OrderDetail.layout';


@connect(state => ({}), dispatch => ({
    ...bindActionCreators(actionCreators, dispatch),
    resetForm: (formName) => {
        dispatch(reset(formName));
    }
}))
class OrderDetail extends React.Component {
    static propTypes = {};
    static defaultProps = {};

    constructor(props) {
        super(props);
        this.state = {
            mainModal: false,
            itemModal: false,
            deliveryFeeModal: false,
            realAmountModal: false,
            unitPriceModal: false,
            purchaseCodeModal: false,
            billOfLandingModal: false,
            previewModal: false,
            noteModal: false,
            logModal: false,
            itemId: null,
            bulkRemove: true,
            params: {},
            rate: 3400,
            dataLoaded: store.getState().orderDetailReducer.list.length?true:false
        };
        this.setInitData = this.setInitData.bind(this);
        this.list = this.list.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.handleFilter = this.handleFilter.bind(this);
        this.handleCheck = this.handleCheck.bind(this);
        this.handleCheckAll = this.handleCheckAll.bind(this);
        this.handleRemove = this.handleRemove.bind(this);
        this.handleRemoveCode = this.handleRemoveCode.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleChangeOrderItem = this.handleChangeOrderItem.bind(this);
        this.handleChangeRealAmount = this.handleChangeRealAmount.bind(this);
        this.handleChangeUnitPrice = this.handleChangeUnitPrice.bind(this);
        this.handleChangeDeliveryFee = this.handleChangeDeliveryFee.bind(this);
        this.handleChangePurchaseCode = this.handleChangePurchaseCode.bind(this);
        this.handleChangePurchaseNote = this.handleChangePurchaseNote.bind(this);
        this.handleChangeBillOfLanding = this.handleChangeBillOfLanding.bind(this);
        this.handlePageChange = this.handlePageChange.bind(this);
        this.handleToggleLog = this.handleToggleLog.bind(this);
        this.handleRemoveEmptyOrderItems = this.handleRemoveEmptyOrderItems.bind(this);
        this.handleRemoveSelectedItems = this.handleRemoveSelectedItems.bind(this);

        this.filterTimeout = null;
    }

    setInitData(initData){
        const listAddress = Tools.renameColumn(initData.extra.list_address, 'address');
        const listAdmin = Tools.renameColumn(initData.extra.list_admin, 'full_name');
        const listCheckItemStatus = initData.extra.list_check_item_status;
        if(listAdmin.length){
            this.props.orderDetailAction('defaultAdmin', listAdmin[0].id);
        }
        this.props.orderDetailAction('listAddress', {list: [...listAddress]});
        this.props.orderDetailAction('listAdmin', {list: [...listAdmin]});
        this.props.orderDetailAction('objCheckItemStatus', {...listCheckItemStatus});

        this.setState({rate: parseInt(initData.data.rate)});
        this.setState({order_fee_factor: parseInt(initData.data.order_fee_factor)});
        if(initData.data.type === 'normal'){
            let purchases = [...initData.data.purchases];
            let counter = 1;
            forEach(purchases, (purchase, i) => {
                forEach(purchase.order_items, (orderItem, j) => {
                    purchases[i].order_items[j].counter = counter;
                    counter++;
                });
            });
            this.props.orderDetailAction('newList', {list: [...purchases], pages: 1});
            delete initData.data.purchases;
            this.props.orderDetailAction('obj', {
                ...initData.data
            });
        }else{
            this.props.orderDetailAction('listBillOfLanding', {list: [...initData.data.purchases[0].bills_of_landing]});
            delete initData.data.purchases;
            this.props.orderDetailAction('obj', {
                ...initData.data
            });
        }

        this.setState({dataLoaded: true});
    }

    componentDidMount(){
        document.title = 'OrderDetail';
        // if(!this.props.orderDetailReducer.list.length){
            if(window.initData){
                if(window.initData.success){
                    this.setInitData(window.initData);
                }else{
                    // Pop message here
                }
                window.initData = null;
            }else{
                // this.list();
                Tools.apiCall(apiUrls.obj, {id: this.props.params.id}, false).then((result) => {
                    if(result.success){
                        this.setInitData(result);
                    }
                    this.setState({dataLoaded: true});
                });
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
                            this.props.orderDetailAction('obj', {
                                ...result.data
                            });
                            this.setState(newState);
                            return;
                        }
                    });
                }else{
                    this.props.orderDetailAction('obj', {...Tools.getInitData(labels[state])});
                    this.setState(newState);
                }
            break;
            case 'itemModal':
            case 'unitPriceModal':
                newState.itemId = id;
                if(id && open){
                    Tools.apiCall(apiUrls.orderItemObj, {id: id}, false).then((result) => {
                        if(result.success){
                            this.props.orderDetailAction('objItem', {
                                ...result.data
                            });
                            this.setState(newState);
                            return;
                        }
                    });
                }else{
                    this.props.orderDetailAction('objItem', {...Tools.getInitData(cartLabels['mainModal'])});
                    this.setState(newState);
                }
            break;
            case 'realAmountModal':
            case 'purchaseCodeModal':
            case 'purchaseNoteModal':
            case 'deliveryFeeModal':
                newState.itemId = id;
                if(id && open){
                    Tools.apiCall(apiUrls.purchaseObj, {id: id}, false).then((result) => {
                        if(result.success){
                            this.props.orderDetailAction('objPurchase', {
                                ...result.data
                            });
                            this.setState(newState);
                            return;
                        }
                    });
                }else{
                    this.props.orderDetailAction('objPurchase', {...Tools.getInitData(labels[state])});
                    this.setState(newState);
                }
            break;
            case 'billOfLandingModal':
                newState.itemId = id;
                let params = {};
                if(id){
                    params.id = id;
                }else{
                    params = {
                        order_id: this.props.params.id,
                        purchase_id: this.props.orderDetailReducer.selectedShop
                    }
                }
                if(id && open){
                    Tools.apiCall(apiUrls.billOfLandingObj, params, false).then((result) => {
                        if(result.success){
                            this.props.orderDetailAction('objBillOfLanding', {
                                ...result.data
                            });
                            result.data.landing_status = result.data.landing_status.split(':')[0];
                            this.props.billOfLandingAction('obj', {
                                ...result.data
                            });
                            this.setState(newState);
                            return;
                        }
                    });
                }else{
                    this.props.orderDetailAction('objBillOfLanding', {...Tools.getInitData(labels['billOfLandingForm'])});
                    this.props.billOfLandingAction('obj', {...Tools.getInitData(labels['billOfLandingForm'])});
                    this.setState(newState);
                }
            break;
            case 'previewModal':
                newState.itemId = id;
                this.setState(newState);
            break;
            case 'noteModal':
                newState.itemId = id;
                if(id && open){
                    Tools.apiCall(apiUrls.orderItemNoteList, {order_item_id: id}, false).then((result) => {
                        if(result.success){
                            this.props.orderDetailAction('listNote', {
                                list: result.data.items,
                                orderItemId: id
                            });
                            this.setState(newState);
                        }
                    });
                }else{
                    this.setState(newState);
                }
            break;
        }
    }

    handleToggleLog(id, open=true){
        let newState = {
            logModal: open,
            itemId: id
        };
        this.setState(newState);
    }


    list(outerParams={}, page=1){
        let params = {
            page
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

    handleFilter(event){
        let keyword = event.target.value;
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

    handlePageChange(data){
        let page = data.selected + 1;
        this.list({}, page);
    }

    handleCheck(id, checked){
        let index = store.getState().orderDetailReducer.list.findIndex(x => x.id===id);
        this.props.orderDetailAction('edit', {checked}, index);
    }

    handleCheckAll(){
        let list = this.props.orderDetailReducer.list;
        if(filter(list, {checked: true}).length === list.length){
            this.props.orderDetailAction('uncheckAll');
        }else{
            this.props.orderDetailAction('checkAll');
        }
    }

    handleRemove(id=null){
        const confirm = window.confirm('Do you want to remove this item(s)?');
        if(!confirm){
            return;
        }
        if(id === null){
            let listId = [];
            this.props.orderDetailReducer.list.map(value => {
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
        Tools.apiCall(apiUrls.orderItemRemove, {id}).then((result) => {
            if(result.success){
                this.setInitData(result.extra.order);
            }
        });
    }

    handleRemoveCode(id=null){
        const confirm = window.confirm('Do you want to remove this code?');
        if(!confirm){
            return;
        }
        id = String(id);
        Tools.apiCall(apiUrls.billOfLandingRemove, {id}).then((result) => {
            if(result.success){
                this.setInitData(result.extra.order);
            }
        });
    }

    handleChange(eventData, dispatch){
        try{
            const params = {
                listOrderItem: JSON.stringify([{
                    order_id: parseInt(this.props.params.id),
                    title: eventData.title,
                    shop_name: eventData.shop_name,
                    url: eventData.url,
                    vendor: Tools.getVendor(eventData.url),
                    avatar: eventData.avatar,
                    properties: eventData.properties,
                    unit_price: parseFloat(eventData.unit_price),
                    quantity: parseInt(eventData.quantity),
                    message: eventData.message
                }])
            };
            return Tools.apiCall(apiUrls.addFull, params).then((result) => {
                if(result.success){
                    this.setInitData(result.extra.order);
                    dispatch(reset('OrderDetailMainForm'));
                    this.toggleModal(null, 'mainModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleChangeOrderItem(eventData, dispatch){
        try{
            const params = {
                id: parseInt(eventData.id),
                quantity: parseInt(eventData.quantity),
                properties: eventData.properties,
                message: eventData.message
            };
            return Tools.apiCall(apiUrls.orderItemEdit, params).then((result) => {
                if(result.success){
                    this.setInitData(result.extra.order);
                    dispatch(reset('CartMainForm'));
                    this.toggleModal(null, 'itemModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleChangeUnitPrice(eventData, dispatch){
        try{
            const params = {
                id: parseInt(eventData.id),
                unit_price: parseFloat(eventData.unit_price)
            };
            return Tools.apiCall(apiUrls.orderItemEditUnitPrice, params).then((result) => {
                if(result.success){
                    this.setInitData(result.extra.order);
                    dispatch(reset('UnitPriceForm'));
                    this.toggleModal(null, 'unitPriceModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleChangeRealAmount(eventData, dispatch){
        try{
            const params = {
                id: parseInt(eventData.id),
                real_amount: parseFloat(eventData.real_amount)
            };
            return Tools.apiCall(apiUrls.purchaseEdit, params).then((result) => {
                if(result.success){
                    this.setInitData(result.extra.order);
                    dispatch(reset('RealAmountForm'));
                    this.toggleModal(null, 'realAmountModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleChangeDeliveryFee(eventData, dispatch){
        try{
            const params = {
                id: parseInt(eventData.id),
                // delivery_fee_unit: parseFloat(eventData.delivery_fee_unit),
                inland_delivery_fee_raw: parseFloat(eventData.inland_delivery_fee_raw)
            };
            return Tools.apiCall(apiUrls.purchaseEdit, params).then((result) => {
                if(result.success){
                    this.setInitData(result.extra.order);
                    dispatch(reset('DeliveryFeeForm'));
                    this.toggleModal(null, 'deliveryFeeModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleRefreshDeliveryFeeUnit(eventData, dispatch){
        const confirm = window.confirm('Bạn có muốn cập nhật lại đơn giá vận chuyển?');
        if(!confirm){
            return;
        }
        id = String(id);
        Tools.apiCall(apiUrls.billOfLandingRemove, {id}).then((result) => {
            if(result.success){
                this.setInitData(result.extra.order);
            }
        });
    }

    handleChangePurchaseCode(eventData, dispatch){
        try{
            const params = {
                id: parseInt(eventData.id),
                code: eventData.code
            };
            return Tools.apiCall(apiUrls.purchaseEdit, params).then((result) => {
                if(result.success){
                    this.setInitData(result.extra.order);
                    dispatch(reset('PurchaseCodeForm'));
                    this.toggleModal(null, 'purchaseCodeModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleChangePurchaseNote(eventData, dispatch){
        try{
            const params = {
                id: parseInt(eventData.id),
                note: eventData.note
            };
            return Tools.apiCall(apiUrls.purchaseEdit, params).then((result) => {
                if(result.success){
                    this.setInitData(result.extra.order);
                    dispatch(reset('PurchaseNoteForm'));
                    this.toggleModal(null, 'purchaseNoteModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleChangeBillOfLanding(eventData, dispatch){
        try{
            let params = {
                order_id: parseInt(this.props.params.id),
                purchase_id: parseInt(this.props.orderDetailReducer.selectedShop),
                code: eventData.code,
                packages: parseInt(eventData.packages),
                input_mass: parseFloat(eventData.input_mass),
                transform_factor: parseInt(eventData.transform_factor),
                length: parseInt(eventData.length),
                width: parseInt(eventData.width),
                height: parseInt(eventData.height),
                wooden_box: eventData.wooden_box,
                straight_delivery: eventData.straight_delivery,
                insurance_register: eventData.insurance_register,
                insurance_value: parseFloat(eventData.insurance_value),
                address_code: eventData.address_code,
                purchase_code: eventData.purchase_code,
                landing_status: eventData.landing_status,
                note: eventData.note
            };

            if(typeof eventData.insurance_register != 'undefined'){
                params.insurance_register = eventData.insurance_register;
            }
            if(typeof eventData.insurance_value != 'undefined'){
                params.insurance_value = parseFloat(eventData.insurance_value);
            }

            if(isNaN(params.purchase_id)){
                params.purchase_id = 0;
            }
            const id = this.state.itemId;
            if(id){
                if(!Tools.isAdmin){
                    // User edit item
                    delete params.purchase_id;
                    delete params.transform_factor;
                    delete params.length;
                    delete params.width;
                    delete params.height;
                    params.insurance_register = eventData.insurance_register;
                    params.insurance_value = eventData.insurance_value;
                }
            }
            return Tools.apiCall(
                apiUrls[id?'billOfLandingEdit':'billOfLandingAdd'], 
                id?{...params, id}:params).then((result) => {
                if(result.success){
                    this.setInitData(result.extra.order);
                    dispatch(reset('BillOfLandingMainForm'));
                    this.toggleModal(null, 'billOfLandingModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleRemoveEmptyOrderItems(){
        let confirm = window.confirm('Bạn có muốn xoá tất cả các sản phẩm có số lượng = 0 ?');
        if(!confirm) return;
        return Tools.apiCall(apiUrls.orderItemEmpty, {order_id: this.props.params.id}).then((result) => {
            if(result.success){
                Tools.apiCall(apiUrls.obj, {id: this.props.params.id}, false).then((result) => {
                    if(result.success){
                        this.setInitData(result);
                    }
                });
            }
        });
    }

    handleRemoveSelectedItems(){
        const listPurchase = this.props.orderDetailReducer.list;
        let listChecked = [];
        for (let purchase of listPurchase) {
            for (let item of purchase.order_items) {
                if (item.checked) {
                    listChecked.push(item.id);
                }
            } 
        }
        if (listChecked.length) {
            this.handleRemove(listChecked.join(','));
        }
        /*
        return Tools.apiCall(apiUrls.orderItemEmpty, {order_id: this.props.params.id}).then((result) => {
            if(result.success){
                Tools.apiCall(apiUrls.obj, {id: this.props.params.id}, false).then((result) => {
                    if(result.success){
                        this.setInitData(result);
                    }
                });
            }
        });
        */
    }

    render() {
        return (
            <OrderDetailLayout
                {...this.props}
                setInitData={this.setInitData}
                dataLoaded={this.state.dataLoaded}
                mainModal={this.state.mainModal}
                itemModal={this.state.itemModal}
                deliveryFeeModal={this.state.deliveryFeeModal}
                realAmountModal={this.state.realAmountModal}
                unitPriceModal={this.state.unitPriceModal}
                purchaseCodeModal={this.state.purchaseCodeModal}
                purchaseNoteModal={this.state.purchaseNoteModal}
                billOfLandingModal={this.state.billOfLandingModal}
                previewModal={this.state.previewModal}
                noteModal={this.state.noteModal}
                logModal={this.state.logModal}
                bulkRemove={this.state.bulkRemove}
                rate={this.state.rate}
                order_fee_factor={this.state.order_fee_factor}
                itemId={this.state.itemId}
                list={this.list}
                toggleModal={this.toggleModal}
                onFilter={this.handleFilter}
                onCheck={this.handleCheck}
                onCheckAll={this.handleCheckAll}
                onRemove={this.handleRemove}
                onRemoveCode={this.handleRemoveCode}
                onChange={this.handleChange}
                onChangeOrderItem={this.handleChangeOrderItem}
                onChangeRealAmount={this.handleChangeRealAmount}
                onChangeUnitPrice={this.handleChangeUnitPrice}
                onChangeDeliveryFee={this.handleChangeDeliveryFee}
                onChangePurchaseCode={this.handleChangePurchaseCode}
                onChangePurchaseNote={this.handleChangePurchaseNote}
                onChangeBillOfLanding={this.handleChangeBillOfLanding}
                onPageChange={this.handlePageChange}
                onToggleLog={this.handleToggleLog}
                onRemoveEmptyOrderItems={this.handleRemoveEmptyOrderItems}
                onRemoveSelectedItems={this.handleRemoveSelectedItems}
                />
        );
    }
}

export default OrderDetail;
