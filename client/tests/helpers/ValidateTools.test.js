import assert from 'assert';
import forEach from 'lodash/forEach';
import isEmpty from 'lodash/isEmpty';

import { FIELD_TYPE } from 'app/constants';
import { VALIDATE_LABELS } from 'utils/labels';

import ValidateTools from 'utils/helpers/ValidateTools';

const data = {
	string_field: 'hello',
	email_field: 'tbson87@gmail.com',
	integer_field: 10,
	float_field: 1.1
};

const rules = {
	string_field: {
		type: FIELD_TYPE.STRING,
		required: true,
		min: 3,
		max: 20
	},email_field: {
		type: FIELD_TYPE.EMAIL,
		required: true,
		min: 7,
		max: 20
	},integer_field: {
		type: FIELD_TYPE.INTEGER,
		required: true,
		min: 0,
		max: 20
	},float_field: {
		type: FIELD_TYPE.FLOAT,
		required: true,
		min: 0,
		max: 20
	}
};

const expected_output = {
	string_field: {
		value: 'hello',
		type: FIELD_TYPE.STRING,
		required: true,
		min: 3,
		max: 20
	},email_field: {
		value: 'tbson87@gmail.com',
		type: FIELD_TYPE.EMAIL,
		required: true,
		min: 7,
		max: 20
	},integer_field: {
		value: 10,
		type: FIELD_TYPE.INTEGER,
		required: true,
		min: 0,
		max: 20
	},float_field: {
		value: 1.1,
		type: FIELD_TYPE.FLOAT,
		required: true,
		min: 0,
		max: 20
	}
};

describe('ValidateTools', () => {
	it('mergeDataAndRules', () => {
		const output = ValidateTools.mergeDataAndRules(data, rules);
		forEach(output, (output_item, key) => {
			const expected_output_item = expected_output[key];
			assert.equal(output_item.value, expected_output_item.value);
			assert.equal(output_item.type, expected_output_item.type);
			assert.equal(output_item.required, expected_output_item.required);
			assert.equal(output_item.min, expected_output_item.min);
			assert.equal(output_item.max, expected_output_item.max);
		});
	});

	it('validateInput', () => {
		let errors = ValidateTools.validateInput(data, rules);
		assert.equal(isEmpty(errors), true);

		// STRING
		const missingString = {
			string_field: '',
			email_field: 'tbson87@gmail.com',
			integer_field: 10,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(missingString, rules);
		assert.equal(errors.string_field, VALIDATE_LABELS.REQUIRED);

		const shortString = {
			string_field: 'aa',
			email_field: 'tbson87@gmail.com',
			integer_field: 10,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(shortString, rules);
		assert.equal(errors.string_field, VALIDATE_LABELS.TOO_SHORT);

		const longString = {
			string_field: 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
			email_field: 'tbson87@gmail.com',
			integer_field: 10,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(longString, rules);
		assert.equal(errors.string_field, VALIDATE_LABELS.TOO_LONG);

		// EMAIL
		const missingEmail = {
			string_field: 'hello',
			email_field: '',
			integer_field: 10,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(missingEmail, rules);
		assert.equal(errors.email_field, VALIDATE_LABELS.REQUIRED);

		const invalidEmail = {
			string_field: 'hello',
			email_field: 'abcde',
			integer_field: 10,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(invalidEmail, rules);
		assert.equal(errors.email_field, VALIDATE_LABELS.INVALID_EMAIL);

		const shortEmail = {
			string_field: 'hello',
			email_field: 'a@b.co',
			integer_field: 10,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(shortEmail, rules);
		assert.equal(errors.email_field, VALIDATE_LABELS.TOO_SHORT);

		const longEmail = {
			string_field: 'hello',
			email_field: 'aaaaaaaaaaaaaaaaaaaaaaaaaaaa@b.co',
			integer_field: 10,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(longEmail, rules);
		assert.equal(errors.email_field, VALIDATE_LABELS.TOO_LONG);

		// INTEGER
		const missingInteger = {
			string_field: 'hello',
			email_field: 'tbson87@gmail.com',
			integer_field: null,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(missingInteger, rules);
		assert.equal(errors.integer_field, VALIDATE_LABELS.REQUIRED);

		const zeroInteger = {
			string_field: 'hello',
			email_field: 'tbson87@gmail.com',
			integer_field: 0,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(zeroInteger, rules);
		assert.equal(isEmpty(errors), true);

		const shortInteger = {
			string_field: 'hello',
			email_field: 'tbson87@gmail.com',
			integer_field: -1,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(shortInteger, rules);
		assert.equal(errors.integer_field, VALIDATE_LABELS.TOO_SMALL);

		const longInteger = {
			string_field: 'hello',
			email_field: 'tbson87@gmail.com',
			integer_field: 999,
			float_field: 1.1
		};
		errors = ValidateTools.validateInput(longInteger, rules);
		assert.equal(errors.integer_field, VALIDATE_LABELS.TOO_BIG);

		// FLOAT
		const missingFloat = {
			string_field: 'hello',
			email_field: 'tbson87@gmail.com',
			integer_field: 5,
			float_field: null
		};
		errors = ValidateTools.validateInput(missingFloat, rules);
		assert.equal(errors.float_field, VALIDATE_LABELS.REQUIRED);

		const zeroFloat = {
			string_field: 'hello',
			email_field: 'tbson87@gmail.com',
			integer_field: 5,
			float_field: 0
		};
		errors = ValidateTools.validateInput(zeroFloat, rules);
		assert.equal(isEmpty(errors), true);

		const shortFloat = {
			string_field: 'hello',
			email_field: 'tbson87@gmail.com',
			integer_field: 5,
			float_field: -1
		};
		errors = ValidateTools.validateInput(shortFloat, rules);
		assert.equal(errors.float_field, VALIDATE_LABELS.TOO_SMALL);

		const longFloat = {
			string_field: 'hello',
			email_field: 'tbson87@gmail.com',
			integer_field: 5,
			float_field: 999999999
		};
		errors = ValidateTools.validateInput(longFloat, rules);
		assert.equal(errors.float_field, VALIDATE_LABELS.TOO_BIG);
	});
});