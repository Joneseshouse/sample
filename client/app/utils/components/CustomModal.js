import React from 'react';
import PropTypes from 'prop-types';
import Modal from 'react-modal';

class CustomModal extends React.Component {
	static propTypes = {
		size: PropTypes.string
	};
	static defaultProps = {
		size: 'full'
	};

	constructor(props) {
		super(props);
	}

	render(){
		let customStyles = {
			overlay: {
				zIndex: 2,
				overflowY: 'scroll'
			},
			content : {
				top: '5%',
				left: '30%',
				right: '30%',
				bottom: 'auto',
				overflowX: 'visible',
				overflowY: 'visible'
			}
		};
		switch(this.props.size){
			case 'sm':
				customStyles.content.left = '30%';
				customStyles.content.right = '30%';
			break;
			case 'md':
				customStyles.content.left = '20%';
				customStyles.content.right = '20%';
			break;
			case 'lg':
				customStyles.content.left = '3%';
				customStyles.content.right = '3%';
			break;
			case 'full':
				customStyles.content.left = '0%';
				customStyles.content.right = '0%';
				customStyles.content.top = '0%';
				customStyles.content.bottom = '0%';
				customStyles.content.borderWidth = 0;
			break;
			default:
				customStyles.content.left = '20%';
				customStyles.content.right = '20%';
		}

		const closeButtonStyle = {
			position: 'absolute',
			top: 5,
			right: 5,
			cursor: 'pointer'
		};
		const headingStyle = {
			margin: 0,
			marginBottom: 10
		};
		return(
			<Modal
				style={customStyles}
				isOpen={this.props.open}
				contentLabel="Modal">
				<span
					style={closeButtonStyle}
					className="glyphicon glyphicon-remove non-printable"
					onClick={() => this.props.close()}>
				</span>
				<h4 className="non-printable" style={headingStyle}>{ this.props.title }</h4>
				{ this.props.children }
			</Modal>
		);
	}
}

export default CustomModal;
