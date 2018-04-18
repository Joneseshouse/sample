import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'category',
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
		title: 'Quản lý category'
	},
    mainForm: {
        title: {
            title: 'Tên',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            link: {
                location: '__type__',
                params: ['__id__']
            },
            rules: {
                required: true,
                min: 3
            }
        }, type: {
            title: 'Loại',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: 'article',
            mapLabels: 'listType',
            rules: {
                required: true
            }
        }, single: {
            title: 'Dạng đơn',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: false,
            rules: {}
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
