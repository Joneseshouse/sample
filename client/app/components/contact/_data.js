import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'contact',
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
		title: 'Quản lý contact'
	},
    mainForm: {
        company: {
            title: 'Tên cty',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        address: {
            title: 'Địa chỉ',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        phone: {
            title: 'Số đt',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        email: {
            title: 'Email',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        website: {
            title: 'Website',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
