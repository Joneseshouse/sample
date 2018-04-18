export function updateProfile(data){
	return { type: 'AUTH_UPDATE_PROFILE', data }
}

export function logout(data){
	return { type: 'AUTH_LOGOUT', data }
}

export function wrapperAction(action){
	const PREFIX = 'WRAPPER' + '_';
	switch(action){
		case 'update':
			return { type: PREFIX + 'UPDATE'};
	}
}

export function configAction(action, data=[], index=null){
	const PREFIX = 'CONFIG' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function adminAction(action, data=[], index=null){
	const PREFIX = 'ADMIN' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
		case 'setListRole':
			return { type: PREFIX + 'SET_LIST_ROLE', data };
		case 'setDefaultRole':
			return { type: PREFIX + 'SET_DEFAULT_ROLE', data };
	}
}
export function userAction(action, data=[], index=null){
	const PREFIX = 'USER' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'listDathangAdmin':
			return { type: PREFIX + 'LIST_DATHANG_ADMIN', data };
		case 'defaultAdmin':
			return { type: PREFIX + 'DEFAULT_ADMIN', data };
		case 'listUser':
			return { type: PREFIX + 'LIST_USER', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
		case 'setListRegion':
			return { type: PREFIX + 'SET_LIST_REGION', data };
		case 'setDefaultRegion':
			return { type: PREFIX + 'SET_DEFAULT_REGION', data };
		case 'listAreaCode':
			return { type: PREFIX + 'LIST_AREA_CODE', data };
		case 'defaultAreaCode':
			return { type: PREFIX + 'DEFAULT_AREA_CODE', data };
	}
}
export function permissionAction(action, data=[], index=null){
	const PREFIX = 'PERMISSION' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function roleTypeAction(action, data=[], index=null){
	const PREFIX = 'ROLE_TYPE' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function roleAction(action, data=[], index=null){
	const PREFIX = 'ROLE' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function categoryAction(action, data, index=null){
	const PREFIX = 'CATEGORY' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function bannerAction(action, data, index=null){
	const PREFIX = 'BANNER' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function articleAction(action, data, index=null){
	const PREFIX = 'ARTICLE' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function addressAction(action, data, index=null){
	const PREFIX = 'ADDRESS' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
		case 'listAreaCode':
			return { type: PREFIX + 'LIST_AREA_CODE', data };
		case 'defaultAreaCode':
			return { type: PREFIX + 'DEFAULT_AREA_CODE', data };
	}
}
export function bankAction(action, data, index=null){
	const PREFIX = 'BANK' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function cartAction(action, data, shopIndex=null, itemIndex=null){
	const PREFIX = 'CART' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, shopIndex, itemIndex };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
		case 'checkAllShop':
			return { type: PREFIX + 'CHECK_ALL_SHOP', shopIndex};
		case 'uncheckAllShop':
			return { type: PREFIX + 'UNCHECK_ALL_SHOP', shopIndex};
		case 'getTotalWhenChecked':
			return { type: PREFIX + 'GETT_TOTAL_WHEN_CHECKED', data};
	}
}
export function grabbingAction(action, data=[], index=null){
	const PREFIX = 'GRABBING' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function shopAction(action, data=[], index=null){
	const PREFIX = 'SHOP' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function orderAction(action, data=[], index=null){
	const PREFIX = 'ORDER' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAddress':
			return { type: PREFIX + 'LIST_ADDRESS', data };
		case 'defaultAddress':
			return { type: PREFIX + 'DEFAULT_ADDRESS', data };
		case 'listStatus':
			return { type: PREFIX + 'LIST_STATUS', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'defaultAdmin':
			return { type: PREFIX + 'DEFAULT_ADMIN', data };
		case 'listUser':
			return { type: PREFIX + 'LIST_USER', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function orderStatisticsAction(action, data=[], index=null){
	const PREFIX = 'ORDER_STATISTICS' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAddress':
			return { type: PREFIX + 'LIST_ADDRESS', data };
		case 'defaultAddress':
			return { type: PREFIX + 'DEFAULT_ADDRESS', data };
		case 'listStatus':
			return { type: PREFIX + 'LIST_STATUS', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'defaultAdmin':
			return { type: PREFIX + 'DEFAULT_ADMIN', data };
		case 'listUser':
			return { type: PREFIX + 'LIST_USER', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function orderDetailAction(action, data=[], index=null){
	const PREFIX = 'ORDER_DETAIL' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'objItem':
			return { type: PREFIX + 'OBJ_ITEM', data };
		case 'objPurchase':
			return { type: PREFIX + 'OBJ_PURCHASE', data };
		case 'objBillOfLanding':
			return { type: PREFIX + 'OBJ_BILL_OF_LANDING', data };
		case 'objCheckItemStatus':
			return { type: PREFIX + 'OBJ_CHECK_ITEM_STATUS', data };
		case 'selectedShop':
			return { type: PREFIX + 'SELECTED_SHOP', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listBillOfLanding':
			return { type: PREFIX + 'LIST_BILL_OF_LANDING', data };
		case 'listAddress':
			return { type: PREFIX + 'LIST_ADDRESS', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'defaultAdmin':
			return { type: PREFIX + 'DEFAULT_ADMIN', data };
		case 'listNote':
			return { type: PREFIX + 'LIST_NOTE', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
            return { type: PREFIX + 'EDIT_ITEM', data, index: index };
        case 'check':
            return { type: PREFIX + 'CHECK', data };
        case 'checkPurchase':
            return { type: PREFIX + 'CHECK_PURCHASE', data };
        case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };	
	}
}
export function billOfLandingAction(action, data=[], index=null){
	const PREFIX = 'BILL_OF_LANDING' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAddress':
			return { type: PREFIX + 'LIST_ADDRESS', data };
		case 'listUser':
			return { type: PREFIX + 'LIST_USER', data };
		case 'defaultAddress':
			return { type: PREFIX + 'DEFAULT_ADDRESS', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function cnBillOfLandingFailAction(action, data=[], index=null){
	const PREFIX = 'CN_BILL_OF_LANDING_FAIL' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function cnBillOfLandingAction(action, data=[], index=null){
	const PREFIX = 'CN_BILL_OF_LANDING' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function vnBillOfLandingAction(action, data=[], index=null){
	const PREFIX = 'VN_BILL_OF_LANDING' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function exportBillAction(action, data=[], index=null){
	const PREFIX = 'EXPORT_BILL' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function exportBillDetailAction(action, data=[], index=null){
	const PREFIX = 'EXPORT_BILL_DETAIL' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'keyword':
			return { type: PREFIX + 'KEYWORD', data };
		case 'address_uid':
			return { type: PREFIX + 'ADDRESS_UID', data };
		case 'select':
			return { type: PREFIX + 'SELECT', data };
		case 'listPure':
			return { type: PREFIX + 'LIST_PURE', data };
		case 'listFail':
			return { type: PREFIX + 'LIST_FAIL', data };
		case 'addFail':
			return { type: PREFIX + 'ADD_FAIL', data };
		case 'editFail':
			return { type: PREFIX + 'EDIT_FAIL', data, index: index };
		case 'removeFail':
			return { type: PREFIX + 'REMOVE_FAIL', data, listIndex: index };
		case 'listSelected':
			return { type: PREFIX + 'LIST_SELECTED', data };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
	}
}
export function exportBillPreviewAction(action, data=[], index=null){
	const PREFIX = 'EXPORT_BILL_PREVIEW' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'address':
			return { type: PREFIX + 'ADDRESS', data };
		case 'listContact':
			return { type: PREFIX + 'LIST_CONTACT', data };
	}
}
export function exportBillDailyAction(action, data=[], index=null){
	const PREFIX = 'EXPORT_BILL_DAILY' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
	}
}
export function bolDailyAction(action, data=[], index=null){
	const PREFIX = 'BOL_DAILY' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
	}
}
export function bolCheckAction(action, data=[], index=null){
	const PREFIX = 'BOL_CHECK' + '_';
	switch(action){
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
	}
}
export function bolReportAction(action, data=[], index=null){
	const PREFIX = 'BOL_REPORT' + '_';
	switch(action){
		case 'list':
			return { type: PREFIX + 'LIST', data };
	}
}
export function rateLogAction(action, data=[], index=null){
	const PREFIX = 'RATE_LOG' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function userOrderLogAction(action, data=[], index=null){
	const PREFIX = 'USER_ORDER_LOG' + '_';
	switch(action){
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
	}
}
export function areaCodeAction(action, data=[], index=null){
	const PREFIX = 'AREA_CODE' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function checkBillAction(action, data=[], index=null, purchaseIndex=null){
	const PREFIX = 'CHECK_BILL' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'keyword':
			return { type: PREFIX + 'KEYWORD', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'listPurchase':
			return { type: PREFIX + 'LIST_PURCHASE', data };
		case 'updatePurchase':
			return { type: PREFIX + 'UPDATE_PURCHASE', data, index: index };
		case 'checkAllItemOfPurchase':
			return { type: PREFIX + 'CHECK_ALL_ITEM_OF_PURCHASE', index };
		case 'uncheckAllItemOfPurchase':
			return { type: PREFIX + 'UNCHECK_ALL_ITEM_OF_PURCHASE', index };
		case 'checkedQuantity':
			return { type: PREFIX + 'CHECKED_QUANTITY', data, index, purchaseIndex };
		case 'checkedStatus':
			return { type: PREFIX + 'CHECKED_STATUS', data, index, purchaseIndex };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function collectBolAction(action, data=[], index=null){
	const PREFIX = 'COLLECT_BOL' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}
export function userAccountingAction(action, data=[], index=null){
	const PREFIX = 'USER_ACCOUNTING' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'receipt':
			return { type: PREFIX + 'RECEIPT', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'listUser':
			return { type: PREFIX + 'LIST_USER', data };
		case 'listType':
			return { type: PREFIX + 'LIST_TYPE', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}

export function userTransactionAction(action, data=[], index=null){
	const PREFIX = 'USER_TRANSACTION' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'receipt':
			return { type: PREFIX + 'RECEIPT', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'listUser':
			return { type: PREFIX + 'LIST_USER', data };
		case 'listType':
			return { type: PREFIX + 'LIST_TYPE', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}

export function adminTransactionAction(action, data=[], index=null){
	const PREFIX = 'ADMIN_TRANSACTION' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'receipt':
			return { type: PREFIX + 'RECEIPT', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'listUser':
			return { type: PREFIX + 'LIST_USER', data };
		case 'listType':
			return { type: PREFIX + 'LIST_TYPE', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}

export function receiptAction(action, data=[], index=null){
	const PREFIX = 'RECEIPT' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'listAdmin':
			return { type: PREFIX + 'LIST_ADMIN', data };
		case 'listUser':
			return { type: PREFIX + 'LIST_USER', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
	}
}

export function lostAction(action, data=[], index=null){
	const PREFIX = 'LOST' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
		case 'listBillLost':
			return { type: PREFIX + 'LIST_BILL_LOST', data};
		case 'listMessage':
			return { type: PREFIX + 'LIST_MESSAGE', data};
	}
}


export function chatLostAction(action, data=[], index=null){
	const PREFIX = 'CHAT_LOST' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
		case 'listBillLost':
			return { type: PREFIX + 'LIST_BILL_LOST', data};
		case 'listMessage':
			return { type: PREFIX + 'LIST_MESSAGE', data};
	}
}

export function contactAction(action, data=[], index=null){
	const PREFIX = 'CONTACT' + '_';
	switch(action){
		case 'obj':
			return { type: PREFIX + 'OBJ', data };
		case 'newList':
			return { type: PREFIX + 'NEW_LIST', data };
		case 'appendList':
			return { type: PREFIX + 'APPEND_LIST', data };
		case 'add':
			return { type: PREFIX + 'ADD_ITEM', data };
		case 'edit':
			return { type: PREFIX + 'EDIT_ITEM', data, index: index };
		case 'remove':
			return { type: PREFIX + 'REMOVE_ITEM', data, listIndex: index };
		case 'checkAll':
			return { type: PREFIX + 'CHECK_ALL'};
		case 'uncheckAll':
			return { type: PREFIX + 'UNCHECK_ALL'};
		case 'listBillLost':
			return { type: PREFIX + 'LIST_BILL_LOST', data};
		case 'listMessage':
			return { type: PREFIX + 'LIST_MESSAGE', data};
	}
}
