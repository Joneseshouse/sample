import React from 'react';
import MainSlider from './homeComponents/mainSlider/MainSlider';
import LandingWrapper from 'utils/components/landing/LandingWrapper';
import WaitingMessage from 'utils/components/WaitingMessage';

class Home extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
	    };
	}

	render() {
		return (
			<LandingWrapper>
				<div>
					<br/>
					<div className="panel panel-primary">
						<div className="panel-heading">
							<h3 className="panel-title">Bảng Báo Giá</h3>
						</div>
						<div className="panel-body">
							<table className="table table-striped">
								<thead>
									<tr>
										<th style={{width: '25px'}}>#</th>
										<th>Sản phẩm</th>
										<th style={{width: '95px'}}>Đơn giá / m<sup>2</sup></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><strong>1</strong></td>
										<td>Sản phẩm 1</td>
										<td>₫200.000</td>
									</tr>
									<tr>
										<td><strong>2</strong></td>
										<td>Sản phẩm 2</td>
										<td>₫200.000</td>
									</tr>
									<tr>
										<td><strong>3</strong></td>
										<td>Sản phẩm 3</td>
										<td>₫200.000</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</LandingWrapper>
		);
	}
}

export default Home
