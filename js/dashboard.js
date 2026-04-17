
function GetGraphDetails() {
    $.ajax({
        url: "./ajax/dashboard/graph_details.php",   // or Laravel route
        type: "POST",
        dataType: 'Json',        // GET or POST
        data: { type: btoa("Spend") },
        success: function (response) {

            // const xValues = ["2021", "2022", "2023", "2024", "2025"];
            // const yValues = [18, 10, 6, 19, 3];        // actual data

            const xValues = response['year'];
            const yValues = response['spends'];
            const maxValues = response['max_array'];
            const max_value = response['max_value'];

            const xValuesItems = response['items_year'];
            const yValuesItems = response['items'];
            const maxValuesItems = response['maxItemsArr'];
            const max_value_items = response['max_items'];


            Showgraphs(xValues, yValues, maxValues, max_value, 'Money Spent (in $)', 'myChart');
            Showgraphs(xValuesItems, yValuesItems, maxValuesItems, max_value_items, 'Items bought', 'myChart2') ;

        },
        error: function (xhr, status, error) {
            console.log("Error:", error);
        }
    });
}


function GetDashBoardData(){
   let year_val = $('#filter_year').val(); 
    $.ajax({
    url: './ajax/dashboard/order_count.php',   // PHP / API URL
    type: 'POST',              // GET or POST
    dataType: 'json',          // expected response type
    data: {
        year: year_val 
    },
    success: function (response) {

         let paid = response['Paid']; 
         let unpaid = response['Unpaid']; 
         let pending = response['Pending'] ;
        // Update count data 
        $.each(response  , function(key , val){
            if(key=='status') return  ;
            if(key=='Paid' || key=='Unpaid' || key=='Pending'){
                $('.' +key).text(val +"%"+key);
            }

            $('.total_count_div').find('.' +key).text(val);
            if(key=='total_invoice'){$('.total_invoice').text(val) ; return }  ;  

        }) 
           
            var paidEnd = paid;
            var unpaidEnd = paid + unpaid;
            var pendingEnd = paid + unpaid + pending;

            $('.donut-chart').css('background', 
             `conic-gradient(
                #8ee000 0% ${paidEnd}%,
                #f18b1a ${paidEnd}% ${unpaidEnd}%,
                #1E88E6 ${unpaidEnd}% ${pendingEnd}%
                )`
            );
 
         

                
           
    },
    error: function (xhr, status, error) {
        console.error(error);
    }
});

}


function Showgraphs(xValues, yValues, maxValues, max_value, heading, id) {
    new Chart("" + id + "", {
        type: "bar",
        data: {
            labels: xValues,
            datasets: [
                {
                    label: "",
                    data: maxValues,
                    backgroundColor: "#c7c7c74a",
                    barThickness: 28,
                    borderRadius: 10,
                    borderSkipped: false
                },
                {
                    label: heading,
                    data: yValues,
                    backgroundColor: "#0b74f0",
                    barThickness: 28,
                    borderRadius: 10,
                    borderSkipped: false
                }
            ]
        },
        options: {
            plugins: {
                legend: { display: false }, title: {
                    display: true,
                    text: heading,
                    font: {
                        size: 14
                    }
                }
            },
            scales: {
                x: { stacked: true },
                y: { beginAtZero: true, maximum: max_value }
            }
        }
    });
}

function GetRecentOrderList(){
   $.ajax({
    url : "./ajax/dashboard/get_recent_order_list.php" , 
    method : "POST" ,
    dataType : "Json" , 
    data:{},
    success : function(response){
         $('.order-table').find('tbody').html(response['html']);
    } , 
    error : function(xhr ,error , status){
        alert("Something went wrong with recent orders"); 
    }
   }) ;

}


$(document).ready(function(){
    // Priority 1: Order counts (most important)
    GetDashBoardData();

    // Priority 2: Graphs (can load after counts)
    setTimeout(function() {
        GetGraphDetails();
    }, 100);

    // Priority 3: Recent orders (least critical, defer)
    setTimeout(function() {
        GetRecentOrderList();
    }, 200);
});



$(document).on('change' , '#filter_year' , function(){
     GetDashBoardData(); 
})


// Go to details page 
$(document).on('click' ,'.go_to_details' ,function(event){
       event.preventDefault();
       let link = $(this).attr('href');
       let type = $(this).data('status') ;
       let year = $('#filter_year').val(); 
     window.location = link+'&type='+btoa(type)+ '&year='+btoa(year);
});