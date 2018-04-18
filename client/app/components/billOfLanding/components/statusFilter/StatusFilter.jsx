import React from 'react';
import PropTypes from 'prop-types';
import Link from 'react-router/lib/Link';
import Tools from 'helpers/Tools';


class StatusFilter extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
	    };
	}

	render() {
		return (
			<div>
				<div className="btn-group btn-group-justified" role="group">
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [2, this.props.params.type])}
						className="btn btn-default">Tất cả</Link>
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [0, this.props.params.type])}
						className="btn btn-default">Vận đơn thường</Link>
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [1, this.props.params.type])}
						className="btn btn-default">Vận đơn bảo hiểm</Link>
				</div>
				<div className="btn-group btn-group-justified" role="group">
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [this.props.params.insurance_register, 'all'])}
						className="btn btn-default">Tất cả</Link>
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [this.props.params.insurance_register, 'order'])}
						className="btn btn-default">Order</Link>
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [this.props.params.insurance_register, 'deposit'])}
						className="btn btn-default">Vận chuyển</Link>
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [this.props.params.insurance_register, 'missing'])}
						className="btn btn-default">Thiếu thông tin</Link>
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [this.props.params.insurance_register, 'checked'])}
						className="btn btn-default">Đã kiểm</Link>
					<Link
						activeClassName="active"
						to={Tools.toUrl('bill_of_landing', [this.props.params.insurance_register, 'unchecked'])}
						className="btn btn-default">Chưa kiểm</Link>
				</div>
			</div>
		);
	}
}

export default StatusFilter;
