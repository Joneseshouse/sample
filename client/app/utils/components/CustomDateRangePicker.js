import React from 'react';
import PropTypes from 'prop-types';
import moment from 'moment';
import { DateRangePicker } from 'react-dates';
import 'react-dates/lib/css/_datepicker.css';

export default class CustomDateRangePicker extends React.Component {
	static propTypes = {
		onChange: PropTypes.func.isRequired
	};
	static defaultProps = {
	};

	constructor(props) {
		super(props);
		this.state = {
			focusedInput: null,
			startDate: null,
			endDate: null
		}
	}

	render(){
		return (
			<DateRangePicker
					startDate={this.state.startDate}
					endDate={this.state.endDate}
					onDatesChange={({ startDate, endDate }) => {
						if(!startDate && !endDate){
							this.setState({ startDate, endDate })
							this.props.onChange([startDate, endDate]);
						}else{
							const startDateFormat = startDate?startDate.format('YYYY/MM/DD'):null;
							if(endDate){
								const endDateFormat = endDate?endDate.format('YYYY/MM/DD'):null;
								this.setState({ startDate, endDate })
								this.props.onChange([startDateFormat, endDateFormat]);
							}else{
								this.setState({ startDate, endDate: startDate });
								this.props.onChange([startDateFormat, startDateFormat]);
							}
						}
					}}
					numberOfMonths={1}
					focusedInput={this.state.focusedInput}
					onFocusChange={focusedInput => this.setState({ focusedInput })}
					startDatePlaceholderText="Từ ngày"
					endDatePlaceholderText="Đến ngày"
					monthFormat="MM / YYYY"
					displayFormat="DD/MM/YYYY"
					showClearDates={true}
					isOutsideRange={day => false}
					isDayHighlighted={day => (day.isSame(moment(), 'day')?true:false)}
				/>
		);
	}
}
