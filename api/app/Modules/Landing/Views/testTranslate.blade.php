@extends('Landing::share.base')
@section('content')
<div class="container" style="margin: 40px 40px">
	<form method="POST" target="_blank" action="">
		<div class="form-group">
			<label for="keyword">Tên sản phẩm</label>
			<input type="text" class="form-control" name="keyword" id="keyword" placeholder="Ví dụ: giày nam">
		</div>
		<button type="submit" class="btn btn-primary">Tìm kiếm</button>
	</form>
</div>
@endsection
