import React from 'react';
import PropTypes from 'prop-types';
import isEmpty from 'lodash/isEmpty';
import forEach from 'lodash/forEach';
import keys from 'lodash/keys';
import values from 'lodash/values';
import filter from 'lodash/filter';
import find from 'lodash/find';
import findIndex from 'lodash/findIndex';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { SubmissionError, reset } from 'redux-form';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import { BASE_URL } from 'app/constants';
import {apiUrls, labels} from './_data';
import Tools from 'helpers/Tools';
import CartLayout from './Cart.layout';


@connect(state => ({}), dispatch => ({
    ...bindActionCreators(actionCreators, dispatch),
    resetForm: (formName) => {
        dispatch(reset(formName));
    }
}))
class Cart extends React.Component {
    static propTypes = {};
    static defaultProps = {};

    constructor(props) {
        super(props);
        this.state = {
            rate: 3400,
            mainModal: false,
            manualModal: false,
            previewModal: false,
            itemId: null,
            bulkRemove: true,
            params: {}
        };
        this.list = this.list.bind(this);
        this.toggleModal = this.toggleModal.bind(this);
        this.handleFilter = this.handleFilter.bind(this);
        this.handleCheck = this.handleCheck.bind(this);
        this.handleCheckAll = this.handleCheckAll.bind(this);
        this.handleCheckAllShop = this.handleCheckAllShop.bind(this);
        this.handleRemove = this.handleRemove.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handlePageChange = this.handlePageChange.bind(this);
        this.handleAddOrder = this.handleAddOrder.bind(this);
        this.handleUpload = this.handleUpload.bind(this);
        this.setInitData = this.setInitData.bind(this);
        this.hanldeManualAdd = this.hanldeManualAdd.bind(this);
        this.handleSaveDraft = this.handleSaveDraft.bind(this);
        this.checkForInit = this.checkForInit.bind(this);
        this.filterTimeout = null;
    }

    componentDidMount(){
        document.title = 'Cart';
        this.checkForInit();
    }

    checkForInit(){
        let data = Tools.getStorage('orderItems');
        if(!data){
            data = [];
        }
        data = JSON.stringify(data);
        Tools.apiCall(apiUrls.cartItemAdd, {data}, false).then(result => {
            if(result.success){
                this.setInitData(result.data.items);
                Tools.setStorage('orderItems', []);
            }
        });
    }

    setInitData(listItemStorage){
        let listItem = Tools.orderItemsParse(listItemStorage);
        this.props.cartAction('newList', {list: listItem, pages: 1});
    }

