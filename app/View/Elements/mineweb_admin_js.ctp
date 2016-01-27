<script type="text/javascript">
	function confirmDel(url) {
	  if (confirm("<?= $Lang->get('CONFIRM_WANT_DELETE') ?>"))
	    window.location.href=''+url+'';
	  else
	    return false;
	}
</script>
