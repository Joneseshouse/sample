import React from 'react';
import './styles.styl';


class MainMenu extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
	    };
	}

	render() {
		return (
			<div className="main-menu">
				<div className="row">
					<div className="col-sm-10 col-sm-offset-1">
						<ul>
							<li>
								<a className="active" href="#home">
									Trang chủ
								</a>
							</li>
							<li>
								<a href="#news">
									Giới thiệu
								</a>
							</li>
							<li>
								<a href="#contact">
									Sản phẩm
								</a>
							</li>
							<li>
								<a href="#about">
									Chia sẻ
								</a>
							</li>
							<li>
								<a href="#about">
									Liên hệ
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		);
	}
}

export default MainMenu
