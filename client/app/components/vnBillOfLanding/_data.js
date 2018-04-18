import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'vnBillOfLanding',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST',
            upload: 'POST'
        }
    }
];

export const labels = {
	common: {
		title: 'Quản lý vận đơn VN'
	},
    mainForm: {
        created_at: {
            title: 'Ngày',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        code: {
            title: 'Mã vận đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        match: {
            title: 'Khớp',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: null,
            rules: {
            }
        },
        note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        }
    },
    uploadForm: {
        list_code: {
            title: 'Danh sách vận đơn',
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
