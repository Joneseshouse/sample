import forEach from 'lodash/forEach';
import { FIELD_TYPE } from 'app/constants';
import { VALIDATE_LABELS } from 'utils/labels';

export default class ValidateTools {

	static mergeDataAndRules(data, rules){
		let result = {};
		try{
			forEach(rules, (ruleValue, ruleKey) => {
				if(typeof data[ruleKey] !== 'undefined'){
					result[ruleKey]	= {...ruleValue, value: data[ruleKey]};
				}else{
					result[ruleKey]	= {...ruleValue, value: null};
				}
			});
		}catch(error){
			console.error(error);
		}
		return result;
	}

	static validateInput(data, rules){
		let errors = {};
		try{
			let mergeData = this.mergeDataAndRules(data, rules);
			forEach(mergeData, (formValue, key) => {
				let value = formValue.value;
				switch(formValue.type){
					case FIELD_TYPE.STRING:
						if((typeof formValue.required !== 'undefined' || formValue.required === true) && (typeof value === 'undefined' || !value)){
							errors[key] = VALIDATE_LABELS.REQUIRED;
						}else if(typeof formValue.min !== 'undefined' && value.length < parseInt(formValue.min)){
							errors[key] = VALIDATE_LABELS.TOO_SHORT;
						}else if(typeof formValue.max !== 'undefined' && value.length > parseInt(formValue.max)){
							errors[key] = VALIDATE_LABELS.TOO_LONG;
						}
					break;
					case FIELD_TYPE.EMAIL:
						if((typeof formValue.required !== 'undefined' || formValue.required === true) && (typeof value === 'undefined' || !value)){
							errors[key] = VALIDATE_LABELS.REQUIRED;
						}else if(!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(value)){
							errors[key] = VALIDATE_LABELS.INVALID_EMAIL;
						}else if(typeof formValue.min !== 'undefined' && value.length < parseInt(formValue.min)){
							errors[key] = VALIDATE_LABELS.TOO_SHORT;
						}else if(typeof formValue.max !== 'undefined' && value.length > parseInt(formValue.max)){
							errors[key] = VALIDATE_LABELS.TOO_LONG;
						}
					break;
					case FIELD_TYPE.INTEGER:
						if((typeof formValue.required !== 'undefined' || formValue.required === true) && (typeof value === 'undefined' || value === null)){
							errors[key] = VALIDATE_LABELS.REQUIRED;
						}else if(typeof formValue.min !== 'undefined' && value < parseInt(formValue.min)){
							errors[key] = VALIDATE_LABELS.TOO_SMALL;
						}else if(typeof formValue.max !== 'undefined' && value > parseInt(formValue.max)){
							errors[key] = VALIDATE_LABELS.TOO_BIG;
						}
					break;
					case FIELD_TYPE.FLOAT:
						if((typeof formValue.required !== 'undefined' || formValue.required === true) && (typeof value === 'undefined' || value === null)){
							errors[key] = VALIDATE_LABELS.REQUIRED;
						}else if(typeof formValue.min !== 'undefined' && value < parseFloat(formValue.min)){
							errors[key] = VALIDATE_LABELS.TOO_SMALL;
						}else if(typeof formValue.max !== 'undefined' && value > parseFloat(formValue.max)){
							errors[key] = VALIDATE_LABELS.TOO_BIG;
						}
					break;
					default:
						//Do nothing
				}
			});
		}catch(error){
			console.error(error);
		}
		return errors
	}
}
