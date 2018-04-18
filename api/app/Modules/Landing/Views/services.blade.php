@extends('Landing::share.base')
@section('content')

<div class="row gold-bg">
	<div class="col-md-7 col-md-offset-1" style="padding-right: 10px">
		<div class="brown-bg padding-15">
			<p class="white">
				BÁO GIÁ SHIP HÀNG HỘ (Khách hàng tự mua, tự chuyển về kho Quảng Châu)
			</p>

			<p class="white">
				Từ Quảng Châu về Hà Nội: 27.000đ/kg - Trên 60kg, 30.000đ/kg - Dưới 60kg
			</p>
			<p class="white">
				Từ Quảng Châu về Sài Gòn, Đà Nẵng: 37.000đ/kg - Trên 60kg, 40.000đ/kg - Dưới 60kg
			</p>

			<p class="white">
				BÁO GIÁ ORDER (ĐHQC mua, kiểm hàng, vận chuyển về VN)
			</p>

			<div class="row">
				<div class="col-md-3">
					<button class="btn btn-default service-table-button yellow-bg" style="width: 100% !important">
						<span class="round-number">1</span>
						 TIỀN HÀNG TRÊN WEB
					</button>
				</div>

				<div class="col-md-3">
					<button class="btn btn-default service-table-button yellow-bg" style="width: 100% !important">
						<span class="round-number">2</span>
						PHÍ SHIP TRUNG QUỐC
					</button>
				</div>

				<div class="col-md-3">
					<button class="btn btn-default service-table-button yellow-bg" style="width: 100% !important">
						<span class="round-number">3</span>
						PHÍ GIAO DỊCH
					</button>
				</div>

				<div class="col-md-3">
					<button class="btn btn-default service-table-button yellow-bg" style="width: 100% !important">
						<span class="round-number">4</span>
						CƯỚC VẬN CHUYỂN
					</button>
				</div>
			</div>
			<br/>
			<p>
				<span class="yellow">
					Số tiền phải thanh toán:
				</span>
				<span class="round-number">1</span>
				<span class="red">+</span>
				<span class="round-number">2</span>
				<span class="red">+</span>
				<span class="round-number">3</span>
				<span class="red">+</span>
				<span class="round-number">4</span>
			</p>

			<p>
				<span class="round-number">1</span>
				<span class="red">TIỀN HÀNG TRÊN WEB:</span> <span class="white">Giá sản phẩm niêm yết trên website</span>
			</p>
			<p>
				<span class="round-number">2</span>
				<span class="red">PHÍ SHIP TRUNG QUỐC:</span> <span class="white">Phí vận chuyển từ người bán tới kho hàng tại Trung quốc</span>
			</p>
			<p>
				<span class="round-number">3</span>
				<span class="red">PHÍ GIAO DỊCH:</span> <span class="white">Áp dụng theo bảng phí dưới đây</span>
				<table class="service-table" border="1">
					<tr>
						<td>Cấp độ</td>
						<td>Tổng tiền giao dịch tích lũy</td>
						<td>10%</td>
					</tr>
					<tr>
						<td>VIP1</td>
						<td>Từ 0 đến 300 Triệu vnđ</td>
						<td>7%</td>
					</tr>
					<tr>
						<td>VIP2</td>
						<td>Từ 300 triệu đến 1 tỷ vnđ</td>
						<td></td>
					</tr>
					<tr>
						<td>VIP3</td>
						<td>Trên 1 tỷ vnđ</td>
						<td>5%</td>
					</tr>
				</table>
			</p>

			<p>
				<span class="round-number">4</span>
				<span class="red">CƯỚC VẬN CHUYỂN CÂN NẶNG:</span> <span class="white">Áp dụng theo mức phí dưới đây</span>
			</p>

			<p class="white">
				Từ Quảng Châu về Hà Nội: 27.000 vnđ/ kg
			</p>
			<p class="white">
				Từ Quảng Châu về Sài Gòn, Đà Nẵng: 37.000 vnđ/ kg
			</p>

			<div class="dotted-line">&nbsp;</div>

			<p class="white">
				(*) Chúng tôi chỉ tính phí dịch vụ cho hàng về đến kho tại Hà Nội hoặc TP.HCM, khách hàng sẽ đến kho lấy hoặc sẽ tự thanh toán thêm tiền vận chuyển từ kho về nhà.
			</p>
		</div>
	</div>
	<div class="col-md-3" style="padding-left: 10px">
		<div class="brown-bg padding-15">
			<div class="white">
				BÁO GIÁ CÁC DỊCH VỤ KHÁC
			</div>
			<br/>
			<ul class="white" style="padding-left: 15px">
				<li>
					VẬN CHUYỂN TỪ VIỆT NAM SANG TRUNG QUỐC
				</li>
				<li>
					THÔNG QUAN, NHẬP KHẨU CHÍNH NGHẠCH
				</li>
				<li>
					PHIÊN DỊCH, DẪN KHÁCH SANG TRUNG QUỐC ĐÁNH HÀNG
				</li>
				<li>
					VẬN CHUYỂN HÀNG TỪ HÀ NỘI VÀO SÀI GÒN
				</li>
			</ul>
			<div>
				<h3 class="yellow">
					MỜI QUÝ KHÁCH LIÊN HỆ
				</h3>
				<h2 class="yellow">
					0937 690 559
				</h2>
				<h3 class="yellow">
					ĐỂ CÓ GIÁ TỐT NHẤT!
				</h3>
			</div>
		</div>
	</div>
</div>

@endsection
