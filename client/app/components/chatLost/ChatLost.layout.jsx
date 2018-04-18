import React from 'react';
import PropTypes from 'prop-types';
import values from 'lodash/values';
import forEach from 'lodash/forEach';
import {labels} from './_data';
import Tools from 'helpers/Tools';
import MainForm from './forms/Main.form';
import WaitingMessage from 'utils/components/WaitingMessage';
import Message from './components/Message';
import PanelChat from './components/PanelChat';

class ChatLostLayout extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
		};
	}

	render() {
		// const Messages = this.props.chatLostReducer.list.map( data => <Message key={data.id} datas={data}/>)
		return (
		    
		    <div className="row form-group">
		        <div className="col-xs-12 col-md-12 message-box">
		            <div className="panel panel-primary">
		                <div className="panel-heading" onClick={this.props.onPanel}>
		                    <span className="glyphicon glyphicon-comment"></span>
		                    <span 
		                    	className={this.props.showPanel?'glyphicon glyphicon-chevron-up chevron-icon' : 'glyphicon glyphicon-chevron-down chevron-icon'} 
		                    >
		                    </span>
		                </div>   
						 {this.props.showPanel ? < PanelChat {...this.props}  / > : null}
		            </div>
		        </div>
		    </div>
		);
	}
}

export default ChatLostLayout;

