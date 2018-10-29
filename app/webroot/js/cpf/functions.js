jQuery(document).ready(function(){
	// Bucket Data Table
    jQuery('#cpfTable').DataTable({'responsive': true, 'pageLength':25});
	
    // DataTables Length to Select2
    jQuery('div.dataTables_length select').removeClass('form-control input-sm');
    jQuery('div.dataTables_length select').css({width: '60px'});
    jQuery('div.dataTables_length select').select2({minimumResultsForSearch: -1});
    
	//Cpf File Upload
	Dropzone.options.cpfFile={
		init:function(){
    		this.on("processing",function(file,xhr,formData){
    			this.options.url = '/cpf/addfile';
    			jQuery(this.element).children('.progress').show();
    		})
    	},
    	uploadprogress:function(file,progress,bytesSent){
    		jQuery(this.element).children('.progress').children('.progress-bar').css('width',progress+'%');
    		if(progress == 100){
    			jQuery('.dz-spin').fadeIn('slow');
    			jQuery('.progress').fadeOut();
    		}
    	},
    	complete:function(){
    		location.reload(true);
    	},
    	url:'empty',
    	maxFilesize:'5',
    	autoDiscover: false,
    	createImageThumbnails: false,
    	previewTemplate : '<div style="display:none"></div>',
    	headers: {'x-keyStone-nonce': nonce}
	}
});