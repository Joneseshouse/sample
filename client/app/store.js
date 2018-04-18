import { createStore, compose, applyMiddleware } from 'redux';
import { syncHistoryWithStore, routerMiddleware } from 'react-router-redux';
import browserHistory from 'react-router/lib/browserHistory';

// Import the root reducer
import rootReducer from './reducers/index';

//Remove when production
const composeEnhancers = window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose;

// Create an object for the default data
const middleware = routerMiddleware(browserHistory);
const listStatus = [
	{id: 'new', title: 'Chờ duyệt'},
	{id: 'confirm', title: 'Đã duyệt mua'},
	{id: 'purchasing', title: 'Đang g.dịch'},
	{id: 'purchased', title: 'G.dịch xong'},
	{id: 'complain', title: 'K.nại'},
	{id: 'done', title: 'Hoàn Thành'}
];
const currentYear = new Date().getFullYear();
const currentMonth = new Date().getMonth();
const defaultState = {
	commonReducer: {
		listRegion: [
			{id: 'HN', title: 'HN: Hà Nội'},
			{id: 'HNL', title: 'HNL: Lân cận Hà Nội'},
			{id: 'SG', title: 'SG: Sài Gòn'},
			{id: 'SGL', title: 'SGL: Lân cận Sài Gòn'},
			{id: 'DN', title: 'DN: Đà Nẵng'},
			{id: 'DNL', title: 'DNL: Lân cận Đà Nẵng'},
			{id: 'LK', title: 'LK: Lưu kho TQ'}
		]
	},
	wrapperReducer:{
		firstUpdate: true
	},
	authReducer: {
		login: {
			email: null,
			password: null
		}, profile: {
			email: null,
			first_name: null,
			last_name: null,
			role_id: null
		}
	},
	adminReducer: {
		list: [],
		obj: {},
		pages: 1,
		listRole: [],
		defaultRole: []
	},
	userReducer: {
		list: [],
		obj: {},
		pages: 1,
		listRegion: [],
		defaultRegion: null,
		listAdmin: [],
		listChamsocAdmin: [],
		listUser: [],
		defaultAdmin: null,
		listAreaCode: [],
		defaultAreaCode: null,
		listLockFilter: [
			{id: 'all', title: '--- Khoá + 0 khoá ---'},
			{id: 'yes', title: 'Khoá'},
			{id: 'no', title: 'Không khoá'}
		],
		listCareFilter: [
			{id: 'all', title: '--- Có + 0 NVCS ---'},
			{id: 'yes', title: 'Có NVCS'},
			{id: 'no', title: 'Không NVCS'}
		],
		listDebtFilter: [
			{id: 'all', title: '--- Có + 0 nợ ---'},
			{id: 'yes', title: 'Có nợ'},
			{id: 'no', title: 'Không nợ'}
		]
	},
	configReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	permissionReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	roleTypeReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	roleReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	categoryReducer: {
		list: [],
		obj: {},
		pages: 1,
		listType: [
			{id: 'article', title: 'Bài viết'},
		    {id: 'banner', title: 'Banner'}
		]
	},
	bannerReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	articleReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	addressReducer: {
		list: {},
		obj: {},
		pages: 1,
		listAreaCode: [],
		defaultAreaCode: null
	},
	bankReducer: {
		list: [],
		obj: {},
		pages: 1,
		listType: [
			{id: 'vn', title: 'Việt Nam'},
		    {id: 'cn', title: 'Trung Quốc'}
		]
	},
	cartReducer: {
		list: {},
		obj: {},
		pages: 1,
		totalSelected: 0,
		totalSelectedWithRate: 0
	},
	grabbingReducer: {
		list: {},
		obj: {},
		pages: 1
	},
	shopReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	orderReducer: {
		list: [],
		listAddress: [],
		defaultAddress: null,
		listAdmin: [],
		listUser: [],
		defaultAdmin: null,
		obj: {},
		pages: 1,
		listStatus
	},
	orderStatisticsReducer: {
		list: [],
		listAddress: [],
		defaultAddress: null,
		listAdmin: [],
		listUser: [],
		defaultAdmin: null,
		obj: {},
		pages: 1,
		listStatus
	},
	orderDetailReducer: {
		list: [],
		listBillOfLanding: [],
		obj: {},
		pages: 1,
		objItem: {},
		objPurchase: {},
		objBillOfLanding: {},
		selectedShop: null,
		listStatus,
		objStatus: {},
		listAddress: [],
		objAddress: {},
		listAdmin: [],
		listNote: [],
		objCheckItemStatus: {},
		orderItemId: null,
		defaultAdmin: null
	},
	billOfLandingReducer: {
		list: [],
		obj: {},
		pages: 1,
		listAddress: [],
		listUser: [],
		defaultAddress: null,
		listLandingStatus: [
			{id: 'Mới', title: 'Mới'},
			{id: 'Về TQ', title: 'Về TQ'},
			{id: 'Về VN', title: 'Về VN'},
			{id: 'Đã xuất', title: 'Đã xuất'}
		],
		listLandingStatusFilter: [
			{id: 'all', title: '--- Tất cả trạng thái ---'},
			{id: 'new', title: 'Mới'},
			{id: 'cn', title: 'Về TQ'},
			{id: 'vn', title: 'Về VN'},
			{id: 'export', title: 'Đã xuất'},
			{id: 'complain', title: 'Khiếu nại'}
		],
		listComplainType: [
			{id: 'change', title: 'Đổi hàng'},
			{id: 'change_discount', title: 'Đổi hàng & chiết khấu'},
			{id: 'reject', title: 'Trả hàng & nhận tiền'},
			{id: 'accept_discount', title: 'Nhận hàng & chiết khấu'}
		],
		listWoodenBoxFilter: [
			{id: 'all', title: '--- Đ.gỗ + 0 đ.gỗ ---'},
			{id: 'yes', title: 'Đóng gỗ'},
			{id: 'no', title: 'Không đóng gỗ'}
		]
	},
	cnBillOfLandingReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	vnBillOfLandingReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	exportBillReducer: {
		list: [],
		listAdmin: [],
		obj: {},
		pages: 1
	},
	exportBillDetailReducer: {
		listSelected: [],
		listPure: [],
		listFail: [],
		obj: {},
		keyword: '',
		address_uid: '',
		pages: 1
	},
	exportBillPreviewReducer: {
		obj: {},
		address: {},
		list_contact: []
	},
	exportBillDailyReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	bolDailyReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	bolCheckReducer: {
		list: [],
		listAdmin: [],
		pages: 1
	},
	bolReportReducer: {
		list: [],
		listYear: [0, 1, 2, 3].map(deltaYear => {
			return {id: currentYear - deltaYear, title: currentYear - deltaYear}
		}),
		listMonth: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(month => {
			return {id: month, title: "Tháng " + month}
		}),
		initFilter: {selected_year: currentYear, selected_month: currentMonth + 1}
	},
	rateLogReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	cnBillOfLandingFailReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	userOrderLogReducer: {
		list: [],
		pages: 1
	},
	areaCodeReducer: {
		list: [],
		obj: {},
		pages: 1
	},
	checkBillReducer: {
		list: [],
		listCheckItemStatus: [],
		listPurchase: [],
		obj: {},
		keyword: '',
		pages: 1
	},
	collectBolReducer: {
		list: [],
		obj: {},
		pages: 1,
		listAdmin: []
	},
	userAccountingReducer: {
		list: [],
		obj: {},
		receipt: {},
		pages: 1,
		listAdmin: [],
		listUser: [],
		listType: [],
		listMoneyType:[
			{id: '', title: '--- Chọn loại tiền ---'},
			{id: 'transfer', title: 'Chuyển khoản'},
			{id: 'cash', title: 'Tiền mặt'}
		]
		/*
		listType: [
			{id: '', title: '--- Chọn hàngh động ---'},
			{id: 'deposit', title: 'Gửi vào'},
			{id: 'withdraw', title: 'Rút ra'}
		]
		*/
	},
	userTransactionReducer: {
		list: [],
		obj: {},
		receipt: {},
		pages: 1,
		listAdmin: [],
		listUser: [],
		listType: [],
		listMoneyType:[
			{id: '', title: '--- Chọn loại tiền ---'},
			{id: 'transfer', title: 'Chuyển khoản'},
			{id: 'cash', title: 'Tiền mặt'}
		],
		total: {}
	},
	adminTransactionReducer: {
		list: [],
		obj: {},
		receipt: {},
		pages: 1,
		listAdmin: [],
		listUser: [],
		listType: [],
		listMoneyType:[
			{id: '', title: '--- Chọn loại tiền ---'},
			{id: 'transfer', title: 'Chuyển khoản'},
			{id: 'cash', title: 'Tiền mặt'}
		],
		total: {}
	},
	receiptReducer: {
		list: [],
		obj: {},
		pages: 1,
		listAdmin: [],
		listUser: [],
		listType: [],
		listMoneyType:[
			{id: '', title: '--- Chọn loại tiền ---'},
			{id: 'transfer', title: 'Chuyển khoản'},
			{id: 'cash', title: 'Tiền mặt'}
		],
		listHaveTransaction:[
			{id: '', title: '--- Có & 0 giao dịch ---'},
			{id: 'yes', title: 'Có giao dịch'},
			{id: 'no', title: 'Không giao dịch'}
		]
	},
	lostReducer: {
		list: [],
		obj: {},
		listBillLost: [],
		pages: 1
	},
	chatLostReducer:{
		list: [],
		obj: {},
		page: 1
	},
	contactReducer:{
		list: [],
		obj: {},
		page: 1
	},
	spinner: {
		show: false
	}
};
export const intState = {...defaultState};

const store = createStore(rootReducer, defaultState, composeEnhancers(applyMiddleware(middleware)));

export const history = syncHistoryWithStore(browserHistory, store);

export default store;