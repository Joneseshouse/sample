import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'roleType',
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
		title: 'Quản lý loại role'
	},
    mainForm: {
        title: {
            title: 'Tên loại role',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            link: {
                location: 'role',
                params: ['__id__']
            },
            rules: {
                required: true,
                min: 3
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
