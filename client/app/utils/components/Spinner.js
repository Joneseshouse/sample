import React from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import hloading from 'images/hloading.gif';

import * as actionCreators from 'app/actions/actionCreators';


@connect(state => ({}), dispatch => ({
	...bindActionCreators(actionCreators, dispatch)
}))
class Spinner extends React.Component {
	constructor(props) {
		super(props);
	}
	render(){
		if(this.props.spinner.show){
			return (
				<div className="spinner-container">
					<div className="spinner-sub-container">
						<img src={hloading}/>
					</div>
				</div>
			)
		}else{
			return null;
		}
	}
}

export default Spinner;
