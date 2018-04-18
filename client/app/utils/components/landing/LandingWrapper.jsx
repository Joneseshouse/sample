import React from 'react';
import Link from 'react-router/lib/Link';
import store from 'app/store';
import { intState } from 'app/store';
import {logout as logoutAction} from 'app/actions/actionCreators';

import Sidebar from 'react-sidebar';
import DropdownMenu from 'react-dd-menu';

import { APP_TITLE, URL_PREFIX, APP, STATIC_URL } from 'app/constants';
import Tools from 'helpers/Tools';
import {apiUrls} from 'components/auth/_data';
import MainSlider from './components/mainSlider/MainSlider';
import ListSide from './components/listSide/ListSide';

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
					<MainSlider/>
					<div className="row">
						<div className="col-sm-3 col-sm-offset-1 hidden-xs hidden-sm">
							<ListSide title="Danh sách sản phẩm"/>
							<ListSide title="Bài viết mới nhất"/>
						</div>
						<div className="col-sm-7">
							{ React.cloneElement(this.props.children, {...this.props}) }
						</div>
					</div>
					<div className="footer">
						<table>
							<tr>
								<td width="150px">
									<img src={STATIC_URL + 'images/sample/logo.png'} width="100%"/>
								</td>
								<td style={{paddingLeft: "10px"}}>
									<div>CÔNG TY TNHH THƯƠNG MẠI & DỊCH VỤ NHÔM KÍNH ĐĂNG KHOA</div>
									<div><strong>Địa chỉ:</strong> Số 335, Cầu Giấy, Hà Nội</div>
									<div><strong>Xưởng SX:</strong> số 59, đường Trung Tựu, quận Bắc Từ Liêm, Hà Nội</div>
									<div><strong>Điện thoại:</strong> 098.5803.489</div>
									<div><strong>Email:</strong> <a href="mailto:info@nhomkinhvn.net">info@nhomkinhvn.net</a></div>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</Sidebar>
		);
	}
}

export default LandingWrapper;
