import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import Table from 'rc-table';
import {labels} from './_data';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import MainTable from './tables/Main.table';
import WaitingMessage from 'utils/components/WaitingMessage';


class GrabbingLayout extends React.Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	_renderContent(){
		if(!this.props.dataLoaded){
			return <WaitingMessage/>
		}
		return (
			<div>
				<div className="breadcrumb-container">
					{labels.common.title}
				</div>
				<div className="main-content">
					<MainTable {...this.props}/>
				</div>
			</div>
		);
	}

	render() {
		return (
			<NavWrapper data-location={this.props.location} data-user={this.props.authReducer}>
				<div>
					{this._renderContent()}
				</div>
			</NavWrapper>
		);
	}
}

GrabbingLayout.propTypes = {
	bulkRemove: PropTypes.bool
};

GrabbingLayout.defaultProps = {
	bulkRemove: true
};

export default GrabbingLayout;
