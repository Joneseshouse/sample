import Tools from 'helpers/Tools';

function orderDetailReducer(state = {}, action){
	const PREFIX = 'ORDER_DETAIL' + '_';
	switch(action.type){
		case PREFIX + 'OBJ':
			return {
				...state,
				obj: {...state.obj, ...action.data}
			};
		case PREFIX + 'OBJ_ITEM':
			return {
				...state,
				objItem: {...state.objItem, ...action.data}
			};
		case PREFIX + 'OBJ_PURCHASE':
			return {
				...state,
				objPurchase: {...state.objPurchase, ...action.data}
			};
		case PREFIX + 'OBJ_BILL_OF_LANDING':
			return {
				...state,
				objBillOfLanding: {...state.objBillOfLanding, ...action.data}
			};
		case PREFIX + 'OBJ_CHECK_ITEM_STATUS':
			let result = {};
			for(let i in action.data){
				if(action.data[i].id){
					result[action.data[i].id] = action.data[i].title;
				}else{
					result[action.data[i].id] = "";
				}
			}
			return {
				...state,
				objCheckItemStatus: {...result}
			};
		case PREFIX + 'SELECTED_SHOP':
			return {
				...state,
				selectedShop: action.data
			};
		case PREFIX + 'NEW_LIST':
			return {
				...state,
				list: [...action.data.list],
				pages: action.data.pages
			};
		case PREFIX + 'APPEND_LIST':
			return {
				...state,
				list: [...state.list, ...action.data]
			};
		case PREFIX + 'LIST_BILL_OF_LANDING':
			return {
				...state,
				listBillOfLanding: [...action.data.list]
			};
		case PREFIX + 'LIST_ADDRESS':
			return {
				...state,
				listAddress: [...action.data.list]
			};
		case PREFIX + 'LIST_ADMIN':
			return {
				...state,
				listAdmin: [...action.data.list]
			};
		case PREFIX + 'DEFAULT_ADMIN':
			return {
				...state,
				defaultAdmin: action.data
			};
		case PREFIX + 'LIST_NOTE':
			return {
				...state,
				listNote: [...action.data.list],
				orderItemId: action.data.orderItemId
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
        case PREFIX + 'CHECK': {
            let data = action.data;
            let localState = {...state};

            let purchaseIndex = localState.list.findIndex(x => x.id === data.purchaseId);
            let index = localState.list[purchaseIndex].order_items.findIndex(x => x.id === data.id);
            
            localState.list[purchaseIndex].order_items[index].checked = data.checked;
            return localState;
        }
        case PREFIX + 'CHECK_PURCHASE': {
            var checked = true;
            let data = action.data;
            let localState = {...state};

            let purchaseIndex = localState.list.findIndex(x => x.id === data.purchaseId);
            var orderItems = localState.list[purchaseIndex].order_items;
            var checkedNum = orderItems.filter(item => item.checked).length;

            if (checkedNum === orderItems.length) {
                checked = false; 
            }

            for (let item of orderItems) {
                item.checked = checked;
            }
            return localState;
        }
		case PREFIX + 'CHECK_ALL':
			return {
				...state,
				list: state.list.map(value => {
					return {...value, checked: true};
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

export default orderDetailReducer;
