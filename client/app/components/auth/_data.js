import Tools from 'helpers/Tools';
import {FIELD_TYPE, APP} from 'app/constants';

const rawApiUrls = [
    {
        controller: APP,
        endpoints: {
            obj: 'GET',
            authenticate: 'POST',
            logout: 'POST',
            profile: 'GET',
            updateProfile: 'POST',
            resetPassword: 'POST',
            resetPasswordConfirm: 'GET',
            changePassword: 'POST',
            changePasswordConfirm: 'GET'
        }
    }
];

export const labels = {
    login: {
        email: {
            title: 'Email / username',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }, password: {
            title: 'Password',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }
    },resetPassword: {
        email: {
            title: 'Your email',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }, password: {
            title: 'Your new password',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }, newPassword: {
            title: 'Retype your new password',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }
    }, changePassword: {
        password: {
            title: 'Your new password',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }, newPassword: {
            title: 'Retype your new password',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        }
    }, profile: {
        email: {
            title: 'Your email',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        },first_name: {
            title: 'Firt name',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        },last_name: {
            title: 'Last name',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
                required: true
            }
        },company: {
            title: 'Company',
            type: FIELD_TYPE.STRING,
            heading: false,
            init: null,
            rules: {
            }
        }
    }
};

export const apiUrls = Tools.getApiUrlsV1(rawApiUrls);
