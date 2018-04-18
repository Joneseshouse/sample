import Tools from 'helpers/Tools';

export default function bolCheckReducer(state = {}, action){
	const PREFIX = 'BOL_CHECK' + '_';
	switch(action.type){
		case PREFIX + 'NEW_LIST':
			return {
				...state,
				list: [...action.data.list],
				listAdmin: [...action.data.listAdmin],
				pages: action.data.pages
			};
		default:
			return state;
	}
}
