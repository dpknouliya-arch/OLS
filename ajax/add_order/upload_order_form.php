<?php 
  
    include('../../db.php');
    
   


      if (!empty($_FILES['file']['tmp_name'])) {
          $filePath = $_FILES['file']['tmp_name'];
          $AllExcelData = readExcelData($filePath);
          $excel_data = $AllExcelData['data']  ?? [] ; 

         if(COUNT($excel_data) == 0){
              echo json_encode(['status'=> 503 , 'upload' => false ]) ; 
              exit ; 
         }else{
        

             ob_start();
             include('new_card.php');
             $html = ob_get_clean();

            echo json_encode([
                'status' => 200,
                'upload' => true,
                'html' => $html
            ]);
        exit;
         }
      }



 function readExcelData($filePath)
{
    require_once '../../excel/SimpleXLSX.php';

    if (!\Shuchkin\SimpleXLSX::parse($filePath)) {
        die(\Shuchkin\SimpleXLSX::parseError());
    }

    $xlsx = \Shuchkin\SimpleXLSX::parse($filePath);
    $rows = $xlsx->rows();

    $Data = [];

    // ============================
    // LOOP THROUGH ROWS
    // ============================
    foreach ($rows as $index => $row) {

        // Skip header rows (0,1,2)
        if ($index <= 2) {
            continue;
        }

        // Convert row to string to detect keywords
        $rowString = strtolower(implode(' ', array_filter($row)));

        // Stop at TOTAL row
        if (strpos($rowString, 'total order') !== false) {
            break;
        }

        // Skip completely empty rows
        if (empty(array_filter($row))) {
            continue;
        }

        // Remove index 0 (Player Name)
      

        // Re-index array (important)
        $row = array_values($row);

        $Data[] = $row;
    }

    return [
        'data' => $Data
    ];
}

?>