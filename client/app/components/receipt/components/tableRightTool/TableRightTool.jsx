import React from 'react';
import PropTypes from 'prop-types';
import Link from 'react-router/lib/Link';
import Tools from 'helpers/Tools';


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
		this._renderDetail = this._renderDetail.bind(this);
		this._renderUpdate = this._renderUpdate.bind(this);
		this._renderRemove = this._renderRemove.bind(this);
	}

	_renderDetail(){
		return (
			<Link to={Tools.toUrl('receipt', [this.props.row.id])}>
				<span
		        	className="glyphicon glyphicon-eye-open"></span>
	        </Link>
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
		        {this._renderDetail()}
		        {this._renderUpdate()}
		        {this._renderRemove()}
			</div>
		)
	}
}
