import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import {labels} from '../_data';
import Tools from 'helpers/Tools';
import MainForm from '../forms/Main.form';
import WaitingMessage from 'utils/components/WaitingMessage';
import Message from './Message';

class PanelChat extends React.Component {
	constructor(props) {
		super(props);
		this.state = {};
	}

	render() {
		const Messages = this.props.chatLostReducer.list.map( data => <Message key={data.id} datas={data}/>)
		return (
		    <div className="panel-chat">
		    	<div className="panel-body body-panel">
	                <ul className="chat">
						{Messages}
					</ul>
				</div>
                <MainForm 
					onSubmit={this.props.onChange}
					labels={labels.mainForm}
					submitTitle="Sent"
                />
		    </div>
		);
	}
}
export default PanelChat;

