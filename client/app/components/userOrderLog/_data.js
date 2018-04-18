import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'userOrderLog',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST'
        }
    }
];


export const labels = {
	common: {
		title: 'Quản lý userOrderLog'
	},
    mainForm: {
        uid: {
            title: 'Tên userOrderLog',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                min: 3
            }
        }, value: {
            title: 'Giá trị',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
