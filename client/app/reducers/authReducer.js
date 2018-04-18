function authReducer(state = {}, action){
	switch(action.type){
		case 'AUTH_UPDATE_PROFILE':
			return {
				...state,
				profile: {...state.profile, ...action.data}
			};
		case 'AUTH_LOGOUT':
			return action.data;
		default:
			return state;
	}
}

/*
function login(state = {}, action){

}
*/

export default authReducer;