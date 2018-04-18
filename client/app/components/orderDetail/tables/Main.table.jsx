import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import {labels} from '../_data';
import Tools from 'helpers/Tools';
import PurchaseRow from '../components/purchaseRow/PurchaseRow';


class MainTable extends React.Component {
    static propTypes = {};
    static defaultProps = {};

    constructor(props) {
        super(props);
        this.state = {};
        this._renderShop = this._renderShop.bind(this);
    }

    _renderShop(){
        if(this.props.listItem.length){
            return this.props.listItem.map((row, i) =>{
                return (
                    <PurchaseRow
                        {...this.props}
                        key={i}
                        index={i}
                        purchase={row}
                        rate={this.props.rate}
                        order_fee_factor={this.props.order_fee_factor}/>
                );
            });
        }
    }

    render(){
        let status = this.props.orderDetailReducer.obj.status;
        let listStatus = this.props.orderDetailReducer.listStatus;

        if(Tools.isExistInArray(listStatus, status, 1) && !Tools.isAdmin){
            return (
                <table className="table table-bordered">
                    {this._renderShop()}
                </table>
            );
        }
        return (
            <table className="table table-bordered">
                <thead>
                    <tr>
                        <th colSpan={6}>
                            <button
                                type="button"
                                className="btn btn-warning btn-xs"
                                onClick={() => this.props.onRemoveSelectedItems()}>
                                <span className="glyphicon glyphicon-remove"></span>&nbsp;
                                Bỏ sản phẩm được chọn
                            </button>
                        </th>
                        <th className="right-align">
                            <button
                                type="button"
                                className="btn btn-danger btn-xs"
                                onClick={() => this.props.onRemoveEmptyOrderItems()}>
                                <span className="glyphicon glyphicon-remove"></span>&nbsp;
                                Bỏ sản phẩm rỗng
                            </button>
                        </th>
                        <th>
                            <button
                                type="button"
                                className="btn btn-success btn-xs btn-block"
                                onClick={() => {this.props.toggleModal(null, 'mainModal')}}>
                                <span className="glyphicon glyphicon-plus"></span>&nbsp;
                                S.phẩm
                            </button>
                        </th>



                        {/*
                        <th colSpan={2}>Thông tin purchase</th>
                        <th colSpan={2}>
                            <button
                                type="button"
                                className="btn btn-success btn-xs btn-block"
                                onClick={() => {this.props.toggleModal(null, 'mainModal')}}>
                                <span className="glyphicon glyphicon-plus"></span>&nbsp;
                                S.phẩm
                            </button>
                        </th>
                        */}
                    </tr>
                </thead>
                {this._renderShop()}
            </table>
        );
    }
}

export default MainTable;
