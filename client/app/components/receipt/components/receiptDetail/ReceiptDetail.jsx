import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { SubmissionError, reset } from 'redux-form';
import * as actionCreators from 'app/actions/actionCreators';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';



@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch)
}))
class ReceiptDetail extends React.Component {
	static propTypes = {
	};
	static defaultProps = {
	};

	constructor(props) {
		super(props);
		this.state = {};
		this.printPage = this.printPage.bind(this);
		this._renderNote = this._renderNote.bind(this);
	}

	printPage(){
		window.print();
	}

	_renderNote(){
		return (
			<div className="printable">
				<h3 className="center-align">PHIẾU GIAO HÀNG</h3>
				<div>
					It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
				</div>
			</div>
		);
	}

	_renderContent(){
		return (
			<div>
				<div className="breadcrumb-container non-printable">
					In phiếu thu
				</div>
				<div className="main-content">
					<div className="row">
						<div className="col-xs-12">
							<div className="non-printable">
								<div className="row">
									<div className="col-md-12">
										<button
											onClick={() => this.printPage()}
											className="btn btn-success btn-block">
											<span className="glyphicon glyphicon-list-alt"></span>&nbsp;
											In phiếu giao hàng
										</button>
									</div>
								</div>
							</div>
							{this._renderNote()}
						</div>
					</div>
				</div>
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

export default ReceiptDetail;
