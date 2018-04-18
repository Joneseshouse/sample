import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'banner',
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
		title: 'Quản lý banner'
	},
    mainForm: {
        title: {
            title: 'Tên banner',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true,
                min: 3
            }
        }, subtitle: {
            title: 'Mô tả banner',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {}
        }, url: {
            title: 'Link liên kết',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {}
        }, image: {
            title: 'Ảnh banner',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
