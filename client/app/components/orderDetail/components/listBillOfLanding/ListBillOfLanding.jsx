import React from 'react';
import PropTypes from 'prop-types';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import {ADMIN_ROLES} from 'app/constants';
import {labels} from '../../_data';
import Tools from 'helpers/Tools';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch)
}))
class ListBillOfLanding extends React.Component {
	static propTypes = {
		purchaseId: PropTypes.number.isRequired,
		listItem: PropTypes.array.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderItem = this._renderItem.bind(this);
		this._renderBillOfLandingCode = this._renderBillOfLandingCode.bind(this);
	}

	_getStatus(item){
		if(item.cn_store_date && !item.vn_store_date){
			return '[CN] ';
		}else if(item.vn_store_date && !item.export_store_date){
			return '[VN] ';
		}else if(item.export_store_date){
			return '[DG] ';
		}
		return '';
	}

	_renderBillOfLandingCode(item){
		if(Tools.isAdmin){
			return (
				<span>
					<span
						onClick={() => {
							this.props.orderDetailAction('selectedShop', item.purchase_id);
							this.props.toggleModal(item.id, 'billOfLandingModal')
						}}
						className="pointer green">
						{this._getStatus(item) + item.code}
					</span>
					&nbsp;
					<span
						onClick={() => {this.props.onRemoveCode(item.id)}}
						className="glyphicon glyphicon-remove pointer red"></span>
				</span>
			);
		}else{
			return (
				<span>
					<span
						className="pointer green">
						{this._getStatus(item) + item.code}
					</span>
				</span>
			);
		}
	}
	/*
	_renderItem(){
		return this.props.listItem.map((item, i) => {
			return(
				<div key={i}>
					{this._renderBillOfLandingCode(item)}
					<ul className="thin-padding">
						<li>
							{item.packages}	Kiện / {item.mass}	Kg
						</li>
					</ul>
				</div>
			);
		});
	}
	*/

	_renderItem(){
		return this.props.listItem.map((item, i) => {
			return(
				<p key={i}>
					{this._renderBillOfLandingCode(item)}
				</p>
			);
		});
	}

	render(){
		return (
			<div>
				{this._renderItem()}
			</div>
		);

	}
}

export default ListBillOfLanding;
