import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'collectBol',
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
		title: 'Quản lý collectBol'
	},
    mainForm: {
        updated_at: {
            title: 'Ngày',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        admin_full_name: {
            title: 'Nhân viên',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        purchase_code: {
            title: 'Mã giao dịch',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        bill_of_landing_code: {
            title: 'Mã vận đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        real_amount: {
            title: 'Thanh toán thực',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            prefix: '¥',
            init: null,
            rules: {
            }
        },
        match: {
            title: 'Khớp',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: false,
            rules: {
            }
        },
        note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
