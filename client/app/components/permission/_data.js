import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'permission',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            syncList: 'GET',
            edit: 'POST'
        }
    }
];

export const labels = {
	common: {
		title: 'Quản lý permission'
	},
    mainForm: {
        title: {
            title: 'Tên module',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }, action: {
            title: 'Hành động',
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
