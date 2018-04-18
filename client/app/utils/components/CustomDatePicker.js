import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';
import { SingleDatePicker } from 'react-dates';
import 'react-dates/lib/css/_datepicker.css';

export default class CustomDatePicker extends React.Component {
	static propTypes = {
		onChange: PropTypes.func.isRequired
	};
	static defaultProps = {
	};

	constructor(props) {
		super(props);
		this.state = {
			focused: null,
			date: moment()
		}
		this.onDateChange = this.onDateChange.bind(this);
	}

	onDateChange(date){
		const dateValue = date?date.format('YYYY/MM/DD'):null;
		this.setState({ date });
		this.props.onChange(dateValue);
	}

	render(){
		return (
			<SingleDatePicker
				date={this.props.value?moment(this.props.value):null} // momentPropTypes.momentObj or null
				onDateChange={this.onDateChange} // PropTypes.func.isRequired
				focused={this.state.focused} // PropTypes.bool
				onFocusChange={({ focused }) => this.setState({ focused })} // PropTypes.func.isRequired
				isOutsideRange={day => false}
				showClearDate={true}
				numberOfMonths={1}
				displayFormat="DD/MM/YYYY"
				placeholder="Chọn ngày..."
			/>
		);
	}
}
