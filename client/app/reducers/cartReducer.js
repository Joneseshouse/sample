import Tools from 'helpers/Tools';

function shopItemData(state=[], action){
	const PREFIX = 'CART' + '_';
	switch(action.type){
		case PREFIX + 'EDIT_ITEM':
			return [
				...state.slice(0, action.itemIndex),
				{...state.slice(action.itemIndex, action.itemIndex + 1)[0], ...action.data},
				...state.slice(action.itemIndex + 1)
			];
		case PREFIX + 'CHECK_ALL':
			return state.map(value => {
				return {...value, checked: true};
			});
		case PREFIX + 'UNCHECK_ALL':
			return state.map(value => {
				return {...value, checked: false};
			});
		case PREFIX + 'CHECK_ALL_SHOP':
			return state.map(value => {
				return {...value, checked: true};
			});
		case PREFIX + 'UNCHECK_ALL_SHOP':
			return state.map(value => {
				return {...value, checked: false};
			});
		default:
			return state;
	}
}

function cartReducer(state = {}, action){
	const PREFIX = 'CART' + '_';
	switch(action.type){
		case PREFIX + 'OBJ':
			return {
				...state,
				obj: {...state.obj, ...action.data}
			};
		case PREFIX + 'NEW_LIST':
			return {
				...state,
				list: {...action.data.list}
			};
		case PREFIX + 'APPEND_LIST':
			return {
				...state,
				list: [...state.list, ...action.data]
			};
		case PREFIX + 'ADD_ITEM':
			return {
				...state,
				list: [{...action.data}, ...state.list]
			};
		case PREFIX + 'EDIT_ITEM':
			return {
				...state,
				list: {
					...state.list,
					shops: [
						...state.list.shops.slice(0, action.shopIndex),
					   	{
					   		...state.list.shops.slice(action.shopIndex, action.shopIndex + 1)[0],
					   		items: shopItemData(state.list.shops[action.shopIndex].items, action)
					   	},
						...state.list.shops.slice(action.shopIndex + 1)
					]
				}
			};
		case PREFIX + 'CHECK_ALL':
			return {
				...state,
				list: {
					... state.list,
					shops: state.list.shops.map(shop => {
						return {...shop, items: shopItemData(shop.items, action)}
					})
				}
			};
		case PREFIX + 'UNCHECK_ALL':
			return {
				...state,
				list: {
					... state.list,
					shops: state.list.shops.map(shop => {
						return {...shop, items: shopItemData(shop.items, action)}
					})
				}
			};
		case PREFIX + 'CHECK_ALL_SHOP':
			return {
				...state,
				list: {
					... state.list,
					shops: [
						...state.list.shops.slice(0, action.shopIndex),
					   	{
					   		...state.list.shops.slice(action.shopIndex, action.shopIndex + 1)[0],
					   		items: shopItemData(state.list.shops[action.shopIndex].items, action)
					   	},
						...state.list.shops.slice(action.shopIndex + 1)
					]
				}
			};
		case PREFIX + 'UNCHECK_ALL_SHOP':
			return {
				...state,
				list: {
					... state.list,
					shops: [
						...state.list.shops.slice(0, action.shopIndex),
					   	{
					   		...state.list.shops.slice(action.shopIndex, action.shopIndex + 1)[0],
					   		items: shopItemData(state.list.shops[action.shopIndex].items, action)
					   	},
						...state.list.shops.slice(action.shopIndex + 1)
					]
				}
			};
		case PREFIX + 'GETT_TOTAL_WHEN_CHECKED':
			return {
				...state,
				... action.data
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

export default cartReducer;