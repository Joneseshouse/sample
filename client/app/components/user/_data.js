import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';
import store from 'app/store';

const rawApiUrls = [
    {
        controller: 'user',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST',
            signup: 'POST'
        }
    }, {
        controller: 'areaCode',
        endpoints: {
            list: 'GET'
        }
    }
];

export const labels = {
	common: {
		title: 'Quản lý user'
	},
    mainForm: {
        id: {
            title: 'Mã',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            width: 50,
            rules: {
            }
        },
        block_account: {
            title: 'Khóa',
            type: FIELD_TYPE.INVERSE_BOOLEAN,
            heading: true,
            init: null,
            rules: {
                required: false
            }
        },
        admin_id: {
            title: 'Nhân viên chăm sóc',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            mapLabels: 'listAdmin',
            init: null,
            width: 150,
            rules: {
            }
        },
        dathang_admin_id: {
            title: 'Nhân viên đặt hàng',
            type: FIELD_TYPE.INTEGER,
            heading: false,
            init: null,
            rules: {
            }
        },
        full_name: {
            title: 'Họ tên',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            width: 150,
            rules: {
            }
        },
        first_name: {
            title: 'Tên',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            width: 100,
            rules: {
                required: true
            }
        }, last_name: {
            title: 'Họ',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            width: 100,
            rules: {
                required: true
            }
        }, email: {
            title: 'Email',
            type: FIELD_TYPE.EMAIL,
            heading: true,
            init: null,
            width: 200,
            rules: {
                required: true
            }
        },
        uid: {
            title: 'Username',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            width: 100,
            rules: {
            }
        },
        phone: {
            title: 'Số điện thoại',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            width: 100,
            rules: {
                required: true
            }
        }, area_code_id: {
            title: 'Vùng',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            mapLabels: 'listAreaCode',
            width: 150,
            rules: {
                required: true
            }
        }, address: {
            title: 'Địa chỉ',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            width: 220,
            rules: {
                required: true
            }
        }, default_address_code: {
            title: 'Địa chỉ',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            width: 220,
            rules: {
                required: true
            }
        }, company: {
            title: 'Công ty',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            width: 100,
            rules: {
            }
        }, order_fee_factor: {
            title: 'Phí đặt hàng',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: 5,
            width: 100,
            suffix: '%',
            rules: {
                required: true,
                min: 0,
                max: 100
            }
        }, rate: {
            title: 'Tỷ giá',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 3400,
            width: 60,
            rules: {
                required: true
            }
        },
        deposit_factor: {
            title: 'Hệ số cọc',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: 50,
            width: 100,
            suffix: '%',
            rules: {
                required: true,
                min: 0,
                max: 100
            }
        },
        delivery_fee_unit: {
            title: 'Đơn giá V.Chuyển',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            width: 100,
            suffix: '₫',
            rules: {
                min: 0
            }
        },
        complain_day: {
            title: 'Hạn kh.nại',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 2,
            width: 100,
            suffix: 'ngày',
            rules: {
                required: true,
                min: 1,
                max: 10
            }
        }, password: {
            title: 'Mật khẩu',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        }
    },
    filterForm: {
        customer_staff: {
            title: 'N.Viên CSKH',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        customer_id: {
            title: 'Mã khách hàng',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        lock: {
            title: 'Khoá tài khoản',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        care: {
            title: 'Trạng thái chăm sóc',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        debt: {
            title: 'Nợ',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        rate: {
            title: 'Tỷ giá',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        order_fee: {
            title: 'Phí dịch vụ',
            type: FIELD_TYPE.FLOAT,
            init: null,
            rules: {
            }
        },
        delivery_fee: {
            title: 'Phí vận chuyển',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        complain_date: {
            title: 'T.Gian khiếu nại',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        },
        address_uid: {
            title: 'Địa chỉ nhận hàng',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        deposit_factor: {
            title: 'Hệ số cọc',
            type: FIELD_TYPE.FLOAT,
            init: null,
            rules: {
            }
        },
        order_fee_factor: {
            title: 'Phí đặt hàng',
            type: FIELD_TYPE.FLOAT,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
