function spinner(state = {}, action){
	switch(action.type){
		case 'TOGGLE_SPINNER':
			return {
				...state, show: action.show
			};
		default:
			return state;
	}
}

export default spinner;