    toggleModal(id=null, state, open=true){
        let newState = {};
        newState[state] = open;
        switch(state){
            case 'mainModal':
                newState.itemId = id;
                if(id && open){
                    Tools.apiCall(apiUrls.cartItemObj, {id}, false).then((result) => {
                        if(result.success){
                            this.props.cartAction('obj', {
                                ...result.data
                            });
                            this.setState(newState);
                            return;
                        }
                    });
                }else{
                    this.props.cartAction('obj', Tools.getInitData(labels.mainForm));
                    this.setState(newState);
                }
            break;
            case 'manualModal':
                this.props.cartAction('obj', Tools.getInitData(labels.manualForm));
                this.setState(newState);
            break;
            case 'previewModal':
                newState.itemId = id;
                this.setState(newState);
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

        Tools.apiCall(apiUrls.cartItemList, params, false).then((result) => {
            if(result.success){
                // this.props.cartAction('newList', {list: result.data.items, pages: result.data._meta.last_page});
                this.setInitData(result.data.items);
            }
        });
    }

    handlePageChange(data){
        let page = data.selected + 1;
        this.list({}, page);
    }

    totalCalculating(){
        let totalSelected = 0;
        let totalAll = 0;
        let rate = 0;
        forEach(this.props.cartReducer.list.shops, shop => {
            forEach(shop.items, item => {
                totalAll += item.quantity * parseFloat(item.unit_price);
                if(item.rate && !rate){
                    rate = item.rate
                }
                if(item.checked){
                    totalSelected += item.quantity * parseFloat(item.unit_price);
                }
            });
        });
        if(!totalSelected){
            totalSelected = totalAll;
        }
        return {
            totalSelected,
            totalSelectedWithRate: totalSelected * rate
        }
    }

    handleCheck(id, shopIndex, checked){
        let totalSelected = 0;
        let totalAll = 0;
        let rate = 0;
        let index = store.getState().cartReducer.list.shops[shopIndex].items.findIndex(x => x.id===id);
        this.props.cartAction('edit', {checked}, shopIndex, index); // Mark checked
        setTimeout(() => {
            this.props.cartAction('getTotalWhenChecked', this.totalCalculating());
        }, 100);
    }

    handleCheckAll(){
        let list = this.props.cartReducer.list;
        let totalCheck = 0;
        let totalItem = 0;
        forEach(this.props.cartReducer.list.shops, shop => {
            totalItem += shop.items.length;
            forEach(shop.items, item => {
                if(item.checked){
                    totalCheck++;
                }
            });
        });

        if(totalCheck === totalItem){
            this.props.cartAction('uncheckAll');
        }else{
            this.props.cartAction('checkAll');
        }

        setTimeout(() => {
            this.props.cartAction('getTotalWhenChecked', this.totalCalculating());
        }, 100);
    }

    handleCheckAllShop(shopIndex){
        let list = this.props.cartReducer.list;
        let totalCheck = 0;
        let totalItem = this.props.cartReducer.list.shops[shopIndex].items.length;
        forEach(this.props.cartReducer.list.shops[shopIndex].items, item => {
            if(item.checked){
                totalCheck++;
            }
        });

        if(totalCheck === totalItem){
            this.props.cartAction('uncheckAllShop', null, shopIndex);
        }else{
            this.props.cartAction('checkAllShop', null, shopIndex);
        }

        setTimeout(() => {
            this.props.cartAction('getTotalWhenChecked', this.totalCalculating());
        }, 100);
    }

    handleRemove(id=null){
        let listItemStorage = Tools.getStorage('orderItems');
        let message = '';
        let listId = [];
        if(id){
            message = 'Bạn có muốn xoá sản phẩm này?';
            listId.push(id);
        }else{
            message = 'Bạn có muốn xoá các sản phẩm được chọn?';
            forEach(this.props.cartReducer.list.shops, shop => {
                forEach(shop.items, item => {
                    if(item.checked){
                        listId.push(item.id);
                    }
                });
            });
            if(!listId.length){
                alert('Bạn vui lòng chọn ít nhất 1 sản phẩm để xoá.');return;
            }
        }

        const confirm = window.confirm(message);
        if(!confirm){
            return;
        }

        Tools.apiCall(apiUrls.cartItemRemove, {id: listId.length > 1 ? listId.join(',') : listId[0]}).then((result) => {
            if(result.success){
                this.list();
            }
        });
    }

    handleChange(eventData, dispatch){
        try{
            const params = {
                id: parseInt(this.props.cartReducer.obj.id),
                quantity: parseInt(eventData.quantity),
                properties: eventData.properties,
                message: eventData.message
            };
            return Tools.apiCall(apiUrls.cartItemEdit, params).then((result) => {
                if(result.success){
                    this.checkForInit();
                    dispatch(reset('CartMainForm'));
                    this.toggleModal(null, 'mainModal', false);
                }else{
                    throw new SubmissionError(Tools.errorMessageProcessing(result.message));
                }
            });
        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    hanldeManualAdd(eventData, dispatch){
        try{
            let listItem = Tools.getStorage('orderItems');
            const value = {
                ...eventData,
                vendor: Tools.getVendor(eventData.url),
                rate: this.state.rate,
                id: Math.floor((Math.random() * 999999999) + 1)};
            var matchIndex = findIndex(listItem, {url: value.url, properties: value.properties, unit_price: value.unit_price});
            if(matchIndex !== -1){
                // Match -> Increase quantity
                listItem[matchIndex].quantity = parseInt(listItem[matchIndex].quantity) + parseInt(value.quantity);
            }else{
                // Not Match -> Insert
                listItem.push(value);
            }
            Tools.setStorage('orderItems', listItem);
            this.toggleModal(null, 'manualModal', false);
            this.checkForInit();
        }catch(error){
            console.error(error);
        }
    }

    handleFilter(eventData, dispatch){
        try{
            this.setState(
                {
                    params: {
                        date_range: eventData.date_range?(eventData.date_range[0] ? eventData.date_range.join(',') : ''):'',
                        link: eventData.link?eventData.link:null,
                        shop: eventData.shop?eventData.shop:null
                    }
                }, () => this.list()
            );

        }catch(error){
            console.error(error);
            throw new SubmissionError(Tools.errorMessageProcessing(error));
        }
    }

    handleAddOrder(){
        if(!Tools.getStorage('authData')){
            alert('Bạn vui lòng đăng nhập trước khi tạo đơn hàng.');
            return;
        }
        /*
        if(!Tools.getStorage('orderItems') || !Tools.getStorage('orderItems').length){
            alert('Bạn vui lòng cho vào giỏ hàng ít nhất 1 sản phẩm để tạo đơn hàng.');
            return;
        }
        */
        if(!this.props.cartReducer.list){
            alert('Bạn vui lòng cho vào giỏ hàng ít nhất 1 sản phẩm để tạo đơn hàng.');
            return;
        }

        let listItem = [];
        let listRemain = [];
        forEach(this.props.cartReducer.list.shops, shop => {
            forEach(shop.items, item => {
                if(item.checked){
                    listItem.push(item);
                }else{
                    listRemain.push(item);
                }
            });
        });

        if(!listItem.length){
            const confirm = window.confirm('Bạn chưa chọn sản phẩm nào trong giỏ hàng. Bạn có muốn đưa toàn bộ sản phẩm trong giỏ hàng vào đơn hàng không?');
            if(confirm){
                listItem = [...listRemain];
                listRemain = [];
            }else{
                return;
            }
        }
        const params = {
            listOrderItem: JSON.stringify(listItem),
            draft: false
        };
        Tools.apiCall(apiUrls.addFull, params).then((result) => {
            if(result.success){

                Tools.setStorage('orderItems', listRemain);
                this.props.cartAction('newList', {list: listRemain, pages: 1});
                setTimeout(()=>{
                    window.top.location = BASE_URL + 'order/normal/new/' + result.data.id;
                }, 200);

            }
        });
    }

    handleSaveDraft(){
        if(!Tools.getStorage('authData')){
            alert('Bạn vui lòng đăng nhập trước khi lưu đơn hàng.');
            return;
        }

        if(!this.props.cartReducer.list){
            alert('Bạn vui lòng cho vào giỏ hàng ít nhất 1 sản phẩm để tạo đơn hàng.');
            return;
        }

        const confirm = window.confirm('Bạn có muốn lưu các mặt hàng đã chọn vào 1 đơn hàng tạm để xử lý sau?');
        if(!confirm) return;

        let listItem = [];
        let listRemain = [];
        forEach(this.props.cartReducer.list.shops, shop => {
            forEach(shop.items, item => {
                if(item.checked){
                    listItem.push(item);
                }else{
                    listRemain.push(item);
                }
            });
        });

        if(!listItem.length){
            const confirm = window.confirm('Bạn chưa chọn sản phẩm nào trong giỏ hàng. Bạn có muốn đưa toàn bộ sản phẩm trong giỏ hàng vào đơn hàng tạm không?');
            if(confirm){
                listItem = [...listRemain];
                listRemain = [];
            }else{
                return;
            }
        }

        const params = {
            listOrderItem: JSON.stringify(listItem),
            draft: true
        };
        Tools.apiCall(apiUrls.addFull, params).then((result) => {
            if(result.success){

                Tools.setStorage('orderItems', listRemain);
                this.props.cartAction('newList', {list: listRemain, pages: 1});

                setTimeout(()=>{
                    window.top.location = BASE_URL + 'order/normal/draft';
                }, 200);
            }
        });
    }

    handleUpload(eventData, dispatch){
        Tools.apiCall(apiUrls.uploadCart, eventData).then((result) => {
            if(result.success){
                const listNewItem = result.data.items;
                let listItem = Tools.getStorage('orderItems');

                forEach(listNewItem, function(value){
                    var matchIndex = findIndex(
                        listItem, {
                            url: value.url,
                            properties: value.properties,
                            unit_price: value.unit_price
                        }
                    );
                    if(matchIndex !== -1){
                        // Match -> Increase quantity
                        listItem[matchIndex].quantity = parseInt(listItem[matchIndex].quantity) + parseInt(value.quantity);
                    }else{
                        // Not Match -> Insert
                        listItem.push(value);
                    }
                });
                Tools.setStorage('orderItems', listItem);
                setTimeout(() => this.checkForInit(), 50);
            }
        });
    }

    render() {
        return (
            <CartLayout
                {...this.props}
                mainModal={this.state.mainModal}
                manualModal={this.state.manualModal}
                previewModal={this.state.previewModal}
                bulkRemove={this.state.bulkRemove}
                itemId={this.state.itemId}
                list={this.list}
                toggleModal={this.toggleModal}
                onCheck={this.handleCheck}
                onCheckAll={this.handleCheckAll}
                onCheckAllShop={this.handleCheckAllShop}
                onRemove={this.handleRemove}
                onChange={this.handleChange}
                onUpload={this.handleUpload}
                onFilter={this.handleFilter}
                onPageChange={this.handlePageChange}
                onAddOrder={this.handleAddOrder}
                onSaveDraft={this.handleSaveDraft}
                onManualAdd={this.hanldeManualAdd}
                />
        );
    }
}

export default Cart;
