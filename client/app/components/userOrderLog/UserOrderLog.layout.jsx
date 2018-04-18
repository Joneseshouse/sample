import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import Table from 'rc-table';
import {labels} from './_data';
import Tools from 'helpers/Tools';
import NavWrapper from 'utils/components/NavWrapper';
import CustomModal from 'utils/components/CustomModal';
import MainTable from './tables/Main.table';
import WaitingMessage from 'utils/components/WaitingMessage';


class UserOrderLogLayout extends React.Component {
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
				<MainTable {...this.props}/>
			</div>
		);
	}

	render() {
		return (
			<div>
				{this._renderContent()}
			</div>
		);
	}
}

UserOrderLogLayout.propTypes = {
	bulkRemove: PropTypes.bool
};

UserOrderLogLayout.defaultProps = {
	bulkRemove: true
};

export default UserOrderLogLayout;
