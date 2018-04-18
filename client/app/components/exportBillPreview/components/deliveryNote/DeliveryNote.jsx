import React from 'react';
import PropTypes from 'prop-types';
import Barcode from 'react-barcode';
import Tools from 'helpers/Tools';
import logo from 'images/bill-logo.jpg';


class DeliveryNote extends React.Component {
    static propTypes = {};
    static defaultProps = {};

    constructor(props) {
        super(props);
        this.state = {
            listBillOfLanding: [],
            totalInsurance: 0,
            totalSubFee: 0,
            totalDeliveryFee: 0,
            totalPackages: 0,
            totalMass: 0,
            total: 0,
            listAddress: [],
            showDeliveryNote: true
        };
        this._renderBillOfLanding = this._renderBillOfLanding.bind(this);
        this._renderDeliveryNote = this._renderDeliveryNote.bind(this);
        this._renderAddressDetail = this._renderAddressDetail.bind(this);
        this.printPage = this.printPage.bind(this);
    }


    componentDidMount(){
        let totalInsurance = 0;
        let totalSubFee = 0;
        let totalDeliveryFee = 0;
        let totalPackages = 0;
        let totalMass = 0;
        let total = 0;
        let listAddress = [];

        const listBillOfLanding = this.props.data.list_bill_of_landing.map((item, index) => {
            if(listAddress.indexOf(item.address_code) === -1){
                listAddress.push(item.address_code);
            }
            item.insurance_fee = parseInt(item.insurance_fee);
            item.delivery_fee = parseInt(item.delivery_fee);
            item.sub_fee = parseInt(item.sub_fee);

            totalInsurance += item.insurance_fee;
            totalDeliveryFee += item.delivery_fee;
            totalSubFee += item.sub_fee;
            totalPackages += parseInt(item.packages);
            totalMass += parseFloat(item.mass);
            total += parseInt(item.delivery_fee + item.sub_fee);
            return item;
        });

        this.setState({
            listAddress,
            listBillOfLanding,
            totalInsurance,
            totalDeliveryFee,
            totalSubFee,
            totalPackages,
            totalMass,
            total
        });
    }

    _renderBillOfLanding(){
        return this.state.listBillOfLanding.map((billOfLanding, index) => {
            return(
                <tr key={index}>
                    <td>{index + 1}</td>
                    <td>{billOfLanding.code}</td>
                    <td>{billOfLanding.address_code}</td>
                    <td className={billOfLanding.packages>1?'strong':''}>
                        {billOfLanding.packages} - {billOfLanding.mass}Kg
                    </td>
                    <td>
                        {Tools.numberFormat(billOfLanding.insurance_fee)}
                    </td>
                    <td>
                        {Tools.numberFormat(billOfLanding.sub_fee)}
                    </td>
                    <td>
                        {Tools.numberFormat(billOfLanding.delivery_fee)}
                    </td>
                    <td className="non-printable">
                        {Tools.numberFormat(billOfLanding.delivery_fee + billOfLanding.sub_fee)}
                    </td>
                    <td>{billOfLanding.note}</td>
                </tr>
            );
        });
    }

