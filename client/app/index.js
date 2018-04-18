import {URL_PREFIX, APP} from 'app/constants';

import 'libs/bootstrap/css/bootstrap.min.css';

import 'react-select/dist/react-select.min.css';
import 'react-redux-toastr/src/styles/index.scss';
import 'react-dd-menu/dist/react-dd-menu.min.css';
import 'rc-table/assets/index.css';
import './styles/main.styl';

import React from 'react';
import ReactDOM from 'react-dom';

import Router from 'react-router/lib/Router';
import Route from 'react-router/lib/Route';
import Redirect from 'react-router/lib/Redirect';
import IndexRoute from 'react-router/lib/IndexRoute';
import browserHistory from 'react-router/lib/browserHistory';


import { Provider } from 'react-redux';
import ReduxToastr from 'react-redux-toastr';

import store, { history } from './store';
import Tools from 'helpers/Tools';

/* COMMON COMPONENTS*/
import App from 'components/App';
import NotFound from 'utils/components/404';

/* AUTH COMPONENTS*/
import Login from 'components/auth/Login';
import Profile from 'components/auth/Profile';
import PasswordConfirm from 'components/auth/PasswordConfirm';

/* Admin COMPONENTS*/
import Admin from 'components/admin/Admin';
/* User COMPONENTS*/
import User from 'components/user/User';
import Signup from 'components/user/components/signup/Signup';
/* CONFIG COMPONENTS*/
import Config from 'components/config/Config';
/* CATEGORY COMPONENTS*/
import Category from 'components/category/Category';
/* ARTICLE COMPONENTS*/
import Article from 'components/article/Article';
import ArticleDetail from 'components/article/ArticleDetail';
/* BANNER COMPONENTS*/
import Banner from 'components/banner/Banner';
/* PERMISSION COMPONENTS*/
import Permission from 'components/permission/Permission';
/* ROLE TYPE COMPONENTS*/
import RoleType from 'components/roleType/RoleType';
/* ROLE COMPONENTS*/
import Role from 'components/role/Role';
/* ADDRESS COMPONENTS*/
import Address from 'components/address/Address';
/* BANK COMPONENTS*/
import Bank from 'components/bank/Bank';
/* CART COMPONENTS*/
import Cart from 'components/cart/Cart';
/* GRABBING COMPONENTS*/
import Grabbing from 'components/grabbing/Grabbing';
/* SHOP COMPONENTS*/
import Shop from 'components/shop/Shop';
/* ORDER COMPONENTS*/
import Order from 'components/order/Order';
/* ORDER_STATISTICS COMPONENTS*/
import OrderStatistics from 'components/orderStatistics/OrderStatistics';
/* ORDER DETAIL COMPONENTS*/
import OrderDetail from 'components/orderDetail/OrderDetail';
/* BILL_OF_LANDING COMPONENTS*/
import BillOfLanding from 'components/billOfLanding/BillOfLanding';
/* CN_BILL_OF_LANDING_FAIL COMPONENTS*/
import CnBillOfLandingFail from 'components/cnBillOfLandingFail/CnBillOfLandingFail';
/* CN_BILL_OF_LANDING COMPONENTS*/
import CnBillOfLanding from 'components/cnBillOfLanding/CnBillOfLanding';
/* VN_BILL_OF_LANDING COMPONENTS*/
import VnBillOfLanding from 'components/vnBillOfLanding/VnBillOfLanding';
/* EXPORT_BILL COMPONENTS*/
import ExportBill from 'components/exportBill/ExportBill';
/* EXPORT_BILL_DETAIL COMPONENTS*/
import ExportBillDetail from 'components/exportBillDetail/ExportBillDetail';
/* EXPORT_BILL_PREVIEW COMPONENTS*/
import ExportBillPreview from 'components/exportBillPreview/ExportBillPreview';
/* EXPORT_BILL_DAILY COMPONENTS*/
import ExportBillDaily from 'components/exportBillDaily/ExportBillDaily';
/* BOL_DAILY COMPONENTS*/
import BolDaily from 'components/bolDaily/BolDaily';
/* BOL_CHECK COMPONENTS*/
import BolCheck from 'components/bolCheck/BolCheck';
/* BOL_REPORT COMPONENTS*/
import BolReport from 'components/bolReport/BolReport';
/* RATE_LOG COMPONENTS*/
import RateLog from 'components/rateLog/RateLog';
/* AREA_CODE COMPONENTS*/
import AreaCode from 'components/areaCode/AreaCode';
/* CHECK_BILL COMPONENTS*/
import CheckBill from 'components/checkBill/CheckBill';
/* COLLECT_BOL COMPONENTS*/
import CollectBol from 'components/collectBol/CollectBol';
/* USER_ACCOUNTING COMPONENTS*/
import UserAccounting from 'components/userAccounting/UserAccounting';
/* USER_TRANSACTION COMPONENTS*/
import UserTransaction from 'components/userTransaction/UserTransaction';
/* ADMIN_TRANSACTION COMPONENTS*/
import AdminTransaction from 'components/adminTransaction/AdminTransaction';
/* RECEIPT COMPONENTS*/
import Receipt from 'components/receipt/Receipt';
/* RECEIPT DETAIL COMPONENTS*/
import ReceiptPreview from 'components/receiptPreview/ReceiptPreview';
/* LOST COMPONENTS */
import Lost from 'components/lost/Lost';
/* Contact COMPONENTS*/
import Contact from 'components/contact/Contact';
/* HOME COMPONENTS*/
import Home from 'components/landing/Home';

