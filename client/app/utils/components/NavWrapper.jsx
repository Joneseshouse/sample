import React from 'react';
import Link from 'react-router/lib/Link';
import store from 'app/store';
import { intState } from 'app/store';
import {logout as logoutAction} from 'app/actions/actionCreators';

import Sidebar from 'react-sidebar';
/*
import { Accordion, AccordionItem } from 'react-sanfona';
*/
import DropdownMenu from 'react-dd-menu';

import { APP_TITLE, URL_PREFIX, APP } from 'app/constants';
import Tools from 'helpers/Tools';
import {apiUrls} from 'components/auth/_data';

class NavWrapper extends React.Component {
    constructor(props) {
        super(props);

        const authData = Tools.getStorage('authData');
        this.state = {
            showHambuger: false,
            open: false,
            sidebarOpen: false,
            sidebarDocked: false,
            isMenuOpen: false,
            allowMenus: authData?(authData.allow_menus?authData.allow_menus:[]):[]
        };
        this.closeDropdown = this.closeDropdown.bind(this);
        this.onSetSidebarOpen = this.onSetSidebarOpen.bind(this);
        this.mediaQueryChanged = this.mediaQueryChanged.bind(this);
        this.toggleDropdown = this.toggleDropdown.bind(this);
        this.allow = this.allow.bind(this);
    }

    componentDidMount(){
        const mql = window.matchMedia(`(min-width: 800px)`);
        mql.addListener(this.mediaQueryChanged);

        this.setState({mql: mql});
        this.setState({sidebarDocked: mql.matches});
        this.setState({sidebarOpen: mql.matches});
        this.setState({showHambuger: !mql.matches});
    }

    componentWillUnmount() {
        this.state.mql.removeListener(this.mediaQueryChanged);
    }

    mediaQueryChanged() {
        this.setState({sidebarDocked: this.state.mql.matches});
        this.setState({sidebarOpen: this.state.mql.matches});
        this.setState({showHambuger: !this.state.mql.matches});
    }

    onSetSidebarOpen(open) {
        this.setState({sidebarOpen: open});
    }

    toggleDropdown(){
        this.setState({ isMenuOpen: !this.state.isMenuOpen });
    }

    closeDropdown(){
        this.setState({ isMenuOpen: false });
    }

    getFullName(){
        let fullName = '';
        if(this.props['data-user'].profile){
            fullName = `
                ${this.props['data-user'].profile.first_name || ''} 
                ${this.props['data-user'].profile.last_name || ''}
            `;
        }
        /*
        if(this.state.showHambuger){
            return null;
        }
        */
        return fullName;
    }

    handleLogout(){
        Tools.apiCall(apiUrls.logout, {}, false).then((result) => {
            Tools.removeStorage('authData');
            Tools.setStorage('orderItems', []);
            Tools.goToUrl('login');
            setTimeout(()=>{
                location.reload();
            }, 100);
        });
    }

    allow (index) {
        return (this.state.allowMenus.indexOf(index) !== -1) ? '' : ' hide';
    }

