<!-- Confirm Box -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Are You Sure?</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="confirm-modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" data-dismiss="modal">No</button>
        <button type="button" id="confirm-btn" class="btn btn-warning">Yes</button>
      </div>
    </div>
  </div>
</div>
<!-- Alert Box -->
<div class="modal fade" id="alertModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alertModalLabel">Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="alert-modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">Okay</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
var successFn;
var failureFn;
function confirmBox(confirmBoxText, success ) {
  $('#confirmModal').modal('show');
    $('#confirm-modal-body').html(confirmBoxText);
    successFn = success;
}
$('#confirm-btn').on('click', function() {
    successFn();
});
function alertBox(alertBoxText) {
    $('#confirmModal').modal('hide');
    $('#alertModal').modal('show');
    $('#alert-modal-body').html(alertBoxText);
}



</script>