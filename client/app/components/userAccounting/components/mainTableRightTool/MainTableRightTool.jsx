import React from 'react';
import PropTypes from 'prop-types';
import {API_URL} from 'app/constants';


export default class MainTableRightTool extends React.Component {
	static propTypes = {
		toggleModal: PropTypes.func.isRequired,
		onRemove: PropTypes.func.isRequired
	};
	static defaultProps = {
	};

	constructor(props) {
		super(props);
		this.state = {};
		this._renderRemove = this._renderRemove.bind(this);
		this._renderUpdate = this._renderUpdate.bind(this);
		this._renderPrint = this._renderPrint.bind(this);
	}

	_renderPrint(){
		if(!this.props.row.receipt_id){
			return null;
		}
		return (
			<span>
				&nbsp;&nbsp;&nbsp;
				<span
					onClick={this.props.togglePrintModal}
		        	className="glyphicon glyphicon-print pointer"></span>
			</span>
		);
	}

	_renderUpdate(){
		return (
			<span>
				&nbsp;&nbsp;&nbsp;
				<span
					onClick={this.props.toggleModal}
		        	className="glyphicon glyphicon-pencil pointer"></span>
	        </span>
		);
	}

	_renderRemove(){
		return (
			<span>
				&nbsp;&nbsp;&nbsp;
		        <span
		        	onClick={this.props.onRemove}
		        	className="glyphicon glyphicon-remove pointer"></span>
	        </span>
		);
	}

	render(){
		return(
			<div className="right-align">
			    {this._renderPrint()}
		        {this._renderUpdate()}
		        {this._renderRemove()}
			</div>
		)
	}
}