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
		this._renderDownload = this._renderDownload.bind(this);
	}

	_renderDownload(){
		if(this.props.row.status === 'draft'){
			return (
				<span>
					&nbsp;&nbsp;&nbsp;
					<span
						onClick={()=>this.props.onDraftToNew(this.props.row.id)}
						className="glyphicon glyphicon-upload pointer"></span>
				</span>
			);
		}
		return (
			<span>
				&nbsp;&nbsp;&nbsp;
				<a href={API_URL + 'order/download/' + this.props.row.id + '/' + this.props.row.uid}>
					<span className="glyphicon glyphicon-download-alt"></span>
				</a>
			</span>
		);
	}

	_renderLog(){
		return (
			<span>
				&nbsp;&nbsp;&nbsp;
				<span
					onClick={this.props.onToggleLog}
		        	className="glyphicon glyphicon-th-large pointer"></span>
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
		        {this._renderDownload()}
			    {/*
			        {this._renderLog()}
			    */}
		        {this._renderUpdate()}
		        {this._renderRemove()}
			</div>
		)
	}
}