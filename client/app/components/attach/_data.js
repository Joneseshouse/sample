import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const MAIN_CONTROLLER = 'attach';
const rawApiUrls = {
    obj: 'GET',
    list: 'GET',
    add: 'POST',
    edit: 'POST',
    remove: 'POST'
};


export const labels = {
	common: {
		title: 'Quản lý đính kèm'
	},
    mainForm: {
        title: {
            title: 'Tên file',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }
    }
};

export const apiUrls = Tools.getApiUrls(MAIN_CONTROLLER, rawApiUrls);
