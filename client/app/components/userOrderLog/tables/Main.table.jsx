import React from 'react';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import isEqual from 'lodash/isEqual';
import Table from 'rc-table';
import {labels} from '../_data';
import Tools from 'helpers/Tools';
import Paginator from 'utils/components/Paginator';
import {
	TableAddButton,
	TableFilter,
	TableCheckAll,
	TableRightTool,
	TableCheckBox
} from 'utils/components/table/TableComponents';

class MainTable extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
		};
	}


	render() {
		return (
			<div>
				{this.props.userOrderLogReducer.list.map((item, index) => {
					return (
						<div key={index} style={{color: item.admin_id?'black':'green'}}>
							<strong>[{item.created_at}]</strong>
							&rarr;
							<strong className="red">
								{item.admin_id?item.admin_full_name:item.user_full_name}
							</strong>
							&rarr;
							<em>{item.uid}</em>
							<div dangerouslySetInnerHTML={{__html: item.content}}/>
						</div>
					)
				})}
			</div>
		);
	}
}

MainTable.propTypes = {
};

MainTable.defaultProps = {
};

export default MainTable;
