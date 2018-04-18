import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'role',
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
		title: 'Quản lý role'
	},
    mainForm: {
        title: {
            title: 'Tên role',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                min: 3
            }
        }, default_role: {
            title: 'Mặc định',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: false,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
