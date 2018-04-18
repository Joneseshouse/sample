import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'shop',
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
		title: 'Quản lý shop'
	},
    mainForm: {
        uid: {
            title: 'Mã shop',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                min: 3
            }
        },
        title: {
            title: 'Tên shop',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                min: 3
            }
        },
        vendor: {
            title: 'Vendor',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                min: 3
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
