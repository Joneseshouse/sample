import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'order',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            filter: 'POST'
        }
    },
    {
        controller: 'user',
        endpoints: {
            obj: 'GET'
        }
    }
];

export const labels = {
	common: {
		title: 'Quản lý đơn hàng'
	},
    mainForm: {
        created_at: {
            title: 'Ngày gửi',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        confirm_date: {
            title: 'Ngày duyệt',
            type: FIELD_TYPE.DATE,
            heading: true,
            init: null,
            rules: {
            }
        },
        uid: {
            title: 'Mã đơn',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        status: {
            title: 'Trạng thái',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            mapLabels: 'listStatus',
            rules: {
                required: true
            }
        },
        customer_care_staff_full_name: {
            title: 'NVCS',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        confirm_full_name: {
            title: 'NV duyệt',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        items: {
            title: 'Số lượng SP',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            rules: {
            }
        },
        s_mass: {
            title: 'K.lượng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        s_amount: {
            title: 'Tiền hàng',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        real_amount: {
            title: 'TT thực',
            type: FIELD_TYPE.FLOAT,
            prefix: '¥',
            heading: Tools.isDatHang || Tools.isKeToan || Tools.isQuanTriVien,
            init: null,
            rules: {
            }
        },
        discount: {
            title: 'C.Khấu',
            type: FIELD_TYPE.FLOAT,
            prefix: '¥',
            heading: Tools.isDatHang || Tools.isKeToan || Tools.isQuanTriVien,
            init: null,
            rules: {
            }
        },
        s_inland_delivery_fee: {
            title: 'Phí ship ND',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        delivery_fee: {
            title: 'Phí vận chuyển',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            prefix: '₫',
            rules: {
            }
        },
        s_order_fee: {
            title: 'Phí DV',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        sub_fee: {
            title: 'Phụ phí',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            prefix: '₫',
            rules: {
            }
        }
    },
    filterForm: {
        dathang_staff: {
            title: 'N.Viên đặt hàng',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        customer_staff: {
            title: 'N.Viên CSKH',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        check_staff: {
            title: 'N.Viên kiểm hàng',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        confirm_staff: {
            title: 'N.Viên duyệt đơn',
            type: FIELD_TYPE.INTEGER,
            init: 0,
            rules: {
            }
        },
        customer_name: {
            title: 'Tên khách hàng',
            type: FIELD_TYPE.STRING,
            init: null,
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
        customer_phone: {
            title: 'Đ.Thoại',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        customer_email: {
            title: 'Email',
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
        created_at: {
            title: 'Ngày gửi đơn',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        },
        updated_at: {
            title: 'Ngày cập nhật',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        },
        confirm_date: {
            title: 'Ngày duyệt đơn',
            type: FIELD_TYPE.DATE,
            init: null,
            rules: {
            }
        },
        bill_of_landing_code: {
            title: 'Mã vận đơn',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        purchase_code: {
            title: 'Mã giao dịch',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        order_uid: {
            title: 'Mã đơn hàng',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        shop_title: {
            title: 'Tên shop',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        order_item_url: {
            title: 'Link sản phẩm',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        from_total: {
            title: 'Giá tổng từ',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        },
        to_total: {
            title: 'đến',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
