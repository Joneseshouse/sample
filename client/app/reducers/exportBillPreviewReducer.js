import Tools from 'helpers/Tools';
import findIndex from 'lodash/findIndex';


export default function exportBillPreviewReducer(state = {}, action){
	const PREFIX = 'EXPORT_BILL_PREVIEW' + '_';
	switch(action.type){
		case PREFIX + 'OBJ':
			return {
				...state,
				obj: {...state.obj, ...action.data}
			};
		case PREFIX + 'ADDRESS':
			return {
				...state,
				address: {...action.data}
			};
		case PREFIX + 'LIST_CONTACT':
			return {
				...state,
				list_contact: [...action.data.list]
			};
		default:
			return state;
	}
}
