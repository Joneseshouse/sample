import Tools from 'helpers/Tools';
import {FIELD_TYPE} from 'app/constants';

const rawApiUrls = [
    {
        controller: 'order',
        endpoints: {
            addFull: 'POST',
            uploadCart: 'POST'
        }
    }, {
        controller: 'config',
        endpoints: {
            rate: 'get'
        }
    }, {
        controller: 'cartItem',
        endpoints: {
            list: 'GET',
            obj: 'GET',
            add: 'POST',
            edit: 'POST',
            remove: 'POST'
        }
    }
];

export const labels = {
	common: {
		title: 'Quản lý cart'
	},
    mainForm: {
        quantity: {
            title: 'Số lượng',
            type: FIELD_TYPE.INTEGER,
            heading: true,
            init: 0,
            rules: {
                required: true
            }
        },
        properties: {
            title: 'Thuộc tính',
            type: FIELD_TYPE.STRING,
            heading: true,
            init: 0,
            rules: {
            }
        },
        message: {
            title: 'Ghi chú',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
            }
        }
    },
    manualForm: {
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
            title: 'Ảnh sản phẩm',
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
    },
    createOrderForm: {
        title: {
            title: 'Tên đơn hàng',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true,
                min: 3
            }
        },
        payment_method: {
            payment_method: 'Hình thức thanh toán',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: 'direct',
            rules: {
            }
        }
    },
    filterForm: {
        link: {
            title: 'Link',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        shop: {
            title: 'Shop',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        },
        date_range: {
            title: 'Ngày',
            type: FIELD_TYPE.STRING,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
