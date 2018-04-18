import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'order',
        endpoints: {
            obj: 'GET',
            list: 'GET',
            addFull: 'POST',
            edit: 'POST',
            remove: 'POST',
            updateDeliveryFeeUnit: 'POST'
        }
    }, {
        controller: 'orderItem',
        endpoints: {
            obj: 'GET',
            add: 'POST',
            edit: 'POST',
            editUnitPrice: 'POST',
            remove: 'POST',
            empty: 'POST'
        }
    }, {
        controller: 'orderItemNote',
        endpoints: {
            list: 'GET',
            obj: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST'
        }
    }, {
        controller: 'purchase',
        endpoints: {
            obj: 'GET',
            edit: 'POST'
        }
    }, {
        controller: 'billOfLanding',
        endpoints: {
            obj: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST'
        }
    }
];

export const labels = {
	common: {
		title: 'Chi tiết đơn hàng'
	},
    mainForm: {
        title: {
            title: 'Tên sản phẩm',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        shop_name: {
            title: 'Tên shop',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        url: {
            title: 'Link sản phẩm',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        avatar: {
            title: 'Link ảnh sản phẩm',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        properties: {
            title: 'Các thuộc tính',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        },
        unit_price: {
            title: 'Đơn giá CNY',
            type: FIELD_TYPE.FLOAT,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        quantity: {
            title: 'Số lượng',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: null,
            rules: {
                required: true
            }
        },
        message: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: null,
            rules: {
            }
        }
    }, unitPriceForm: {
        unit_price: {
            title: 'Đơn giá',
            type: FIELD_TYPE.FLOAT,
            init: 0,
            rules: {
                required: true
            }
        }
    }, realAmountForm: {
        real_amount: {
            title: 'Thanh toán thực',
            type: FIELD_TYPE.FLOAT,
            init: 0,
            rules: {
                required: true
            }
        }
    }, deliveryFeeForm: {
        delivery_fee_unit: {
            title: 'Đơn giá vận chuyển',
            type: FIELD_TYPE.FLOAT,
            init: 0,
            rules: {
                required: true
            }
        },
        inland_delivery_fee_raw: {
            title: 'Phí vận chuyển nội địa',
            type: FIELD_TYPE.FLOAT,
            init: 0,
            rules: {
                required: true
            }
        }
    }, purchaseCodeForm: {
        code: {
            title: 'Mã giao dịch',
            type: FIELD_TYPE.STRING,
            init: '',
            rules: {
            }
        }
    }, purchaseNoteForm: {
        code: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            init: '',
            rules: {
            }
        }
    }, billOfLandingForm: {
        created_at: {
            title: 'Ngày tạo',
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
    }, addressForm: {
        address_id: {
            title: 'Địa chỉ nhận hàng',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
                required: true
            }
        }
    }, statusForm: {
        status: {
            title: 'Trạng thái đơn hàng',
            type: FIELD_TYPE.STRING,
            init: 'new',
            rules: {
                required: true
            }
        }
    }, adminForm: {
        admin_id: {
            title: 'Nhân viên mua hàng',
            type: FIELD_TYPE.INTEGER,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
