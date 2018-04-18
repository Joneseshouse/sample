import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'userAccounting',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST'
        }
    },
    {
        controller: 'receipt',
        endpoints: {
            obj: 'GET'
        }
    }
];


export const labels = {
	common: {
		title: 'Quản lý kế toán khách hàng'
	},
    mainForm: {
        updated_at: {
            title: 'Ngày tạo / sửa',
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
        admin_id: {
            title: 'Nhân viên',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
                required: true
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
        type: {
            title: 'Hành động',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: '',
            mapLabels: 'listType',
            rules: {
                required: true
            }
        },
        money_type: {
            title: 'Loại tiền',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: '',
            mapLabels: 'listMoneyType',
            rules: {
                required: true
            }
        },
        income: {
            title: 'Ghi có',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        expense: {
            title: 'Ghi nợ',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        purchasing: {
            title: 'Đang giao dịch',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            prefix: '₫',
            init: 0,
            rules: {
            }
        },
        amount: {
            title: 'Số tiền',
            type: FIELD_TYPE.INTEGER,
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
        from_date: {
            title: 'Ngày',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        }
    },
    filterForm: {
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
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