    _renderMenu(){
        if(['admin', 'radmin'].indexOf(APP) !== -1){
            return (
                <ul className="list-group">
                    <Link 
                        to={URL_PREFIX} 
                        className="list-group-item" activeClassName="active" onlyActiveOnIndex>
                        <span className="glyphicon glyphicon-home"/> &nbsp;
                        T.T cá nhân
                    </Link>
                    <Link
                        to={Tools.toUrl('admin')}
                        className={
                            "list-group-item" + this.allow('admin') 
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-user"/> &nbsp;
                        Quản trị viên
                    </Link>
                    <Link
                        to={Tools.toUrl('user/0')}
                        className={
                            "list-group-item" + this.allow('user')
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-briefcase"/> &nbsp;
                        Khách hàng
                    </Link>
                    <Link
                        to={Tools.toUrl('user_transaction')}
                        className={
                            "list-group-item" + this.allow('user-transaction')
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-piggy-bank"/> &nbsp;
                        Kế toán K.Hàng
                    </Link>
                    <Link
                        to={Tools.toUrl('admin_transaction')}
                        className={
                            "list-group-item" + this.allow('admin-transaction')
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-piggy-bank"/> &nbsp;
                        Kế toán nội bộ
                    </Link>
                    <Link
                        to={Tools.toUrl('receipt')}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix('/admin/receipt/', this.props['data-location'].pathname)?
                                " active":""
                            ) + this.allow('receipt')
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-duplicate"/> &nbsp;
                        Phiếu thu
                    </Link>
                    <Link
                        to={Tools.toUrl('area_code')}
                        className={
                            "list-group-item" + this.allow('area-code')
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-font"/> &nbsp;
                        Mã vùng
                    </Link>
                    <Link
                        to={Tools.toUrl('address')}
                        className={
                            "list-group-item" + this.allow('address')
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-road"/> &nbsp;
                        Địa chỉ nhận hàng
                    </Link>
                    <Link
                        to={Tools.toUrl('contact')}
                        className={"list-group-item" + this.allow('contact')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-star"/> &nbsp;
                        Thông tin cty
                    </Link>
                    <Link
                        to={Tools.toUrl('rate_log')}
                        className={"list-group-item" + this.allow('rate-log')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-usd"/> &nbsp;
                        Tỷ giá
                    </Link>
                    <Link
                        to={Tools.toUrl('order', ['normal', 'all'])}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix('/admin/order/normal', this.props['data-location'].pathname)?
                                " active":""
                            ) + this.allow('order')
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-shopping-cart"/> &nbsp;
                        Đơn order
                    </Link>
                    <Link
                        to={Tools.toUrl('order_statistics')}
                        className={"list-group-item" + this.allow('order')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-shopping-cart"/> &nbsp;
                        Thống kê đơn order
                    </Link>
                    <Link
                        to={Tools.toUrl('bill_of_landing', [2, 'all'])}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix(
                                    '/admin/bill_of_landing/', 
                                    this.props['data-location'].pathname
                                )?
                                " active":""
                            ) + this.allow('bill-of-landing')
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-tag"/> &nbsp;
                        Vận đơn
                    </Link>
                    <Link
                        to={Tools.toUrl('bol_check')}
                        className={"list-group-item" + this.allow('purchase/check')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-tag"/> &nbsp;
                        K.Tra v.đơn
                    </Link>
                    <Link
                        to={Tools.toUrl('collect_bol')}
                        className={"list-group-item" + this.allow('collect-bol')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-pencil"/> &nbsp;
                        Lấy V.Đơn
                    </Link>
                    <Link
                        to={Tools.toUrl('cn_bill_of_landing_fail')}
                        className={"list-group-item" + this.allow('cn-bill-of-landing-fail')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-exclamation-sign"/> &nbsp;
                        V.Đơn TQ lỗi
                    </Link>
                    <Link
                        to={Tools.toUrl('cn_bill_of_landing')}
                        className={"list-group-item" + this.allow('cn-bill-of-landing')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-upload"/> &nbsp;
                        Vận đơn TQ
                    </Link>
                    <Link
                        to={Tools.toUrl('vn_bill_of_landing')}
                        className={"list-group-item" + this.allow('vn-bill-of-landing')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-upload"/> &nbsp;
                        Vận đơn VN
                    </Link>
                    <Link
                        to={Tools.toUrl('check_bill')}
                        className={"list-group-item" + this.allow('check-bill')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-thumbs-up"/> &nbsp;
                        Kiểm hàng
                    </Link>
                    <Link
                        to={Tools.toUrl('export_bill')}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix(
                                    '/admin/export_bill', 
                                    this.props['data-location'].pathname
                                )?" active":"") + this.allow('export-bill')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-export"/> &nbsp;
                        Xuất hàng
                    </Link>
                    {/*
                    <Link
                        to={Tools.toUrl('export_bol_daily')}
                        className={"list-group-item" + this.allow('export-bol-daily')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-export"/> &nbsp;
                        Xuất hàng theo ngày
                    </Link>
                    <Link
                        to={Tools.toUrl('bol_report')}
                        className={"list-group-item" + this.allow('bol-report')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-list-alt"/> &nbsp;
                        Bill report
                    </Link>
                    */}
                    <Link
                        to={Tools.toUrl('lost')}
                        className={"list-group-item" + this.allow('lost')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-ban-circle"/> &nbsp;
                        Hàng thất lạc
                    </Link>
                    <Link
                        to={Tools.toUrl('config')}
                        className={"list-group-item" + this.allow('config')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-cog"/> &nbsp;
                        Cấu hình
                    </Link>
                    <Link
                        to={Tools.toUrl('permission')}
                        className={"list-group-item" + this.allow('permission')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-flag"/> &nbsp;
                        Quyền
                    </Link>
                    <Link
                        to={Tools.toUrl('role-type')}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix(
                                    '/admin/role/', 
                                    this.props['data-location'].pathname
                                )?" active":"") + this.allow('role-type')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-lock"/> &nbsp;
                        Phân quyền
                    </Link>
                    <Link
                        to={Tools.toUrl('shop')}
                        className={"list-group-item" + this.allow('shop')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-shopping-cart"/> &nbsp;
                        Shop
                    </Link>
                    <Link
                        to={Tools.toUrl('category')}
                        className={"list-group-item" + this.allow('category')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-folder-open"/> &nbsp;
                        Danh mục
                    </Link>
                    <Link
                        to={Tools.toUrl('category', ['article'])}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix(
                                    '/admin/article/', 
                                    this.props['data-location'].pathname
                                )?" active":"") + this.allow('article')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-book"/> &nbsp;
                        Bài viết
                    </Link>
                    <Link
                        to={Tools.toUrl('category', ['banner'])}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix(
                                    '/admin/banner/', 
                                    this.props['data-location'].pathname
                                )?" active":"") + this.allow('category')}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-picture"/> &nbsp;
                        Banner
                    </Link>
                </ul>
            )
        }else{
            return (
                <ul className="list-group">
                    <Link to={URL_PREFIX} className="list-group-item" activeClassName="active" onlyActiveOnIndex>
                        <span className="glyphicon glyphicon-home"/> &nbsp;
                        T.T cá nhân
                    </Link>
                    {/*
                        <Link to={Tools.toUrl('user_accounting')} className="list-group-item" activeClassName="active">
                            <span className="glyphicon glyphicon-piggy-bank"/> &nbsp;
                            Kế toán
                        </Link>
                    */}
                    <Link to={Tools.toUrl('user_transaction')} className="list-group-item" activeClassName="active">
                        <span className="glyphicon glyphicon-piggy-bank"/> &nbsp;
                        Kế toán
                    </Link>
                    <Link to={Tools.toUrl('address')} className="list-group-item" activeClassName="active">
                        <span className="glyphicon glyphicon-road"/> &nbsp;
                        Địa chỉ nhận hàng
                    </Link>
                    <Link to={Tools.toUrl('bank')} className="list-group-item" activeClassName="active">
                        <span className="glyphicon glyphicon-usd"/> &nbsp;
                        Ngân hàng
                    </Link>
                    <Link to={Tools.toUrl('cart', [1])} className="list-group-item" activeClassName="active">
                        <span className="glyphicon glyphicon-bookmark"/> &nbsp;
                        Giỏ hàng
                    </Link>
                    <Link
                        to={Tools.toUrl('order', ['normal', 'all'])}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix('/user/order/normal', 
                                    this.props['data-location'].pathname
                                )?" active":"")}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-shopping-cart"/> &nbsp;
                        Đơn order
                    </Link>
                    {/*
                    <Link 
                        to={Tools.toUrl('order_statistics')} 
                        className="list-group-item" activeClassName="active">
                        <span className="glyphicon glyphicon-shopping-cart"/> &nbsp;
                        Thống kê đơn order
                    </Link>
                    */}
                    <Link
                        to={Tools.toUrl('bill_of_landing', [2, 'all'])}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix(
                                    '/user/bill_of_landing/', 
                                    this.props['data-location'].pathname)?
                                " active":"")
                        }
                        activeClassName="active">
                        <span className="glyphicon glyphicon-tag"/> &nbsp;
                        Vận đơn
                    </Link>
                    <Link
                        to={Tools.toUrl('bol_check')}
                        className="list-group-item"
                        activeClassName="active">
                        <span className="glyphicon glyphicon-tag"/> &nbsp;
                        K.Tra v.đơn
                    </Link>
                    <Link
                        to={Tools.toUrl('export_bill')}
                        className={
                            "list-group-item" + (
                                Tools.matchPrefix(
                                    '/user/export_bill', 
                                    this.props['data-location'].pathname
                                )?" active":"")}
                        activeClassName="active">
                        <span className="glyphicon glyphicon-export"/> &nbsp;
                        Xuất hàng
                    </Link>
                    <Link 
                        to={Tools.toUrl('lost')} 
                        className="list-group-item" activeClassName="active">
                        <span className="glyphicon glyphicon-ban-circle"/> &nbsp;
                        Hàng thất lạc
                    </Link>
                </ul>
            )
        }
    }

    _renderToggleMenu(){
        if(this.state.showHambuger){
            return (
                <div className="heading non-printable">
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    <span
                        onClick={() => this.onSetSidebarOpen(true)}
                        style={{fontSize: 22}}
                        className="hambuger glyphicon glyphicon-menu-hamburger"></span>
                    <span className="heading-logo">
                        {APP_TITLE}
                    </span>
                    {this._renderRightDropdown()}
                </div>
            );
        }
        return (
            <div className="heading non-printable">
                {this._renderRightDropdown()}
            </div>
        );
    }

    _renderRightDropdown(){
        let toggleButton = <span type="button" className="pointer" onClick={this.toggleDropdown}>
            {this.getFullName()}&nbsp;
            <span className="caret"></span>
        </span>;
        let menuOptions = {
            isOpen: this.state.isMenuOpen,
            close: this.closeDropdown,
            toggle: toggleButton,
            animate: true,
            menuAlign: 'right',
            textAlign: 'left'
        };
        return (
            <span className="main-right-dropdown">
                <DropdownMenu {...menuOptions}>
                    <li>
                        <Link to={Tools.toUrl('')}>
                            <span
                                className="glyphicon glyphicon-user"></span>&nbsp; Profile
                        </Link>
                    </li>
                    <li>
                        <a onClick={() => this.handleLogout()}>
                            <span
                                className="glyphicon glyphicon-off"></span>&nbsp; Logout
                        </a>
                    </li>
                </DropdownMenu>
            </span>
        );
    }

    render(){
        let sidebarContent = (
            <div className="sidebar-content non-printable">
                <div className="heading">
                    <span className="heading-logo">
                        {APP_TITLE}
                    </span>
                </div>
                {this._renderMenu()}
            </div>
        );
        var styles = {
            sidebar: {
                backgroundColor: 'white',
                zIndex: 6,
                transition: 'none',
                WebkitTransition: 'none'
            },
            content:{
                transition: 'none',
                WebkitTransition: 'none'
            },
            overlay: {
                zIndex: 5
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

export default NavWrapper;
