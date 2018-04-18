import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'bolDaily',
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
		title: 'Quản lý bolDaily'
	},
    mainForm: {
        report_date: {
            title: 'Ngày',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        number_of_bols: {
            title: 'Tổng đơn',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
            }
        },
        order_bols: {
            title: 'Đơn order',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            link: {
                location: 'bill_of_landing',
                params: ['2', 'order', '__report_date__']
            },
            rules: {
            }
        },
        deposit_bols: {
            title: 'Đơn v.chuyển',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            link: {
                location: 'bill_of_landing',
                params: ['2', 'deposit', '__report_date__']
            },
            rules: {
            }
        },
        missing_bols: {
            title: 'Đơn thiếu t.tin',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            link: {
                location: 'bill_of_landing',
                params: ['2', 'missing', '__report_date__']
            },
            rules: {
            }
        },
        mass: {
            title: 'Khối lượng',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: 0,
            rules: {
            }
        },
        total: {
            title: 'Tổng cộng',
            type: FIELD_TYPE.INTEGER,
            prefix: '₫',
            heading: true,
            init: 0,
            rules: {
            }
        },
        last_updated: {
            title: 'Ngày cập nhật',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        }
    },
    filterForm: {
        date_range: {
            title: 'Ngày tạo',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        last_updated: {
            title: 'Ngày cập nhật',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        bol: {
            title: 'Vận đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
