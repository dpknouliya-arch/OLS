(function($) {
  'use strict';
  if ($("#timepicker-example").length) {
    $('#timepicker-example').datetimepicker({
      format: 'LT'
    });
  }
  if ($(".color-picker").length) {
    $('.color-picker').asColorPicker();
  }
  
   if ($("#datepicker-popup").length) {
    $('#datepicker-popup').datepicker({
      enableOnReadonly: true,
      todayHighlight: true,
	  format: 'yyyy-mm-dd'
     });
  }
  if ($("#chipdate-popup").length) {
    $('#chipdate-popup').datepicker({
      enableOnReadonly: true,
      todayHighlight: true,
	  format: 'yyyy-mm-dd'
    });
  }
  if ($("#duedate-popup").length) {
    $('#duedate-popup').datepicker({
      enableOnReadonly: true,
      todayHighlight: true,
	  format: 'yyyy-mm-dd'
    });
  }
  if ($("#inline-datepicker").length) {
    $('#inline-datepicker').datepicker({
      enableOnReadonly: true,
      todayHighlight: true,
    });
  }
  if ($(".datepicker-autoclose").length) {
    $('.datepicker-autoclose').datepicker({
      autoclose: true
    });
  }
  if ($('input[name="date-range"]').length) {
    $('input[name="date-range"]').daterangepicker();
  }
  if ($('input[name="date-time-range"]').length) {
    $('input[name="date-time-range"]').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY h:mm A'
      }
    });
  }
})(jQuery);