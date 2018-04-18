import Tools from 'helpers/Tools';

function bolReportReducer(state = {}, action){
	const PREFIX = 'BOL_REPORT' + '_';
	switch(action.type){
		case PREFIX + 'LIST':
			return {
				...state,
				list: [...action.data.list],
				pages: action.data.pages
			};
		default:
			return state;
	}
}

export default bolReportReducer;