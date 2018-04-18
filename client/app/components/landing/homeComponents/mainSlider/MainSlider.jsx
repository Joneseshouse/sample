import React from 'react';
import Slider from 'react-slick';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import {STATIC_URL} from 'app/constants';
import MainMenu from '../mainMenu/MainMenu';

class MainSlider extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			mobileMode: false
	    };
	    this.mediaQueryChanged = this.mediaQueryChanged.bind(this);
	}

	componentDidMount(){
		const mql = window.matchMedia(`(max-width: 768px)`);
	    mql.addListener(this.mediaQueryChanged);
	    this.setState({mql: mql});
	    this.setState({mobileMode: mql.matches});
	}

	mediaQueryChanged() {
	    this.setState({mobileMode: this.state.mql.matches});
	}

	componentWillUnmount() {
		this.state.mql.removeListener(this.mediaQueryChanged);
	}

	render() {
		if(this.state.mobileMode) return null;
		const settings = {
			dots: false,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 5000,
			slidesToShow: 1,
			slidesToScroll: 1
		};
		return (
			<div>
				<Slider {...settings}>
					<div>
						<img src={STATIC_URL + 'images/sample/sample-banner.jpg'} width="100%"/>
					</div>
					<div>
						<img src={STATIC_URL + 'images/sample/sample-banner.jpg'} width="100%"/>
					</div>
					<div>
						<img src={STATIC_URL + 'images/sample/sample-banner.jpg'} width="100%"/>
					</div>
				</Slider>
				<MainMenu/>
			</div>
		);
	}
}

export default MainSlider
