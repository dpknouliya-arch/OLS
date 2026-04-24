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
        $startReading = false;

        // ============================
        // LOOP THROUGH ROWS
        // ============================
        foreach ($rows as $index => $row) {
            $rowString = strtolower(implode(' ', array_filter($row)));

                // Detect header row
                if (!$startReading && strpos($rowString, 'name on jersey') !== false && strpos($rowString, 'qty') !== false) {
                    $startReading = true;
                    continue; // skip header itself
                }

                // Start collecting only after header is found
                if (!$startReading) {
                    continue;
                }

                // Stop at TOTAL row
                if (strpos($rowString, 'total order') !== false) {
                    break;
                }

                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Re-index array
                $row = array_values($row);

                $Data[] = $row;
        }

        return [
            'data' => $Data
        ];
}

?>