const changeRouteHandle = (nextState, replace, callback) => {
	const pathname = nextState.location.pathname;
	const path = pathname.replace(URL_PREFIX, '');
	if(pathname === URL_PREFIX + 'login'){
		// Neu da login va vao trang login thi ve trang chu
		if(Tools.getToken()){
			replace(URL_PREFIX);
		}
	}else{
		// Neu khac trang login thi kiem tra login
		const login = Tools.checkLoginRequiredRoute(nextState.routes, path);
		if(login && !Tools.getToken()){
			replace(URL_PREFIX + 'login');
		}
	}
	callback();
}

const onEnter = (nextState, replace, callback) => {
	changeRouteHandle(nextState, replace, callback);
}

const onChange = (prevState, nextState, replace, callback) => {
	changeRouteHandle(nextState, replace, callback);
}
const rootElementAdmin = (
	<Provider store={store}>
		<div>
			<Router history={history}>
				<Route path={URL_PREFIX} component={App} onChange={onChange} onEnter={onEnter}>
					{/* MAIN ROUTES */}
					<IndexRoute
						component={Profile}
						params={{login: true}}></IndexRoute>
					<Route
						path="login"
						component={Login}
						params={{login: false}}></Route>
					<Route
						path="reset_password_confirm/:type/:token"
						component={PasswordConfirm}
						params={{login: false}}></Route>
					<Route
						path="change_password_confirm/:type/:token"
						component={PasswordConfirm}
						params={{login: false}}></Route>

					{/* ADMIN ROUTES */}
					<Route
						path="admin"
						component={Admin}
						params={{login: true}}></Route>

					{/* USER ROUTES */}
					{/*
						<Route
							path="user"
							component={User}
							params={{login: true}}></Route>
					*/}
					<Route
						path="user/:id"
						component={User}
						params={{login: true}}></Route>

					{/* CONFIG ROUTES */}
					<Route
						path="config"
						component={Config}
						params={{login: true}}></Route>

					{/* LOST ROUTES */}
					<Route
						path="lost"
						component={Lost}
						params={{login: true}}></Route>

					{/* PERMISSION ROUTES */}
					<Route
						path="permission"
						component={Permission}
						params={{login: true}}></Route>

					{/* ADDRESS ROUTES */}
					<Route
						path="address"
						component={Address}
						params={{login: true}}></Route>

					{/* ROLE TYPE ROUTES */}
					<Route
						path="role-type"
						component={RoleType}
						params={{login: true}}></Route>

					{/* ROLE ROUTES */}
					<Route
						path="role/:role_type_id"
						component={Role}
						params={{login: true}}></Route>

					{/* SHOP ROUTES */}
					<Route
						path="shop"
						component={Shop}
						params={{login: true}}></Route>

					{/* ORDER ROUTES */}
					<Route
						path="order/:type/:status"
						component={Order}
						params={{login: true}}></Route>

					{/* ORDER DETAIL ROUTES */}
					<Route
						path="order/:type/:status/:id"
						component={OrderDetail}
						params={{login: true}}></Route>

					{/* ORDER STATISTICS ROUTES */}
					<Route
						path="order_statistics"
						component={OrderStatistics}
						params={{login: true}}></Route>

					{/* GRABBING ROUTES */}
					<Route
						path="grabbing"
						component={Grabbing}
						params={{login: true}}></Route>

					{/* COLLECT_BOL ROUTES */}
					<Route
						path="collect_bol"
						component={CollectBol}
						params={{login: true}}></Route>

					{/* CATEGORY ROUTES */}
					<Route
						path="category"
						component={Category}
						params={{login: true}}></Route>
					<Route
						path="category/:type"
						component={Category}
						params={{login: true}}></Route>

					{/* BANNER ROUTES */}
					<Route
						path="banner/:category_id"
						component={Banner}
						params={{login: true}}></Route>
					<Route
						path="banner/detail/:id"
						component={Banner}
						params={{login: true}}></Route>

					{/* ARTICLE ROUTES */}
					<Route
						path="article/:category_id"
						component={Article}
						params={{login: true}}></Route>
					<Route
						path="article/detail/:id"
						component={ArticleDetail}
						params={{login: true}}></Route>

					{/* BILL_OF_LANDING ROUTES */}
					<Route
						path="bill_of_landing/:insurance_register/:type(/:date)"
						component={BillOfLanding}
						params={{login: true}}></Route>

					{/* BOL_DAILY ROUTES */}
					<Route
						path="bol_daily"
						component={BolDaily}
						params={{login: true}}></Route>

					{/* BOL_CHECK ROUTES */}
					<Route
						path="bol_check"
						component={BolCheck}
						params={{login: true}}></Route>

					{/* CN_BILL_OF_LANDING_FAIL ROUTES */}
					<Route
						path="cn_bill_of_landing_fail"
						component={CnBillOfLandingFail}
						params={{login: true}}></Route>

					{/* CN_BILL_OF_LANDING ROUTES */}
					<Route
						path="cn_bill_of_landing"
						component={CnBillOfLanding}
						params={{login: true}}></Route>

					{/* VN_BILL_OF_LANDING ROUTES */}
					<Route
						path="vn_bill_of_landing"
						component={VnBillOfLanding}
						params={{login: true}}></Route>

					{/* EXPORT_BILL ROUTES */}
					<Route
						path="export_bill"
						component={ExportBill}
						params={{login: true}}></Route>

					{/* EXPORT_BILL_DETAIL ROUTES */}
					<Route
						path="export_bill_detail"
						component={ExportBillDetail}
						params={{login: true}}></Route>

					{/* EXPORT_BILL_DAILY ROUTES */}
					<Route
						path="export_bol_daily"
						component={ExportBillDaily}
						params={{login: true}}></Route>

					{/* EXPORT_BILL_PREVIEW ROUTES */}
					<Route
						path="export_bill/:id"
						component={ExportBillPreview}
						params={{login: true}}></Route>

					{/* BOL_REPORT ROUTES */}
					<Route
						path="bol_report"
						component={BolReport}
						params={{login: true}}></Route>

					{/* RATE_LOG ROUTES */}
					<Route
						path="rate_log"
						component={RateLog}
						params={{login: true}}></Route>

					{/* AREA_CODE ROUTES */}
					<Route
						path="area_code"
						component={AreaCode}
						params={{login: true}}></Route>

					{/* CHECK_BILL ROUTES */}
					<Route
						path="check_bill"
						component={CheckBill}
						params={{login: true}}></Route>

					{/* USER_ACCOUNTING ROUTES */}
					<Route
						path="user_accounting"
						component={UserAccounting}
						params={{login: true}}></Route>

					{/* USER_TRANSACTION ROUTES */}
					<Route
						path="user_transaction"
						component={UserTransaction}
						params={{login: true}}></Route>

					{/* ADMIN_TRANSACTION ROUTES */}
					<Route
						path="admin_transaction"
						component={AdminTransaction}
						params={{login: true}}></Route>

					{/* RECEIPT ROUTES */}
					<Route
						path="receipt"
						component={Receipt}
						params={{login: true}}></Route>

					{/* RECEIPT DETAIL ROUTES */}
					<Route
						path="receipt/:id"
						component={ReceiptPreview}
						params={{login: true}}></Route>

					{/* CONTAC ROUTES */}
					<Route
						path="contact"
						component={Contact}
						params={{login: true}}></Route>

					{/* MISSING ROUTES */}
					<Route
						path='*'
						component={NotFound}
						params={{login: false}}/>
				</Route>
			</Router>
			<ReduxToastr
				timeOut={8000}
				newestOnTop={false}
				position="top-right"/>
		</div>
	</Provider>
);

