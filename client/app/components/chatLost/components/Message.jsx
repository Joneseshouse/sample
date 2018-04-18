import React from 'react';

class Message extends React.Component {
    constructor(props){
        super(props);
    }
  
    _renderAdmin(){
        return(
            <div>
                <strong className="primary-font text-admin">
                        {this.props.datas.admin_full_name+'(Admin)'}
                </strong> <small className="pull-right text-muted">
                <span className="glyphicon glyphicon-time"></span>{this.props.datas.updated_at}</small>
            </div>
        );
    }

    _renderUser(){
        return(
            <div>
                <strong className="primary-font text-user">
                        {this.props.datas.user_full_name}
                </strong> <small className="pull-right text-muted">
                <span className="glyphicon glyphicon-time"></span>{this.props.datas.updated_at}</small>
            </div>
        );
    }

	render() {
		return (
            <li className="left clearfix message">
                <div className="chat-body clearfix">
                    <div className="header">
                        { this.props.datas.admin_full_name ? this._renderAdmin() : this._renderUser() }
                    </div>
                    <p>
                      {this.props.datas.message}
                    </p>
                </div>
            </li>
		);
	}
}
export default Message;