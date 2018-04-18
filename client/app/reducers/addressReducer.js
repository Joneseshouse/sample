import Tools from 'helpers/Tools';

function addressReducer(state = {}, action){
	const PREFIX = 'ADDRESS' + '_';
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
		case PREFIX + 'LIST_AREA_CODE':
			return {
				...state,
				listAreaCode: [...action.data.list]
			};
		case PREFIX + 'DEFAULT_AREA_CODE':
			return {
				...state,
				defaultAreaCode: action.data
			};
		default:
			return state;
	}
}

export default addressReducer;