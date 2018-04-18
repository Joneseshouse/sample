import React from 'react';
import PropTypes from 'prop-types';
import Tools from 'helpers/Tools';
import CustomModal from 'utils/components/CustomModal';

import {labels} from '../../_data';
import {ADMIN_ROLES} from 'app/constants';
import NoteForm from './forms/Note.form';


export default class ListNoteLayout extends React.Component {
	static propTypes = {
		listItem: PropTypes.array.isRequired,
		onSubmit: PropTypes.func.isRequired
	};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
	    };
	    this._renderRow = this._renderRow.bind(this);
	}

	_renderRow(){
		return this.props.listItem.map((item, index) => {
			return(
				<tr key={index}>
					<td>
						<strong>[{item.fullname}]</strong> {item.note}
					</td>
					<td className="right-align top-align">{Tools.dateFormat(item.created_at)}</td>
				</tr>
			);
		});
	}

	render() {
		return (
			<div>
				<NoteForm onSubmit={this.props.onSubmit}/>
				<table className="table">
					<tbody>
						{this._renderRow()}
					</tbody>
				</table>
			</div>
		);
	}
}
