import React from 'react';
import Tools from 'helpers/Tools';


export default class UserProfile extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
        };
    } 

    render(){
        const {data} = this.props
        return (
            <div className="row">
                <div className="col-md-6">
                    <table className="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>ID / Họ tên: </strong></td>
                                <td>
                                    {data.id} / {data.full_name}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tên đăng nhập: </strong></td>
                                <td>
                                    {data.uid}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Email: </strong></td>
                                <td>{data?data.email:''}</td>
                            </tr>
                            <tr>
                                <td><strong>Phone: </strong></td>
                                <td>
                                    {data.phone}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Công ty: </strong></td>
                                <td>
                                    {data.company}
                                </td>
                            </tr>

                            <tr>
                                <td><strong>Công cụ đặt hàng: </strong></td>
                                <td>
                                    <a href={data.extension_url_shopping} target="_blank">
                                        Link
                                    </a>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div className="col-md-6">
                    <table className="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Tỷ giá: </strong></td>
                                <td>
                                    {Tools.numberFormat(data.rate)}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Phí đặt hàng: </strong></td>
                                <td>
                                    {data.order_fee_factor}%
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Hệ số cọc: </strong></td>
                                <td>
                                    {data.deposit_factor}%
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Hạn khiếu nại: </strong></td>
                                <td>
                                    {data.complain_day} ngày
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Số dư: </strong></td>
                                <td className="green strong">
                                    {Tools.numberFormat(data.balance)}₫
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Công nợ: </strong></td>
                                <td className="red strong">
                                    {Tools.numberFormat(data.debt)}₫
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        );
    }
}

