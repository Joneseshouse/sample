import React from 'react';
import injectTapEventPlugin from 'react-tap-event-plugin';
import Link from 'react-router/lib/Link';
import Spinner from 'utils/components/Spinner';
import Tools from 'helpers/Tools';

class Main extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
	    };
		injectTapEventPlugin();
	}

	componentDidMount(){
		if(Tools.getToken()){
			const authData = Tools.getStorage('authData');
	    	this.props.updateProfile({
	    		email: authData.email,
	    		first_name: authData.first_name,
	    		last_name: authData.last_name
	    	});
		}
	}

	render() {
		return (
			<div>
				{React.cloneElement(this.props.children, {...this.props})}
				<Spinner {...this.props}/>
			</div>
		);
	}
}

export default Main;
