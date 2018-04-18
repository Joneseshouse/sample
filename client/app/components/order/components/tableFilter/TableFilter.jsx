import React from 'react';
import PropTypes from 'prop-types';


export default class TableFilter extends React.Component {
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
					disabled={true}
					type="text"
					className="form-control"
					value={this.state.keyword}
					onChange={this.onFilter}
					placeholder="Enter keywords for searching..."/>
				<span className="input-group-addon bulk-remove">
				<span
					className="glyphicon glyphicon-thumbs-up green pointer"
					onClick={() => this.props.onConfirmOrder()}></span>
				</span>
			</div>
		)
	}
}