const rootElementUser = (
	<Provider store={store}>
		<div>
			<Router history={history}>
				<Route path={URL_PREFIX} component={App} onChange={onChange} onEnter={onEnter}>
					{/* MAIN ROUTES */}
					<IndexRoute
						component={Profile}
						params={{login: true}}></IndexRoute>
					<Route
						path="login"
						component={Login}
						params={{login: false}}></Route>
					<Route
						path="reset_password_confirm/:type/:token"
						component={PasswordConfirm}
						params={{login: false}}></Route>
					<Route
						path="change_password_confirm/:type/:token"
						component={PasswordConfirm}
						params={{login: false}}></Route>
					<Route
						path="address"
						component={Address}
						params={{login: true}}></Route>
					<Route
						path="bank"
						component={Bank}
						params={{login: true}}></Route>
					<Route
						path="cart(/:auth)"
						component={Cart}
						params={{login: true}}></Route>
					<Route
						path="cart"
						component={Cart}
						params={{login: false}}></Route>
					<Route
						path="signup"
						component={Signup}
						params={{login: false}}></Route>
					{/* ORDER ROUTES */}
					<Route
						path="order/:type/:status"
						component={Order}
						params={{login: true}}></Route>

					{/* ORDER STATISTICS ROUTES */}
					<Route
						path="order_statistics"
						component={OrderStatistics}
						params={{login: true}}></Route>

					{/* ORDER DETAIL ROUTES */}
					<Route
						path="order/:type/:status/:id"
						component={OrderDetail}
						params={{login: true}}></Route>

					{/* BILL_OF_LANDING ROUTES */}
					<Route
						path="bill_of_landing/:insurance_register/:type"
						component={BillOfLanding}
						params={{login: true}}></Route>

					{/* BOL_CHECK ROUTES */}
					<Route
						path="bol_check"
						component={BolCheck}
						params={{login: true}}></Route>

					{/* EXPORT_BILL ROUTES */}
					<Route
						path="export_bill"
						component={ExportBill}
						params={{login: true}}></Route>

					{/* EXPORT_BILL_PREVIEW ROUTES */}
					<Route
						path="export_bill/:id"
						component={ExportBillPreview}
						params={{login: true}}></Route>

					{/* USER_ACCOUNTING ROUTES */}
					<Route
						path="user_accounting"
						component={UserAccounting}
						params={{login: true}}></Route>

					{/* USER_TRANSACTION ROUTES */}
					<Route
						path="user_transaction"
						component={UserTransaction}
						params={{login: true}}></Route>

					{/* LOST ROUTES */}
					<Route
						path="lost"
						component={Lost}
						params={{login: true}}></Route>

					{/* MISSING ROUTES */}
					<Route
						path='*'
						component={NotFound}
						params={{login: false}}/>

				</Route>
			</Router>
			<ReduxToastr
				timeOut={4000}
				newestOnTop={false}
				position="bottom-right"/>
		</div>
	</Provider>
);

const rootElementLanding = (
	<Provider store={store}>
		<div>
			<Router history={history}>
				<Route path={URL_PREFIX} component={App} onChange={onChange} onEnter={onEnter}>
					{/* MAIN ROUTES */}
					<IndexRoute
						component={Home}
						params={{login: false}}></IndexRoute>

					{/* MISSING ROUTES */}
					<Route
						path='*'
						component={NotFound}
						params={{login: false}}/>
				</Route>
			</Router>
			<ReduxToastr
				timeOut={4000}
				newestOnTop={false}
				position="bottom-right"/>
		</div>
	</Provider>
);

if(['admin', 'radmin'].indexOf(APP) !== -1){
	ReactDOM.render(rootElementAdmin, document.getElementById('app'));
}else if(APP === 'user'){
	ReactDOM.render(rootElementUser, document.getElementById('app'));
}else{
	ReactDOM.render(rootElementLanding, document.getElementById('app'));
}
