import Tools from 'helpers/Tools';

function orderItemReducer(state=[], action){
	const PREFIX = 'CHECK_BILL' + '_';
	let index = -1;
	switch(action.type){
		case PREFIX + 'CHECKED_QUANTITY':
			index = action.index;
			return [
				...state.slice(0, index),
				{...state.slice(index, index + 1)[0], ...action.data},
				...state.slice(index + 1)
			];
		case PREFIX + 'CHECKED_STATUS':
			index = action.index;
			return [
				...state.slice(0, index),
				{...state.slice(index, index + 1)[0], ...action.data},
				...state.slice(index + 1)
			];
		case PREFIX + 'CHECK_ALL_ITEM_OF_PURCHASE':
			return state.map(value => {
				return {...value, checking_quantity: value.checked_quantity};
			});
		case PREFIX + 'UNCHECK_ALL_ITEM_OF_PURCHASE':
			return state.map(value => {
				return {...value, checking_quantity: 0};
			});
		default:
			return state;
	}
}

function checkBillReducer(state = {}, action){
	const PREFIX = 'CHECK_BILL' + '_';
	let index = -1;
	switch(action.type){
		case PREFIX + 'OBJ':
			return {
				...state,
				obj: {...state.obj, ...action.data}
			};
		case PREFIX + 'KEYWORD':
			return {
				...state,
				keyword: action.data
			};
		case PREFIX + 'NEW_LIST':
			return {
				...state,
				list: [...action.data.list],
				listCheckItemStatus: [...action.data.listCheckItemStatus],
				pages: action.data.pages
			};
		case PREFIX + 'LIST_PURCHASE':
			return {
				...state,
				listPurchase: [...action.data.list]
			};
		case PREFIX + 'UPDATE_PURCHASE':
			return {
				...state,
				listPurchase: [
					...state.listPurchase.slice(0, action.index),
					{...state.listPurchase.slice(action.index, action.index + 1)[0], ...action.data},
					...state.listPurchase.slice(action.index + 1)
				]
			};
		case PREFIX + 'CHECKED_QUANTITY':
			index = action.purchaseIndex;
			return {
				...state,
				listPurchase: [
					...state.listPurchase.slice(0, index),
					{
						...state.listPurchase.slice(index, index + 1)[0],
					   	order_items: orderItemReducer(state.listPurchase[index].order_items, action)
					},
					...state.listPurchase.slice(index + 1)
				]
			};
		case PREFIX + 'CHECKED_STATUS':
			index = action.purchaseIndex;
			return {
				...state,
				listPurchase: [
					...state.listPurchase.slice(0, index),
					{
						...state.listPurchase.slice(index, index + 1)[0],
					   	order_items: orderItemReducer(state.listPurchase[index].order_items, action)
					},
					...state.listPurchase.slice(index + 1)
				]
			};
		case PREFIX + 'CHECK_ALL_ITEM_OF_PURCHASE':
			return {
				...state,
				listPurchase: [
					...state.listPurchase.slice(0, action.index),
					{
						...state.listPurchase.slice(action.index, action.index + 1)[0],
					   	order_items: orderItemReducer(state.listPurchase[action.index].order_items, action)
					},
					...state.listPurchase.slice(action.index + 1)
				]
			};
		case PREFIX + 'UNCHECK_ALL_ITEM_OF_PURCHASE':
			return {
				...state,
				listPurchase: [
					...state.listPurchase.slice(0, action.index),
					{
						...state.listPurchase.slice(action.index, action.index + 1)[0],
					   	order_items: orderItemReducer(state.listPurchase[action.index].order_items, action)
					},
					...state.listPurchase.slice(action.index + 1)
				]
			};
		case PREFIX + 'ADD_ITEM':
			return {
				...state,
				list: [{...action.data}, ...state.list]
			};
		case PREFIX + 'EDIT_ITEM':
			return {
				...state,
				list: [
					...state.list.slice(0, action.index),
					{...state.list.slice(action.index, action.index + 1)[0], ...action.data},
					...state.list.slice(action.index + 1)
				]
			};
		case PREFIX + 'CHECK_ALL':
			return {
				...state,
				list: state.list.map(value => {
					return {...value, checked: true};
				})
			};
		case PREFIX + 'UNCHECK_ALL':
			return {
				...state,
				list: state.list.map(value => {
					return {...value, checked: false};
				})
			};
		case PREFIX + 'REMOVE_ITEM':
			return {
				...state,
				list: Tools.ignoreIndex(state.list, action.listIndex)
			};
		default:
			return state;
	}
}

export default checkBillReducer;