import React from 'react';
import PropTypes from 'prop-types';
import { SubmissionError, reset } from 'redux-form';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import * as actionCreators from 'app/actions/actionCreators';
import store from 'app/store';
import {apiUrls, labels} from '../../_data';
import Tools from 'helpers/Tools';
import ListNoteLayout from './ListNote.layout';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch),
	resetForm: (formName) => {
		dispatch(reset(formName));
	}
}))
class ListNote extends React.Component {
	static propTypes = {};
	static defaultProps = {};

	constructor(props) {
		super(props);
		this.state = {
	    };
	    this.handleChange = this.handleChange.bind(this);
	}

	handleChange(eventData, dispatch){
		try{
			const params = {
				note: eventData.note,
				order_item_id: this.props.orderDetailReducer.orderItemId
			};
			return Tools.apiCall(apiUrls.orderItemNoteAdd, params).then((result) => {
		    	if(result.success){
					this.props.orderDetailAction('listNote', {
						list: result.data.items,
						orderItemId: this.props.orderDetailReducer.orderItemId
					});
		    		dispatch(reset('NoteForm'));
		    		this.toggleModal(null, 'noteModal', false);
		    	}else{
					throw new SubmissionError(Tools.errorMessageProcessing(result.message));
		    	}
		    });
	    }catch(error){
			throw new SubmissionError(Tools.errorMessageProcessing(error));
		}
	}

	render() {
		return (
			<ListNoteLayout
				{...this.props}
				onSubmit={this.handleChange}
				listItem={this.props.orderDetailReducer.listNote}
				/>
		);
	}
}

export default ListNote;
