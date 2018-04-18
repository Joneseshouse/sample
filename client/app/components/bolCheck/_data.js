import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'purchase',
        endpoints: {
            checkList: 'GET'
        }
    }
];


export const labels = {
	common: {
		title: 'Quản lý bolCheck'
	},
    mainForm: {
        order_created_at: {
            title: 'Ngày tạo đơn',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        admin_fullname: {
            title: 'N.V đặt hàng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        user_fullname: {
            title: 'K.hàng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        order_uid: {
            title: 'Mã đơn hàng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        code: {
            title: 'Mã giao dịch',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        bols: {
            title: 'Vận đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }

    },
    filterForm: {
        date_range: {
            title: '',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        },
        order_uid: {
            title: '',
            placeholder: 'Mã đơn hàng',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        purchase_code: {
            title: '',
            placeholder: 'Mã giao dịch',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        bol_code: {
            title: '',
            placeholder: 'Vận đơn',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
