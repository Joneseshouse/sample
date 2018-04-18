import React from 'react';
import PropTypes from 'prop-types';


export class TableAddButton extends React.Component {
	static propTypes = {
		title: PropTypes.string,
		onExecute: PropTypes.func.isRequired
	};
	static defaultProps = {
		title: 'Add'
	};

	constructor(props) {
		super(props);
		this.state = {};
	}

	render(){
		return (
			<button
				onClick={this.props.onExecute}
				type="button"
				className="btn btn-success btn-block btn-xs">
		        <span className="glyphicon glyphicon-plus"></span>&nbsp;
				{this.props.title}
			</button>
		)
	}
}

export class TableFilter extends React.Component {
	static propTypes = {
		onFilter: PropTypes.func.isRequired,
		onRemove: PropTypes.func.isRequired,
		bulkRemove: PropTypes.bool.isRequired
	};
	static defaultProps = {
		bulkRemove: true
	};

	constructor(props) {
		super(props);
		this.state = {
			keyword: ''
		};
	    this.onFilter = this.onFilter.bind(this);
		this._renderBulkRemoveButton = this._renderBulkRemoveButton.bind(this);
	}

	onFilter(event){
		this.setState({keyword: event.target.value});
		this.props.onFilter(event);
	}

	_renderBulkRemoveButton(){
		if(this.props.bulkRemove){
			return(
				<span
					className="glyphicon glyphicon-remove"
					onClick={() => this.props.onRemove()}></span>
			);
		}
		return null;
	}

	render(){
		return (
			<div className="input-group">
				<span className="input-group-addon bulk-remove">
					{this._renderBulkRemoveButton()}
				</span>
				<input
					type="text"
					className="form-control"
					value={this.state.keyword}
					onChange={this.onFilter}
					placeholder="Enter keywords for searching..."/>
			</div>
		)
	}
}


export class TableCheckAll extends React.Component {
	static propTypes = {
		bulkRemove: PropTypes.bool,
		onCheckAll: PropTypes.func.isRequired
	};
	static defaultProps = {
		bulkRemove: true
	};

	constructor(props) {
		super(props);
		this.state = {};
	}

	render(){
		if(!this.props.bulkRemove){
			return null;
		}
		return (
			<span>
				<span
					className="glyphicon glyphicon-ok"
					onClick={this.props.onCheckAll}></span>
			</span>
		);
	}
}

export class TableRightTool extends React.Component {
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

	render(){
		return(
			<div className="center-align">
		        {this._renderUpdate()}
		        {this._renderRemove()}
			</div>
		)
	}
}


export class TableCheckBox extends React.Component {
	static propTypes = {
		checked: PropTypes.bool,
		bulkRemove: PropTypes.bool,
		onCheck: PropTypes.func.isRequired
	};
	static defaultProps = {
		checked: false,
		bulkRemove: true
	};

	constructor(props) {
		super(props);
		this.state = {};
	}

	render(){
		if(!this.props.bulkRemove){
			return (
				null
			);
		}
		return (
	        <input type="checkbox"
				checked={this.props.checked}
				onChange={this.props.onCheck}/>
		);
	}
}
