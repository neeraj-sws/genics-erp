<div class="modal fade" id="modal-default">
	<div class="modal-dialog">
		<div class="modal-content">
			
		</div>
	</div>
</div>
<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?php echo base_url();?>assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="<?php echo base_url();?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="<?php echo base_url();?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- Datepicker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>  
<script src="<?php echo base_url();?>assets/plugins/datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url();?>assets/plugins/datetimepicker/bootstrap-datetimepicker.js"></script>
<!-- overlayScrollbars -->
<script src="<?php echo base_url();?>assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

<!-- AdminLTE App -->

<script src="<?php echo base_url();?>assets/dist/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="<?php echo base_url();?>assets/dist/js/demo.js"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="<?php echo base_url();?>assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
<script src="<?php echo base_url();?>assets/plugins/raphael/raphael.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>
<script src="<?php echo base_url();?>assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo base_url();?>assets/plugins/chart.js/Chart.min.js"></script>
 <script src="<?php echo base_url();?>assets/plugins/select2/js/select2.full.min.js"></script>
 <script src="<?php echo base_url();?>assets/dist/js/bootstrap-tagsinput.js"></script>
 <script src="<?php echo base_url();?>assets/js/custom.js"></script>
 <script src="<?php echo base_url();?>assets/js/pages/<?php echo $page;?>.js"></script>
</body>
</html>

<script>
$( document ).ready(function() {
  // setInterval(function(){ 
  //   getNotification();
  // }, 1000);
    
});

function logout(type){

	if(confirm("Are you sure, You want to Logout ?")){
		var surl = BASE_URL+type+'/login/logout'; 
		window.location = surl;
	}
}

function check_link_view(e){
   var link = $('#link_check').val();
   if(link != ''){ link = link; }else{ link = $(e).data('link'); }
   window.location.href = link;
}

tinymce.init({
  selector:'.tinymcetextarea',
  height: 400,
  menubar: true,
  plugins: [
    'advlist autolink lists link image charmap print preview anchor',
    'searchreplace visualblocks code fullscreen',
    'insertdatetime media table paste code help wordcount image'
  ],
  toolbar: ' undo redo | formatselect | ' +
  'bold italic backcolor | alignleft aligncenter ' +
  'alignright alignjustify | bullist numlist outdent indent | ' +
  'removeformat | image',
  image_title: true,
  /* enable automatic uploads of images represented by blob or data URIs*/
  automatic_uploads: true,
  file_picker_types: 'image',
  /* and here's our custom image picker*/
  file_picker_callback: function (cb, value, meta) {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
  input.onchange = function () {
      var file = this.files[0];

      var reader = new FileReader();
      reader.onload = function () {
        var id = 'blobid' + (new Date()).getTime();
        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
        var base64 = reader.result.split(',')[1];
        var blobInfo = blobCache.create(id, file, base64);
        blobCache.add(blobInfo);

        /* call the callback and populate the Title field with the file name */
        cb(blobInfo.blobUri(), { title: file.name });
      };
      reader.readAsDataURL(file);
    };

    input.click();
  },
  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
  setup: function (editor){
      editor.on('change', function () {
          tinymce.triggerSave();
      });
  },
});

tinymce.init({
    selector:'.tinymceshorttext',
    height: 300,
    menubar: false,
    plugins: [
      'advlist autolink lists link charmap print preview anchor',
      'searchreplace visualblocks code fullscreen',
      'insertdatetime media table paste code help wordcount'
    ],
    toolbar: ' undo redo |' +
    'bold italic backcolor | alignleft aligncenter ' +
    'alignright alignjustify | bullist numlist | ',
    branding: false,
    setup: function (editor){
      editor.on('change', function () {
          tinymce.triggerSave();
      });
    },
}); 


function getNotification(){  
   $.ajax({
    url : BASE_URL+"admin/admin/get_notification",
    method:"POST",
    dataType:"json",
    success:function(res){
      $('.notification-count').show();
      if(res.count==0){
        $('.notification-count').hide();
      }else{
        $('.notification-count').html(res.count);
      }      
    }
  });
}


</script>