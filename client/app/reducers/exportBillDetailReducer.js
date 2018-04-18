import Tools from 'helpers/Tools';
import findIndex from 'lodash/findIndex';

export default function exportBillDetailReducer(state = {}, action){
	const PREFIX = 'EXPORT_BILL_DETAIL' + '_';
	switch(action.type){
		case PREFIX + 'OBJ':
			return {
				...state,
				obj: {...state.obj, ...action.data}
			};
		case PREFIX + 'SELECT':
			let index = findIndex(state.listSelected, {id: action.data.id});
			if(index !== -1){
				// Alrealy exist
				return state;
			}
			return {
				...state,
				listSelected: [{...action.data}, ...state.listSelected]
			};
		case PREFIX + 'KEYWORD':
			return {
				...state,
				keyword: action.data
			};
		case PREFIX + 'ADDRESS_UID':
			return {
				...state,
				address_uid: action.data
			};
		case PREFIX + 'LIST_PURE':
			return {
				...state,
				listPure: [...action.data.list]
			};
		case PREFIX + 'LIST_FAIL':
			return {
				...state,
				listFail: [...action.data.list]
			};
		case PREFIX + 'ADD_FAIL':
			return {
				...state,
				listFail: [{...action.data}, ...state.listFail]
			};
		case PREFIX + 'EDIT_FAIL':
			return {
				...state,
				listFail: [
					...state.listFail.slice(0, action.index),
					{...state.listFail.slice(action.index, action.index + 1)[0], ...action.data},
					...state.listFail.slice(action.index + 1)
				]
			};
		case PREFIX + 'REMOVE_FAIL':
			return {
				...state,
				listFail: Tools.ignoreIndex(state.listFail, action.listIndex)
			};
		case PREFIX + 'LIST_SELECTED':
			return {
				...state,
				listSelected: [...action.data.list]
			};
		case PREFIX + 'REMOVE_ITEM':
			return {
				...state,
				listSelected: Tools.ignoreIndex(state.listSelected, action.listIndex)
			};
		default:
			return state;
	}
}
