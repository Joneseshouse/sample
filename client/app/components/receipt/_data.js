import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'receipt',
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
		title: 'Quản lý receipt'
	},
    mainForm: {
        created_at: {
            title: 'Ngày lập',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        uid: {
            title: 'Mã phiếu',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        admin_fullname: {
            title: 'Nhân viên',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        user_fullname: {
            title: 'Khách hàng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        user_id: {
            title: 'Khách hàng',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
                required: true
            }
        },
        amount: {
            title: 'Số tiền',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            prefix: '₫',
            init: null,
            rules: {
                required: true
            }
        },
        note: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        }
    },
    filterForm: {
        uid: {
            title: 'Mã phiếu',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        admin_id: {
            title: 'Nhân viên',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        user_id: {
            title: 'Khách hàng',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        type: {
            title: 'Hành động',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: '',
            mapLabels: 'listType',
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
        },
        from_amount: {
            title: 'Giá trị từ',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        to_amount: {
            title: 'đến',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        date_range: {
            title: 'Khoảng ngày',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        },
        money_type: {
            title: 'Loại tiền',
            type: FIELD_TYPE.STRING,
            init: '',
            mapLabels: 'listMoneyType',
            rules: {
            }
        },
        have_transaction: {
            title: 'Giao dịch',
            type: FIELD_TYPE.STRING,
            init: '',
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
