import React from 'react';
import Link from 'react-router/lib/Link';
import store from 'app/store';
import { intState } from 'app/store';
import {logout as logoutAction} from 'app/actions/actionCreators';

import Sidebar from 'react-sidebar';
import DropdownMenu from 'react-dd-menu';

import { APP_TITLE, URL_PREFIX, APP } from 'app/constants';
import Tools from 'helpers/Tools';
import {apiUrls} from 'components/auth/_data';

class LandingWrapper extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			hideHeading: true,
			showHambuger: false,
			open: false,
			sidebarOpen: false,
			sidebarDocked: false,
			isMenuOpen: false
	    };
	    this.onSetSidebarOpen = this.onSetSidebarOpen.bind(this);
	    this.mediaQueryChanged = this.mediaQueryChanged.bind(this);
	}

	componentDidMount(){
		const mql = window.matchMedia(`(min-width: 768px)`);
	    mql.addListener(this.mediaQueryChanged);

	    this.setState({mql: mql});
	    this.setState({hideHeading: mql.matches});
	    this.setState({showHambuger: !mql.matches});
	}

	componentWillUnmount() {
		this.state.mql.removeListener(this.mediaQueryChanged);
	}

	mediaQueryChanged() {
	    this.setState({hideHeading: this.state.mql.matches});
	    this.setState({showHambuger: !this.state.mql.matches});
	}

	onSetSidebarOpen(open) {
		this.setState({sidebarOpen: open});
	}

	_renderMenu(){
		return (
			<ul className="list-group">
				<Link to={URL_PREFIX} className="list-group-item" activeClassName="active" onlyActiveOnIndex>
					<span className="glyphicon glyphicon-home"/> &nbsp;
					Profile
				</Link>
			</ul>
		)
	}

	_renderToggleMenu(){
		if(this.state.hideHeading){
			return null;
		}
		if(this.state.showHambuger){
			return (
				<div className="heading non-printable">
					<span
						onClick={() => this.onSetSidebarOpen(true)}
						style={{fontSize: '17px'}}
						className="hambuger glyphicon glyphicon-menu-hamburger"></span>
					<span className="heading-logo">
						{APP_TITLE}
					</span>
				</div>
			);
		}
		return (
			<div className="heading non-printable">
				<span className="heading-logo">
					{APP_TITLE}
				</span>
			</div>
		);
	}

	render(){
		let sidebarContent = (
			<div className="sidebar-content non-printable">
				{this._renderMenu()}
			</div>
		);
		var styles = {
			sidebar: {
				backgroundColor: 'white',
				zIndex: 4,
				transition: 'none',
    			WebkitTransition: 'none'
			},
			content:{
				transition: 'none',
    			WebkitTransition: 'none'
			},
			overlay: {
				zIndex: 3
			}
		};
		return (
			<Sidebar sidebar={sidebarContent}
				open={this.state.sidebarOpen}
				docked={this.state.sidebarDocked}
				onSetOpen={this.onSetSidebarOpen}
				className="main-sidebar"
				styles={styles}>
				<div className="content-wrapper">
					{ this._renderToggleMenu() }
					{ React.cloneElement(this.props.children, {...this.props}) }
				</div>
			</Sidebar>
		);
	}
}

export default LandingWrapper;
