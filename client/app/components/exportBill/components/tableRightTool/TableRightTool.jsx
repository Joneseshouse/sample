import React from 'react';
import PropTypes from 'prop-types';


export default class TableRightTool extends React.Component {
	static propTypes = {
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
		this._renderRemove = this._renderRemove.bind(this);
		this._renderUpdate = this._renderUpdate.bind(this);
		this._renderViewDetail = this._renderViewDetail.bind(this);
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

	_renderUpdate(){
		if(this.props.allowUpdate){
			return (
				<span
					onClick={this.props.toggleModal}
		        	className="glyphicon glyphicon-pencil pointer"></span>
			);
		}
		return null;
	}

	_renderViewDetail(){
		return (
			<span>
				<span
					onClick={this.props.toDetail}
		        	className="glyphicon glyphicon-eye-open pointer"></span>
				&nbsp;&nbsp;&nbsp;
			</span>
		);
	}

	render(){
		return(
			<div className="center-align">
				{this._renderViewDetail()}
		        {this._renderUpdate()}
		        {this._renderRemove()}
			</div>
		)
	}
}
