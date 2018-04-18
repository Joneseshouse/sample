import { combineReducers } from 'redux';
import { routerReducer } from 'react-router-redux';
import {reducer as toastrReducer} from 'react-redux-toastr';
import { reducer as formReducer } from 'redux-form'

// import posts from './posts';
// import comments from './comments';
import commonReducer from './commonReducer';
import wrapperReducer from './wrapperReducer';
import authReducer from './authReducer';
import adminReducer from './adminReducer';
import userReducer from './userReducer';
import configReducer from './configReducer';
import permissionReducer from './permissionReducer';
import roleTypeReducer from './roleTypeReducer';
import roleReducer from './roleReducer';
import categoryReducer from './categoryReducer';
import bannerReducer from './bannerReducer';
import articleReducer from './articleReducer';
import addressReducer from './addressReducer';
import bankReducer from './bankReducer';
import cartReducer from './cartReducer';
import grabbingReducer from './grabbingReducer';
import shopReducer from './shopReducer';
import orderReducer from './orderReducer';
import orderStatisticsReducer from './orderStatisticsReducer';
import orderDetailReducer from './orderDetailReducer';
import billOfLandingReducer from './billOfLandingReducer';
import cnBillOfLandingFailReducer from './cnBillOfLandingFailReducer';
import cnBillOfLandingReducer from './cnBillOfLandingReducer';
import vnBillOfLandingReducer from './vnBillOfLandingReducer';
import exportBillReducer from './exportBillReducer';
import exportBillDetailReducer from './exportBillDetailReducer';
import exportBillPreviewReducer from './exportBillPreviewReducer';
import exportBillDailyReducer from './exportBillDailyReducer';
import bolDailyReducer from './bolDailyReducer';
import bolCheckReducer from './bolCheckReducer';
import bolReportReducer from './bolReportReducer';
import rateLogReducer from './rateLogReducer';
import userOrderLogReducer from './userOrderLogReducer';
import areaCodeReducer from './areaCodeReducer';
import checkBillReducer from './checkBillReducer';
import collectBolReducer from './collectBolReducer';
import userAccountingReducer from './userAccountingReducer';
import userTransactionReducer from './userTransactionReducer';
import adminTransactionReducer from './adminTransactionReducer';
import receiptReducer from './receiptReducer';
import lostReducer from './lostReducer';
import chatLostReducer from './chatLostReducer';
import contactReducer from './contactReducer';
import spinner from './spinner';
const rootReducer = combineReducers({
	// posts,
	// comments,
	commonReducer,
	wrapperReducer,
	authReducer,
	adminReducer,
	userReducer,
	configReducer,
	permissionReducer,
	roleTypeReducer,
	roleReducer,
	categoryReducer,
	bannerReducer,
	articleReducer,
	addressReducer,
	bankReducer,
	cartReducer,
	grabbingReducer,
	shopReducer,
	orderReducer,
	orderStatisticsReducer,
	orderDetailReducer,
	billOfLandingReducer,
	cnBillOfLandingFailReducer,
	cnBillOfLandingReducer,
	vnBillOfLandingReducer,
	exportBillReducer,
	exportBillDetailReducer,
	exportBillPreviewReducer,
	exportBillDailyReducer,
	bolDailyReducer,
	bolCheckReducer,
	bolReportReducer,
	rateLogReducer,
	userOrderLogReducer,
	areaCodeReducer,
	checkBillReducer,
	collectBolReducer,
	userAccountingReducer,
	userTransactionReducer,
	adminTransactionReducer,
	receiptReducer,
	lostReducer,
	chatLostReducer,
	contactReducer,
	spinner,
	form: formReducer,
	routing: routerReducer,
	toastr: toastrReducer
});

export default rootReducer;