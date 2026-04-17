<?php 
  $locker_url =   "https://locker-test.jog-joinourgame.com/" ;  

   if(!empty($dt)):
	foreach($dt  as $key=> $value){
       ?> 
        <tr>
            <td><?= $key+1 ?></td>
          <td class=""> <?=$value['customer_name'] ?? ''?></td>
		  <td><?= $value['estimate_number']  ?? ''?></td>
          <td class="<?= $value['match_ols'] == 0 ? 'source-direct' : 'source-ols' ?> " ><?= $value['match_ols'] == 0 ? 'Direct' : 'OLS' ?></td>
		  <td><?=$value['product_name'] ?? ''?></td>
		  <td><?= $value['order_name'] ?? ''?></td>
		  <td><?= $value['po_number'] ?? ''?></td> 
		  <td><?= $value['sales_person'] ?? ''?></td>
		  <td><?= $value['order_date'] ?? ''?></td>
		  <td><?= $value['main_ex_code'] ?? ''?></td>
		  <td><?= $value['total_items'] ?? ''?></td>
		  <td><?= $value['total_qty'] ?? 0?></td>
		  <td><?= isset($value['sales_status']) == 1  ? "Pending"  : '<span class="status completed">Completed</span>' ?></td>
		  <td>
			  <a  class="icon orderForm ViewOrderForm"  data-orderform = "<?=$locker_url.'files/'.$value['main_ex_code'].'/'. $value['order_main_file_name'] ?>"  >
                <figure class="my-auto"><img src="assets/images/icons/editEyeBlue.png" alt="orderFormEdit"></figure>
              </a>
		  </td>
		
		  <td>
            <?php 
               if($value['invoice_status'] == 'Paid'){
			   ?>
                 <a href="<?= $value['invoice_link'] ?>" target="_blank" class="icon">  
                     <figure class="my-auto"><img src="assets/images/icons/invoiceEdit.png" alt="invoice"></figure>
                 </a>
               <?php 
			   }else{
				 echo  $value['invoice_status'] ;
			   }

            ?>

           
          </td>
		  <td> 
			<a href="https://locker.jog-joinourgame.com/view/?id=<?= base64_encode($value['order_main_id']) ?>" target="_blank" class="btn-view"   >View</a>
		 </td>
		</tr> 



      <?php 
	}
   else:
     ?>
       <tr> 
		 <td class="text-center" colspan="16"> No record found</td>
	   </tr>
	 <?php 
  endif; 
?>