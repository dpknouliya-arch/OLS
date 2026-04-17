function GetAllNotification(){
      $.ajax({
    url: './ajax/dashboard/notification.php',
    type: 'POST',

    data : {is_notification : true} ,
    dataType: 'json', // change if needed (html, text, etc.)
    success: function (response) {
         console.log("notification response" , response); 

        $('.notification_dropdown_list').html(response.html);

         // 2️⃣ Update unread count badge
            if (response.unread_count > 0) {
                $('.notificationBadge').text(response.unread_count).show();
            } else {
                $('.notificationBadge').hide();
            }
    },
    error: function (xhr, status, error) {
        console.error(error);
    }
});
 
}

function GetCheckboxNotification() {
    let NotificationIds = [];

    // get checked checkboxes
    let checked = $('.notifyCheck:checked');

    if (checked.length > 0) {
        // if any checkbox is checked → get only checked values
        checked.each(function () {
            NotificationIds.push($(this).val());
        });
    } else {
        // if none checked → get ALL checkbox values
        $('.notifyCheck').each(function () {
            NotificationIds.push($(this).val());
        });
    }

    return NotificationIds;
}


function ActionNotification(type){
    let checkedVal  =  GetCheckboxNotification(); 

    $.ajax({
    url: './ajax/dashboard/notification.php',
    type: 'POST',
    dataType :'json' ,
    data : {
        action_notification : true , 
        checked_val : checkedVal , 
        type  : type 
    } ,
    dataType: 'html', // change if needed (html, text, etc.)
    success: function (response) {
        console.log(response);
        GetAllNotification();
    },
    error: function (xhr, status, error) {
        console.error(error);
    }
});
}

function ActionGetNotificationItemsCount(){
        GetAllNotification(isCount=true);  
}

//======== Notification Action ====================
$('.notification-menu').on('click', '.notification_action', function (e) {
    e.preventDefault();
   let type = $(this).data('type'); 
   ActionNotification(type); 
 
});



// 
$(document).on('click' ,'.dropdown_notification_btn' ,function(){
  GetAllNotification();
});

$(document).ready(function(){
    GetAllNotification();
})