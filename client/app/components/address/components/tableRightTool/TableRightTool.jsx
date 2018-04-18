import React from 'react';
import PropTypes from 'prop-types';
import Tools from 'helpers/Tools';
import { ADMIN_ROLES } from 'app/constants';


export default class TableRightTool extends React.Component {
	static propTypes = {
		toggleModal: PropTypes.func.isRequired,
		onPrint: PropTypes.func.isRequired,
		allowRemove: PropTypes.bool,
		allowUpdate: PropTypes.bool
	};
	static defaultProps = {
		allowRemove: true,
		allowUpdate: true
	};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderPrint = this._renderPrint.bind(this);
		this._renderUpdate = this._renderUpdate.bind(this);
	}

	_renderPrint(){
		if(!Tools.isAdmin){
			return null;
		}
		return (
	        <span
	        	onClick={this.props.onPrint}
	        	className="glyphicon glyphicon-print pointer"></span>
		);
	}

	_renderUpdate(){
		if(Tools.isAdmin){
			return null;
		}
		return (
			<span
				onClick={this.props.toggleModal}
	        	className="glyphicon glyphicon-pencil pointer"></span>
		);
	}

	render(){
		return(
			<div className="center-align">
		        {this._renderUpdate()}
		        {this._renderPrint()}
			</div>
		)
	}
}
