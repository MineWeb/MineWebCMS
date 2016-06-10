function string_to_slug(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim
  str = str.toLowerCase();

  // remove accents, swap ñ for n, etc
  var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
  var to   = "aaaaeeeeiiiioooouuuunc------";
  for (var i=0, l=from.length ; i<l ; i++) {
    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
  }

  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
    .replace(/\s+/g, '-') // collapse whitespace and replace by -
    .replace(/-+/g, '-'); // collapse dashes

  return str;
}

$("#generate_slug").click(function( event ) {
    event.preventDefault();
    var $form = $( this );
    var title = $('body').find("input[name='title']").val();
	$('#slug').val(string_to_slug(title));
    return false;
});

$(function () {
  $('table.dataTable').each(function(e) {

    if(!$.fn.dataTable.isDataTable(this)) {
      $(this).DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false,
        'searching': true
      });
    }

  });
});

// Btn browse

$(document).on('change', '.btn-file :file', function() {
  var input = $(this),
  numFiles = input.get(0).files ? input.get(0).files.length : 1,
  label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
  input.trigger('fileselect', [numFiles, label]);
});

$(document).ready( function() {
  $('.btn-file :file').on('fileselect', function(event, numFiles, label) {

    var input = $(this).parents('.input-group').find(':text'),
        log = numFiles > 1 ? numFiles + ' files selected' : label;

    if( input.length ) {
        input.val(log);
    } else {
        if(log) {
          console.log('File : ', log);
          $('span.browse').html(log);
        }
    }

  });

  $('.choose-from-gallery-img').on('click', function(e) {
    e.preventDefault();

    var path = $(this).attr('data-path');
    var filename = $(this).attr('data-filename');
    var basename = $(this).attr('data-basename');

    $('#image_preview').find('.thumbnail img').attr('src', path);
    $('#img-name').html(filename);

    if($('input[name="img-uploaded"]').length == 0) {
      $('#image_preview').append('<input type="hidden" name="img-uploaded" value="'+basename+'">');
    } else {
      $('input[name="img-uploaded"]').val(basename);
    }

    $('#galery').modal('hide');
  });

});

$('form #delete_upload_file').on('click', function(e) {
  e.preventDefault();

  $('form').find('input[name="image"]').val('');
  $('#image_preview').find('.thumbnail img').attr('src', '#');
  $('#image_preview .thumbnail .caption h5').html('');
})

// A change sélection de fichier
$('form').find('input[name="image"]').on('change', function (e) {
    var files = $(this)[0].files;

    if (files.length > 0) {
        // On part du principe qu'il n'y qu'un seul fichier
        // étant donné que l'on a pas renseigné l'attribut "multiple"
        var file = files[0],
            $image_preview = $('#image_preview');

        // Ici on injecte les informations recoltées sur le fichier pour l'utilisateur
        $image_preview.find('.thumbnail').removeClass('hidden');
        $image_preview.find('img').attr('src', window.URL.createObjectURL(file));
        $image_preview.find('h5').html(file.name);
        $image_preview.find('.caption p:first').html(file.size +' bytes');
    }
});

var initBtnChooseUploadedFiles = false;
$('#choose_form_uploaded_files').click(function(e) {
  e.preventDefault();

  if(!initBtnChooseUploadedFiles) {
    var modal = '';
    modal += '';

    $('body').append(modal);
  }



});
