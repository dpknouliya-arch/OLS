
    // View Order Form 
$(document).on('click', '.ViewOrderForm', function (event) {
    event.preventDefault();
    let orderForm = $(this).data('orderform'); // full URL to .xlsx
    let  viewerUrl = '' ;
    let isExcel = /\.(xlsx|xls)$/i.test(orderForm);
    if(isExcel){
        viewerUrl = "https://view.officeapps.live.com/op/embed.aspx?src=" + encodeURIComponent(orderForm);
    }else{
         viewerUrl = ""+ orderForm +"";  
    }
  

    let iframe = `<iframe src="${viewerUrl}" width="100%" height="600px" frameborder="0"></iframe>`;

    $('#orderFormModal').modal('show');
    $('#orderFormModal').find('.modal-body').html(iframe);
});


 