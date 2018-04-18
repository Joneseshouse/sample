import React from 'react';
import PropTypes from 'prop-types';
import Link from 'react-router/lib/Link';
import Tools from 'helpers/Tools';
import { ADMIN_ROLES } from 'app/constants';


class StatusFilter extends React.Component {
	static propTypes = {
		type: PropTypes.string.isRequired,
		listStatus: PropTypes.array.isRequired
	};
	static defaultProps = {
		listStatus: []
	};

	constructor(props) {
		super(props);
		this.state = {
	    };
	    this._renderListStatus = this._renderListStatus.bind(this);
	    this._renderDraftMenu = this._renderDraftMenu.bind(this);
	}

	_renderListStatus(){
		return this.props.listStatus.map((status, index) => {
			return(
				<Link
					activeClassName="active"
					to={Tools.toUrl('orderStatistics', [this.props.type, status.id])}
					className="btn btn-default" key={index}>
					{status.title} &rarr; {status.total}
				</Link>
			);
		});
	}

	_renderDraftMenu(){
		if(Tools.isAdmin){
			return null;
		}
		return (
			<Link
				activeClassName="active"
				to={Tools.toUrl('orderStatistics', [this.props.type, 'draft'])}
				className="btn btn-default">Tạm</Link>
		);
	}

	render() {
		return (
			<div className="btn-group btn-group-justified" role="group">
				<Link
					activeClassName="active"
					to={Tools.toUrl('orderStatistics', [this.props.type, 'all'])}
					className="btn btn-default">Tất cả</Link>
				{this._renderDraftMenu()}
				{this._renderListStatus()}
			</div>
		);
	}
}

export default StatusFilter;
