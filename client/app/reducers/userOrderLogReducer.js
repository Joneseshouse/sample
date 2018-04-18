import Tools from 'helpers/Tools';

function userOrderLogReducer(state = {}, action){
	const PREFIX = 'USER_ORDER_LOG' + '_';
	switch(action.type){
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

export default userOrderLogReducer;