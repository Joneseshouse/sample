import React from 'react';
import PropTypes from 'prop-types';
import {API_URL} from 'app/constants';


export default class TableRightTool extends React.Component {
	static propTypes = {
		row: PropTypes.object.isRequired,
		toggleModal: PropTypes.func.isRequired,
		onRemove: PropTypes.func.isRequired,
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
		this._renderComplain = this._renderComplain.bind(this);
		this._renderUpdate = this._renderUpdate.bind(this);
		this._renderRemove = this._renderRemove.bind(this);
	}

	_renderComplain(){
		const resolve = this.props.row.complain_resolve;
		const changeDate = this.props.row.complain_change_date;
		let colorClass = '';
		if(!this.props.row.order_id){
			return null;
		}
		if(changeDate){
			if(resolve){
				colorClass = ' green';
			}else{
				colorClass = ' red';
			}
		}
		return (
			<span
				onClick={this.props.toggleComplainModal}
	        	className={"glyphicon glyphicon-comment" + colorClass}></span>
		);
	}

	_renderUpdate(){
		if(this.props.allowUpdate){
			return (
				<span>
					&nbsp;&nbsp;&nbsp;
					<span
						onClick={this.props.toggleModal}
			        	className="glyphicon glyphicon-pencil pointer"></span>
		        </span>
			);
		}
		return null;
	}

	_renderRemove(){
		if(this.props.allowRemove){
			return (
				<span>
					&nbsp;&nbsp;&nbsp;
			        <span
			        	onClick={this.props.onRemove}
			        	className="glyphicon glyphicon-remove pointer"></span>
		        </span>
			);
		}
		return null;
	}

	render(){
		return(
			<div className="center-align">
		        {this._renderComplain()}
		        {this._renderUpdate()}
		        {this._renderRemove()}
			</div>
		)
	}
}
