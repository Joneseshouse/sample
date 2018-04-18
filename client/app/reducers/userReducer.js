import Tools from 'helpers/Tools';

function userReducer(state = {}, action){
	const PREFIX = 'USER' + '_';
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
		case PREFIX + 'LIST_ADMIN':
			return {
				...state,
				listAdmin: [...action.data.list]
			};
		case PREFIX + 'LIST_DATHANG_ADMIN':
			return {
				...state,
				listDathangAdmin: [...action.data.list]
			};
		case PREFIX + 'DEFAULT_ADMIN':
			return {
				...state,
				defaultAdmin: action.data
			};
		case PREFIX + 'LIST_USER':
			return {
				...state,
				listUser: [...action.data.list]
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
		case PREFIX + 'SET_LIST_REGION':
			return {
				...state,
				listRegion: [...action.data.listRegion]
			};
		case PREFIX + 'SET_DEFAULT_REGION':
			return {
				...state,
				defaultRegion: action.data
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

export default userReducer;