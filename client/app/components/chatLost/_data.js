import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'chatlost',
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
		title: 'Trò chuyện'
	},
    mainForm: {
        message: {
            title: 'Tin nhắn',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
                required: false
            }
        },
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
