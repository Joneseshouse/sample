import Tools from 'helpers/Tools';

export default function exportBillDailyReducer(state = {}, action){
	const PREFIX = 'EXPORT_BILL_DAILY' + '_';
	switch(action.type){
		case PREFIX + 'OBJ':
			return {
				...state,
				obj: {...state.obj, ...action.data}
			};
		case PREFIX + 'NEW_LIST':
			return {
				...state,
				list: [...action.data.list],
				pages: action.data.pages
			};
		default:
			return state;
	}
}
