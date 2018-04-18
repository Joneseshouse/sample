import React from 'react';
import PropTypes from 'prop-types';
import Barcode from 'react-barcode';
import Tools from 'helpers/Tools';
import logo from 'images/bill-logo.jpg';


class MainContent extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
	    };
	    this._renderMainContent = this._renderMainContent.bind(this);
	    this.printPage = this.printPage.bind(this);
	}


	componentDidMount(){
	}

	_renderMainContent(){
		return (
			<div className="printable">
				<div className="row">
					<div className="col-xs-6">
						<div><strong>{this.props.data.contact.company}</strong></div>
						<div>{this.props.data.contact.address}</div>
						<div>{this.props.data.contact.email}</div>
						<div>{this.props.data.contact.phone}</div>
						<div>{this.props.data.contact.website}</div>
					</div>
					<div className="col-xs-6 right-align">
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


				<h3 className="center-align">PHIẾU THU TIỀN</h3>
				<div className="row">
					<div className="col-sm-12 center-align">
						{Tools.dateFormat(this.props.data.created_at)}
					</div>
				</div>
				<div>
					<span>Họ tên người nhận: {this.props.data.user_fullname}</span>
				</div>
				<div>
					<span>Địa chỉ: {this.props.data.user_address}</span>
				</div>
				<div className="row">
					<div className="col-xs-12">
						<span>
							Số tiền:
						</span>&nbsp;
						<strong>
							{Tools.numberFormat(this.props.data.amount)} VND
						</strong>
					</div>
				</div>
				<div className="row">
					<div className="col-xs-12">
						Lý do nộp: {this.props.data.note}
					</div>
				</div>
				{/*
					<div>
						<em>Ngày........Tháng........Năm........</em>
					</div>
				*/}
				<br/>
				<div className="row">
					<div className="col-xs-6 center-align">
						<div>
							<strong>
								Người nộp
							</strong>
						</div>
						<div><em>(Ký, họ tên)</em></div>
					</div>
					<div className="col-xs-6 center-align">
						<div>
							<strong>
								Người nhận
							</strong>
						</div>
						<div><em>(Ký, họ tên)</em></div>
					</div>
				</div>
			</div>
		);
	}

	printPage(){
		window.print();
	}

	render() {
		return (
			<div className="row">
				<div className="col-xs-12">
					<div className="non-printable">
						<div className="row">
							<div className="col-md-6">
								<button
									onClick={() => Tools.goToUrl('receipt')}
									className="btn btn-primary btn-block">
									<span className="glyphicon glyphicon-chevron-left"></span>&nbsp;
									Quay lại danh sách
								</button>
							</div>
							<div className="col-md-6">
								<button
									onClick={() => this.printPage()}
									className="btn btn-success btn-block">
									<span className="glyphicon glyphicon-list-alt"></span>&nbsp;
									In phiếu thu
								</button>
							</div>
						</div>
					</div>
					{this._renderMainContent()}
				</div>
			</div>
		);
	}
}

export default MainContent;
