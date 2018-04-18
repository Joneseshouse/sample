import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'billOfLanding',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            add: 'POST',
            edit: 'POST',
            resetComplain: 'POST',
            editComplain: 'POST',
            remove: 'POST'
        }
    }
];


export const labels = {
	common: {
		title: 'Quản lý vận đơn vận chuyển'
	},
    mainForm: {
        created_at: {
            title: 'Ngày tạo',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        check_staff_fullname: {
            title: 'NV kiểm',
            type: FIELD_TYPE.STRING,
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
        purchase_code: {
            title: 'Mã g.dịch',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        address_code: {
            title: 'Mã địa chỉ',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        landing_status: {
            title: 'T.Thái',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: 'Mới',
            rules: {
            }
        },
        transform_factor: {
            title: 'Hệ số quy đổi',
            type: FIELD_TYPE.INTEGER,
            init: 6000,
            rules: {
                required: true
            }
        },
        wooden_box: {
            title: 'Đ.Gỗ',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: false,
            rules: {
            }
        },
        straight_delivery: {
            title: 'C.Thẳng',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: false,
            rules: {
            }
        },
        insurance_register: {
            title: 'B.Hiểm',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: null,
            rules: {
            }
        },
        checked_date: {
            title: 'Kiểm',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: null,
            rules: {
            }
        },
        address_id: {
            title: 'Địa chỉ',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
                required: true
            }
        },
        length: {
            title: 'Dài',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        width: {
            title: 'Rộng',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        height: {
            title: 'Cao',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        input_mass: {
            title: 'K.lượng',
            type: FIELD_TYPE.FLOAT,
            'suffix': 'Kg',
            init: 0,
            rules: {
            }
        },
        mass: {
            title: 'K.lượng',
            type: FIELD_TYPE.FLOAT,
            'suffix': 'Kg',
            heading: true,
            init: 0,
            rules: {
            }
        },
        packages: {
            title: 'Số kiện',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 1,
            rules: {
                required: true
            }
        },
        delivery_fee: {
            title: 'Phí V.Chuyển',
            type: FIELD_TYPE.FLOAT,
            prefix: '₫',
            heading: true,
            init: 0,
            rules: {
            }
        },
        inland_delivery_fee_raw: {
            title: 'V.Chuyển N.Đ',
            type: FIELD_TYPE.FLOAT,
            prefix: '₫',
            heading: true,
            init: 0,
            rules: {
            }
        },
        insurance_fee: {
            title: 'Phí B.Hiểm',
            type: FIELD_TYPE.FLOAT,
            prefix: '₫',
            heading: true,
            init: 0,
            rules: {
            }
        },
        sub_fee: {
            title: 'Phụ phí',
            type: FIELD_TYPE.FLOAT,
            prefix: '₫',
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
        insurance_value: {
            title: 'Giá trị thực',
            type: FIELD_TYPE.FLOAT,
            init: 0,
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
    },
    complainForm: {
        complain_amount: {
            title: 'Thoả thuận chiết khấu',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
            }
        },
        complain_type: {
            title: 'Mục đích khiếu nại',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: 'change',
            rules: {
            }
        },
        complain_resolve: {
            title: 'Đã được giải quyết',
            type: FIELD_TYPE.BOOLEAN,
            heading: true,
            init: false,
            rules: {
            }
        },
        complain_note_user: {
            title: 'Khách hàng ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        complain_note_admin: {
            title: 'Admin ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
