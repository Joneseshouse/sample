import React from 'react';

class WaitingMessage extends React.Component {
	constructor(props) {
		super(props);
	}
	render(){
		const style={
			textAlign: 'center',
			color: 'gray'
		};
		return (
			<h1 style={style}>
				Tải Dữ Liệu...
			</h1>
		)
	}
}


export default WaitingMessage;
