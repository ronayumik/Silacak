<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">

</script>
<div class="row">
	<div class="col-md-12">
		<!-- <div class="note note-warning">
			<p><?php echo $this->lang->line('module_description'); ?></p>
		</div> -->
		<div id="master-page">
			<div class="table-page portlet light bg-inverse">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-user font-green-seagreen"></i>
								<span class="caption-subject bold font-green-seagreen uppercase">
								<!-- TODO LANGLINE-->
								Data Scopus
								</span>
							</div>
							<div class="actions filter-control">
								<div class="btn-group main-control"></div>
							</div>
						</div>
						<div class="portlet-body">
							<div class="masterpage-filter form-inline" >
								<a href="<?php echo base_url()?>/dev/tambahscopus" id="btn-download" class="btn green-seagreen margin-top-10">Tambah Data Scopus</a>
							</div>
							<form method="post" action="javascript:void(null);" class="form-master">
								<table class="table-master table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>No</th>
											<th>
												<!-- TODO LANGLINE -->
												Nama File Scopus
											</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$i = 1;
											foreach($result as $r){


												if( $r!= '.' && $r != '..'){
												?>
											<tr>
												<td>
													<?php echo $i; $i++;?>
												</td>
												<td>
													<?php echo $r?>
												</td>
											</tr>
										<?php } } ?>
									</tbody>
								</table>
							</form>
						</div>
					</div>
		</div>
	</div>
</div>
