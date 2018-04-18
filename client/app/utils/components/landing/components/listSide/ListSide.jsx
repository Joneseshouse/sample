import React from 'react';
import PropTypes from 'prop-types';
import './styles.styl';


class ListSide extends React.Component {
	static propTypes = {
		title: PropTypes.string.isRequired
	};
	static defaultProps = {
	};

	constructor(props) {
		super(props);
		this.state = {
	    };
	}

	render() {
		return (
			<div>
				<br/>
				<div className="panel panel-primary">
					<div className="panel-heading">
						<h3 className="panel-title">{this.props.title}</h3>
					</div>
					<div className="panel-body" style={{padding: 0}}>

						<div className="list-group list-side">
							<a href="#" className="list-group-item">Cras justo odio</a>
							<a href="#" className="list-group-item">Dapibus ac facilisis in</a>
							<a href="#" className="list-group-item">Morbi leo risus</a>
							<a href="#" className="list-group-item">Porta ac consectetur ac</a>
							<a href="#" className="list-group-item">Vestibulum at eros</a>
						</div>

					</div>
				</div>
			</div>
		);
	}
}

export default ListSide
