@extends('Landing::share.base')
@section('content')

<div class="row-fluid">
	@include('Landing::share.side')
	<div class="span9">
		<div class="b-bar-home uppercase">
	        <h1>Liên hệ</h1>
	    </div>

		<div class="border-content-home padding-ct">
            <div style="margin: 5px; color: #000;">
                <div style="text-align: justify; line-height: 19px; margin-bottom: 8px;">
                    <div><span style="line-height: 20.8px;">Văn phòng: Số 8 ngõ 162 Nguyễn Văn Cừ, Bồ Đề, Long Biên, HN&nbsp;</span>
                        <br style="line-height: 20.8px;">
                        <span style="line-height: 20.8px;">Mr. Quân : 0963.226.326 Zalo,Facebook&nbsp;</span>
                        <br style="line-height: 20.8px;">
                        <span style="line-height: 20.8px;">​</span><span style="line-height: 20.8px;">Email: ordertaunhanh@gmail.com</span></div>
                </div>
                <div style="height: 15px;"></div>
                <form method="post" id="check_form" action="http://test.multitemplate.com/ordertaunhanh/contact/send">
                    <table class="guimail">
                        <tbody>
                            <tr>
                                <td>Tên: </td>
                                <td>
                                    <input id="input" name="name" class="validate[required]" type="text" placeholder="Nhập tên" required="">
                                </td>
                            </tr>
                            <tr>
                                <td>Số điện thoại: </td>
                                <td>
                                    <input id="input" type="text" class="validate[required,custom[telephone]]" name="phone" placeholder="Nhập số điện thoại" required="">
                                </td>
                            </tr>
                            <tr>
                                <td>Email: </td>
                                <td>
                                    <input id="input" type="email" class="validate[required,custom[email]]" name="email" placeholder="Nhập email" required="">
                                </td>
                            </tr>
                            <tr>
                                <td>Tiêu đề: </td>
                                <td>
                                    <input id="input" type="text" class="validate[required]" name="title" placeholder="Nhập tiêu đề" required="">
                                </td>
                            </tr>
                            <tr>
                                <td>Nội dung: </td>
                                <td>
                                    <textarea class="span8" style="width: 345px; height: 100px;" name="content" cols="50" rows="10"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input type="submit" value="Send" class="btn btn-primary">&nbsp;
                                    <input type="reset" value="Reset" class="btn">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
                <div class="googlemap">
                    <iframe allowfullscreen="" frameborder="0" height="450" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29793.996973529247!2d105.81945410109321!3d21.02269575409389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab9bd9861ca1%3A0xe7887f7b72ca17a9!2zSMOgIE7hu5lpLCBIb8OgbiBLaeG6v20sIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1456991419208" style="border:0" width="600"></iframe>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
