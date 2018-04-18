function wrapperReducer(state = {}, action){
	switch(action.type){
		case 'WRAPPER_UPDATE':
			return {
				...state, firstUpdate: false
			};
		default:
			return state;
	}
}

export default wrapperReducer;