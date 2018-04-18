import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';
import store from 'app/store';

const rawApiUrls = [
    {
        controller: 'admin',
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
		title: 'Quản lý admin'
	},
    mainForm: {
        first_name: {
            title: 'Tên',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }, last_name: {
            title: 'Họ',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }, email: {
            title: 'Email',
            type: FIELD_TYPE.EMAIL,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }, role_id: {
            title: 'Quyền',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            mapLabels: 'listRole',
            rules: {
                required: true
            }
        }, block_account: {
            title: 'Khóa tài khoản',
            type: FIELD_TYPE.BOOLEAN,
            heading: false,
            init: null,
            rules: {
                required: false
            }
        }, password: {
            title: 'Mật khẩu',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