    _renderDeliveryNote(){
        if(!this.state.showDeliveryNote){
            return null;
        }
        return (
            <div className="printable">
                <h3 className="center-align">PHIẾU GIAO HÀNG</h3>
                <div className="row">
                    <div className="col-xs-8">
                        <div>
                            <strong>{this.props.data.contact.company}</strong>&nbsp;
                            <span
                                onClick={()=> this.props.toggleModal(null, 'mainModal')}
                                className="glyphicon glyphicon-pencil non-printable pointer blue"></span>
                        </div>
                        <div>{this.props.data.contact.address}</div>
                        <div>{this.props.data.contact.email}</div>
                        <div>{this.props.data.contact.phone}</div>
                        <div>{this.props.data.contact.website}</div>
                        <br/>
                        <div><strong>Nhân viên lập phiếu:</strong> {this.props.data.admin_fullname}</div>
                        <div>
                            {Tools.dateFormat(this.props.data.created_at)} | <strong>{this.state.listAddress.join(', ')}</strong>
                        </div>
                    </div>
                    <div className="col-xs-4 right-align">
                        <div>
                            <img src={logo} height={70}/>
                        </div>
                        <Barcode
                            value={this.props.data.uid}
                            height={20}
                            fontSize={12}
                            />
                    </div>
                </div>
                <table className="table table-bordered delivery-note-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã bill</th>
                            <th>Địa chỉ</th>
                            <th>Số kiện</th>
                            <th>Bảo hiểm</th>
                            <th>Phụ phí</th>
                            <th>Vận Chuyển</th>
                            <th className="non-printable">Tổng</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        {this._renderBillOfLanding()}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colSpan={3}>
                                <strong>
                                    Tổng cộng
                                </strong>
                            </td>
                            <td>
                                {Tools.numberFormat(this.state.totalPackages)} - {Tools.numberFormat(this.state.totalMass)}Kg
                            </td>
                            <td>
                                {Tools.numberFormat(this.state.totalInsurance)}
                            </td>
                            <td>
                                {Tools.numberFormat(this.state.totalSubFee)}
                            </td>
                            <td>
                                {Tools.numberFormat(this.state.totalDeliveryFee)}
                            </td>
                            <td className="non-printable">
                                {Tools.numberFormat(this.state.total)}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div className="row">
                    <div className="col-xs-6">
                        Phí giao hàng
                    </div>
                    <div className="col-xs-6 right-align">
                        <strong>
                            {Tools.numberFormat(this.props.data.sub_fee)} VND
                        </strong>
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-6">
                        Tổng cộng
                    </div>
                    <div className="col-xs-6 right-align">
                        <strong>
                            {Tools.numberFormat(this.state.total + this.props.data.sub_fee)} VND
                        </strong>
                    </div>
                </div>
                <div className="row">
                    <div className="col-xs-2">
                        Ghi chú
                    </div>
                    <div className="col-xs-10">
                        <div className="dot-underline">{this.props.data.note}</div>
                        <div className="dot-underline">&nbsp;</div>
                        <div className="dot-underline">&nbsp;</div>
                    </div>
                </div>
                <div>
                    <em>Ngày........Tháng........Năm........</em>
                </div>
                <div className="row">
                    <div className="col-xs-6 center-align">
                        <strong>
                            Bên giao
                        </strong>
                    </div>
                    <div className="col-xs-6 center-align">
                        <strong>
                            Bên nhận
                        </strong>
                    </div>
                </div>
            </div>
        );
    }

    _renderAddressDetail(){
        if(this.state.showDeliveryNote){
            return null;
        }
        return (
            <div>
                <h1><strong>Người gửi</strong></h1>
                <h2>
                    Tên: {this.props.data.address_detail.from_address}, Số đt: {this.props.data.address_detail.from_phone}
                </h2>
                <h1><strong>Người nhận</strong></h1>
                <h2>
                    Tên: {this.props.data.address_detail.fullname}, Số đt: {this.props.data.address_detail.phone}
                </h2>
                <h2>
                    Vùng: {this.props.data.address_detail.area}
                </h2>
                <h2>
                    Địa chỉ: {this.props.data.address_detail.address}
                </h2>
            </div>
        );
    }

    printPage(printDeliveryNote=true){
        if(!printDeliveryNote){
            this.setState({showDeliveryNote: printDeliveryNote}, () => {
                window.print();
                this.setState({showDeliveryNote: true});
            });
        }else{
            window.print();
        }
    }

    render() {
        return (
            <div className="row">
                <div className="col-xs-12">
                    <div className="non-printable">
                        <div className="row">
                            <div className="col-md-6">
                                <button
                                    onClick={() => this.printPage()}
                                    className="btn btn-success btn-block">
                                    <span className="glyphicon glyphicon-list-alt"></span>&nbsp;
                                    In phiếu giao hàng
                                </button>
                            </div>
                            <div className="col-md-6">
                                <button
                                    onClick={() => this.printPage(false)}
                                    className="btn btn-primary btn-block">
                                    <span className="glyphicon glyphicon-user"></span>&nbsp;
                                    In địa chỉ nhận hàng
                                </button>
                            </div>
                        </div>
                    </div>
                    {this._renderAddressDetail()}
                    {this._renderDeliveryNote()}
                </div>
            </div>
        );
    }
}

export default DeliveryNote;
