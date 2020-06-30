
<div style="width: 70%; margin: auto;" id="dlop">	
	<div class="row">
		<?php 

		//$start = array(100,200,300,500,1000,5000);
		$numbers = array(100,200,300,400,500,1000,2000,3000,4000,5000); ?>

		<div class="col-md-6">
			<div class="form-group has-feedback">
				<label>Begin of Rows </label>
				<input type="text" name="fstart" id="fstart" class="form-control" value="0">

			</div>

		</div>
		
		<div class="col-md-6">	
	
			<div class="form-group has-feedback">
				<label> Total Of Rows </label>
				<select id="flimit" name="flimit" class="form-control">
					@foreach($numbers as $nums)
						<option value="{{ $nums}}">{{ $nums}}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>	


	<div class="form-group has-feedback">
	<label> Download Type</label>
	  <select class="form-control" id="ftype" name="ftype">
	  		<option value="excel"> Excel </option>
	  		<option value="word"> Word </option>
	  		<option value="csv"> CSV </option>
	  		<option value="print"> Print </option>
	  		<option value="pdf"> PDF </option>
	  </select>
	</div>
	<div class="form-group has-feedback">
		<button type="button" class="btn btn-primary  btn-block dodl"><i class="fa fa-cloud-download"></i> {{ Lang::get('core.btn_download') }}</button>
	</div>


</div>	
<script type="text/javascript">
	$(function(){
		$('.dodl').click(function(){
			var start = $('#fstart').val();
			var limit = $('#flimit').val();	
			var ftype = $('#ftype').val();	
			url = '{{ $pageUrl }}/export/'+ ftype +'?{{ $return  }}' + '&fstart='+start+'&flimit='+limit;
			if(ftype =='print')
			{
				ajaxPopupStatic(url);
			} else {
				window.location.href = url;	
			}	
			//alert(url);
				
		})
	})
</script>		